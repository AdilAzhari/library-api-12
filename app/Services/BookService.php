<?php

namespace App\Services;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\DTO\BorrowBookDTO;
use App\DTO\ReserveBookDTO;
use App\Enum\BookStatus;
use App\Events\BookBorrowed;
use App\Exceptions\BookNotAvailableException;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookService
{
    public function getAllBooks(array $filters, int $perPage): LengthAwarePaginator
    {
        if (! empty($filters['search'])) {
            return $this->searchWithTypesense($filters, $perPage);
        }

        // Fall back to regular query when there's no search term
        return $this->regularDatabaseQuery($filters, $perPage);
    }

    private function searchWithTypesense(array $filters, int $perPage): LengthAwarePaginator
    {
        $searchQuery = Book::search($filters['search'])
            ->query(function ($query) {
                $query->with('genre', 'reviews', 'activeReservation')
                    ->withCount('reviews')
                    ->withAvg('reviews', 'rating');
            })
            ->options([
                'query_by' => 'title,author,description,ISBN,genre',
                'sort_by' => $this->getTypesenseSortField($filters['sort'] ?? null),
                'filter_by' => $this->buildFilterString($filters),
                'cache' => $this->shouldCacheSearch($filters), // Cache only for common searches
                'per_page' => $perPage,
            ]);

        return Cache::remember(
            $this->getSearchCacheKey($filters, $perPage),
            now()->addMinutes(30), // Cache for 30 minutes
            fn () => $searchQuery->paginate($perPage)
        );
    }

    private function regularDatabaseQuery(array $filters, int $perPage): LengthAwarePaginator
    {
        return Book::with('genre', 'reviews', 'activeReservation')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($filters['genre'] ?? null, fn ($q, $genre) => $q->where('genre_id', $genre))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['sort'] ?? null, fn ($q, $sort) => $this->applySorting($q, $sort))
            ->latest()
            ->paginate($perPage);
    }

    private function fallbackDatabaseSearch(array $filters, int $perPage): LengthAwarePaginator
    {
        return Book::with('genre', 'reviews', 'activeReservation')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%")
                        ->orWhere('ISBN', 'like', "%$search%");
                });
            })
            ->when($filters['genre'] ?? null, fn ($q, $genre) => $q->where('genre_id', $genre))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['sort'] ?? null, fn ($q, $sort) => $this->applySorting($q, $sort))
            ->latest()
            ->paginate($perPage);
    }

    private function buildFilterString(array $filters): string
    {
        $filters = array_filter([
            'status' => $filters['status'] ?? null,
            'genre' => $filters['genre'] ? Genre::find($filters['genre'])?->name : null,
        ]);

        return collect($filters)
            ->map(fn ($value, $key) => "$key:=$value")
            ->join(' && ');
    }

    private function getTypesenseSortField(?string $sort): string
    {
        return match ($sort) {
            'title_asc' => 'title:asc',
            'title_desc' => 'title:desc',
            'author_asc' => 'author:asc',
            'author_desc' => 'author:desc',
            'year_asc' => 'publication_year:asc',
            default => 'publication_year:desc',
        };
    }

    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            'author_asc' => $query->orderBy('author', 'asc'),
            'author_desc' => $query->orderBy('author', 'desc'),
            'year_asc' => $query->orderBy('publication_year', 'asc'),
            'year_desc' => $query->orderBy('publication_year', 'desc'),
            default => $query->latest(),
        };
    }

    private function shouldCacheSearch(array $filters): bool
    {
        // Only cache searches without filters for simplicity
        return empty($filters['genre']) && empty($filters['status']);
    }

    private function getSearchCacheKey(array $filters, int $perPage): string
    {
        return 'books_search_'.md5(serialize($filters)).'_'.$perPage;
    }

    public function createBook(BookCreateDTO $dto): Book
    {
        return Book::query()->create($dto->toArray());
    }

    public function updateBook(Book $book, BookUpdateDTO $dto): Book
    {
        $book->update($dto->toArray());

        return $book;
    }

    public function getRecommendedBooks(Book $book, int $limit = 4): Collection
    {
        return Book::query()->where('genre_id', $book->genre_id)
            ->where('id', '!=', $book->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function reserveBook(ReserveBookDTO $dto): Reservation
    {
        return DB::transaction(function () use ($dto) {
            $book = Book::with(['activeBorrow', 'activeReservation', 'genre'])
                ->lockForUpdate()
                ->findOrFail($dto->bookId);

            // Check if user already has an active reservation for this book
            $existingReservation = Reservation::activeForUser($dto->userId, $dto->bookId)->first();

            if ($existingReservation) {
                return $existingReservation; // Return existing reservation if it exists
            }
            // Check if user already has an active borrow for this book
            $existingBorrow = Borrow::where('book_id', $book->id)
                ->where('user_id', $dto->userId)
                ->whereNull('returned_at')
                ->first();

            if ($existingBorrow) {
                throw new BookNotAvailableException('You have already borrowed this book and cannot reserve it.');
            }

            // Check book availability
            if (! $book->canBeReserved()) {
                throw new BookNotAvailableException('This book cannot be reserved at this time');
            }

            $reservation = Reservation::create([
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
                'reserved_at' => now(),
                'expires_at' => $dto->expiresAt ?? now()->addDays(7),
            ]);

            // Update book status if it's available
            if ($book->status === BookStatus::STATUS_AVAILABLE->value) {
                $book->update(['status' => BookStatus::STATUS_RESERVED->value]);
            }

            return $reservation;
        });
    }

    /**
     * @throws Exception
     */
    /**
     * Borrow a book by fulfilling a reservation
     *
     * @throws Exception
     */
    public function borrowBook(BorrowBookDTO $dto): Borrow
    {
        return DB::transaction(function () use ($dto) {
            $book = Book::with(['activeBorrow', 'activeReservation'])
                ->lockForUpdate()
                ->findOrFail($dto->bookId);

            $user = User::findOrFail($dto->userId);

            // Check if user already has an active borrow for this book
            $existingBorrow = Borrow::where('book_id', $book->id)
                ->where('user_id', $dto->userId)
                ->whereNull('returned_at')
                ->first();

            if ($existingBorrow) {
                throw new BookNotAvailableException('You have already borrowed this book.');
            }

            // Check for active reservation by this user
            $reservation = $book->activeReservation()
                ->where('user_id', $dto->userId)
                ->first();

            // Allow borrowing if:
            // 1. Book is available OR
            // 2. User has a reservation OR
            // 3. Book is reserved but reservation is expired
            if (! $book->canBeBorrowed() && ! $reservation) {
                throw new BookNotAvailableException('This book is not available for borrowing');
            }

            // Create borrowing record
            $borrowing = Borrow::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'borrowed_at' => now(),
                'due_date' => $dto->dueDate ?? now()->addDays(config('library.default_borrow_days', 14)),
                'renewal_count' => 0,
            ]);

            // Fulfill the reservation if it exists
            if ($reservation) {
                $reservation->update([
                    'fulfilled_by_borrow_id' => $borrowing->id,
                    'expires_at' => now(), // Mark as fulfilled
                ]);
            }

            // Update book status
            $book->update([
                'status' => BookStatus::STATUS_BORROWED->value,
            ]);

            event(new BookBorrowed($borrowing));

            return $borrowing;
        });
    }
}
