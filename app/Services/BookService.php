<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Log;

final class BookService
{
    public function getAllBooks(array $filters, int $perPage): LengthAwarePaginator
    {
        Log::info('BookService::getAllBooks - Starting book retrieval', [
            'filters' => $filters,
            'per_page' => $perPage,
            'use_typesense' => $this->shouldUseTypesense($filters),
        ]);
        try {
            // Use Typesense for search queries or when advanced filters are applied
            if ($this->shouldUseTypesense($filters)) {
                $result = $this->searchWithTypesense($filters, $perPage);
            } else {
                // Fall back to regular query for simple filtering
                $result = $this->regularDatabaseQuery($filters, $perPage);
            }

            Log::info('BookService::getAllBooks - Books retrieved successfully', [
                'total' => $result->total(),
                'count' => $result->count(),
                'per_page' => $perPage,
                'search_method' => $this->shouldUseTypesense($filters) ? 'typesense' : 'database',
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('BookService::getAllBooks - Error retrieving books', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $filters,
                'per_page' => $perPage,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function createBook(BookCreateDTO $dto): Book
    {
        Log::info('BookService::createBook - Starting book creation', [
            'data' => $dto->toArray(),
        ]);

        try {
            $book = Book::query()->create($dto->toArray());

            Log::info('BookService::createBook - Book created successfully', [
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
            ]);

            return $book;
        } catch (Exception $e) {
            Log::error('BookService::createBook - Error creating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $dto->toArray(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function updateBook(Book $book, BookUpdateDTO $dto): Book
    {
        Log::info('BookService::updateBook - Starting book update', [
            'book_id' => $book->id,
            'original_title' => $book->title,
            'update_data' => $dto->toArray(),
        ]);

        try {
            $book->update($dto->toArray());

            Log::info('BookService::updateBook - Book updated successfully', [
                'book_id' => $book->id,
                'new_title' => $book->fresh()->title,
            ]);

            return $book;
        } catch (Exception $e) {
            Log::error('BookService::updateBook - Error updating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $book->id,
                'update_data' => $dto->toArray(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    public function deleteBook(int $bookId): bool
    {
        Log::info('BookService::deleteBook - Starting book deletion', [
            'book_id' => $bookId,
        ]);

        try {
            $book = Book::query()->findOrFail($bookId);
            $title = $book->title;

            $result = $book->delete();

            Log::info('BookService::deleteBook - Book deleted successfully', [
                'book_id' => $bookId,
                'title' => $title,
                'result' => $result,
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('BookService::deleteBook - Error deleting book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $bookId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
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
        Log::info('BookService::reserveBook - Starting book reservation', [
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
        ]);

        try {
            return DB::transaction(function () use ($dto) {
                $book = Book::query()->with(['activeBorrow', 'activeReservation', 'genre'])
                    ->lockForUpdate()
                    ->findOrFail($dto->bookId);

                // Check if user already has an active reservation for this book
                $existingReservation = Reservation::query()->activeForUser($dto->userId, $dto->bookId)->first();

                if ($existingReservation) {
                    return $existingReservation; // Return existing reservation if it exists
                }
                // Check if user already has an active borrow for this book
                $existingBorrow = Borrow::query()->where('book_id', $book->id)
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

                $reservation = Reservation::query()->create([
                    'book_id' => $dto->bookId,
                    'user_id' => $dto->userId,
                    'reserved_at' => now(),
                    'expires_at' => $dto->expiresAt ?? now()->addDays(7),
                ]);

                // Update book status if it's available
                if ($book->status === BookStatus::AVAILABLE->value) {
                    $book->update(['status' => BookStatus::RESERVED->value]);
                }

                Log::info('BookService::reserveBook - Book reserved successfully', [
                    'reservation_id' => $reservation->id,
                    'book_id' => $dto->bookId,
                    'user_id' => $dto->userId,
                ]);

                return $reservation;
            });
        } catch (Exception $e) {
            Log::error('BookService::reserveBook - Error reserving book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
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
        Log::info('BookService::borrowBook - Starting book borrowing', [
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
        ]);

        try {
            return DB::transaction(function () use ($dto) {
                $book = Book::query()->with(['activeBorrow', 'activeReservation'])
                    ->lockForUpdate()
                    ->findOrFail($dto->bookId);

                $user = User::query()->findOrFail($dto->userId);

                // Check if user already has an active borrow for this book
                $existingBorrow = Borrow::query()->where('book_id', $book->id)
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
                $borrowing = Borrow::query()->create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'borrowed_at' => now(),
                    'due_date' => $dto->dueDate ?? now()->addDays(config('library.default_borrow_days', 14)),
                    'renewal_count' => 0,
                ]);

                // Fulfill the reservation if it exists
                $reservation?->update([
                    'fulfilled_by_borrow_id' => $borrowing->id,
                    'expires_at' => now(), // Mark as fulfilled
                ]);

                // Update book status
                $book->update([
                    'status' => BookStatus::BORROWED->value,
                ]);

                event(new BookBorrowed($borrowing));

                Log::info('BookService::borrowBook - Book borrowed successfully', [
                    'borrow_id' => $borrowing->id,
                    'book_id' => $dto->bookId,
                    'user_id' => $dto->userId,
                    'due_date' => $borrowing->due_date,
                ]);

                return $borrowing;
            });
        } catch (Exception $e) {
            Log::error('BookService::borrowBook - Error borrowing book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Determine if Typesense should be used based on filters
     */
    private function shouldUseTypesense(array $filters): bool
    {
        return ! empty($filters['search']) ||
               ! empty($filters['author']) ||
               ! empty($filters['isbn']) ||
               ! empty($filters['year_from']) ||
               ! empty($filters['year_to']) ||
               ! empty($filters['min_rating']);
    }

    private function searchWithTypesense(array $filters, int $perPage): LengthAwarePaginator
    {
        // Build search query dynamically based on available filters
        $searchTerm = $this->buildSearchQuery($filters);

        $searchQuery = Book::search($searchTerm)
            ->query(function ($query): void {
                $query->with('genre', 'reviews', 'activeReservation')
                    ->withCount('reviews')
                    ->withAvg('reviews', 'rating');
            })
            ->options([
                'query_by' => 'title,author,description,isbn,genre',
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

    /**
     * Build search query string from filters
     */
    private function buildSearchQuery(array $filters): string
    {
        $searchParts = [];

        // Main search term
        if (! empty($filters['search'])) {
            $searchParts[] = $filters['search'];
        }

        // Author-specific search
        if (! empty($filters['author'])) {
            $searchParts[] = $filters['author'];
        }

        // ISBN-specific search
        if (! empty($filters['isbn'])) {
            $searchParts[] = $filters['isbn'];
        }

        // If no search terms provided, use wildcard to get all results (filtered by filter_by)
        return ! empty($searchParts) ? implode(' ', $searchParts) : '*';
    }

    private function regularDatabaseQuery(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Book::query()->with('genre', 'reviews', 'activeReservation')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Apply basic filters
        $query->when($filters['genre'] ?? null, fn ($q, $genre) => $q->where('genre_id', $genre))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status));

        // Apply advanced filters (fallback for when not using Typesense)
        $query->when($filters['year_from'] ?? null, fn ($q, $year) => $q->where('publication_year', '>=', $year))
            ->when($filters['year_to'] ?? null, fn ($q, $year) => $q->where('publication_year', '<=', $year))
            ->when($filters['min_rating'] ?? null, fn ($q, $rating) => $q->whereHas('reviews', function ($subQ) use ($rating): void {
                $subQ->havingRaw('AVG(rating) >= ?', [$rating]);
            }))
            ->when($filters['author'] ?? null, fn ($q, $author) => $q->where('author', 'LIKE', "%{$author}%"))
            ->when($filters['isbn'] ?? null, fn ($q, $isbn) => $q->where('isbn', 'LIKE', "%{$isbn}%"));

        // Apply sorting
        if (! empty($filters['sort'])) {
            $this->applySorting($query, $filters['sort']);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    private function fallbackDatabaseSearch(array $filters, int $perPage): LengthAwarePaginator
    {
        return Book::query()->with('genre', 'reviews', 'activeReservation')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($filters['search'] ?? null, function ($q, $search): void {
                $q->where(function ($query) use ($search): void {
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
        $conditions = [];

        // Status filter
        if (! empty($filters['status'])) {
            $conditions[] = "status:={$filters['status']}";
        }

        // Genre filter
        if (! empty($filters['genre'])) {
            $genre = Genre::query()->find($filters['genre']);
            if ($genre) {
                $conditions[] = "genre:={$genre->name}";
            }
        }

        // Publication year range filters
        if (! empty($filters['year_from'])) {
            $conditions[] = "publication_year:>={$filters['year_from']}";
        }
        if (! empty($filters['year_to'])) {
            $conditions[] = "publication_year:<={$filters['year_to']}";
        }

        // Minimum rating filter
        if (! empty($filters['min_rating'])) {
            $conditions[] = "average_rating:>={$filters['min_rating']}";
        }

        return implode(' && ', $conditions);
    }

    private function getTypesenseSortField(?string $sort): string
    {
        return match ($sort) {
            'title_asc' => 'title:asc',
            'title_desc' => 'title:desc',
            'author_asc' => 'author:asc',
            'author_desc' => 'author:desc',
            'year_asc' => 'publication_year:asc',
            'year_desc' => 'publication_year:desc',
            'rating_asc' => 'average_rating:asc',
            'rating_desc' => 'average_rating:desc',
            default => '_text_match:desc,publication_year:desc',
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
            'rating_asc' => $query->orderBy('reviews_avg_rating', 'asc'),
            'rating_desc' => $query->orderBy('reviews_avg_rating', 'desc'),
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
}
