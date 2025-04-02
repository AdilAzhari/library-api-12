<?php

namespace App\Services;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\DTO\BorrowBookDTO;
use App\DTO\ReserveBookDTO;
use App\Enum\BookStatus;
use App\Events\BookBorrowed;
use App\Exceptions\BookBorrowedException;
use App\Exceptions\BookNotAvailableException;
use App\Exceptions\ReservationExistsException;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BookService
{
    public function getAllBooks(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Book::with('genre')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%")
                        ->orWhere('isbn', 'like', "%$search%");
                });
            })
            ->when($filters['genre'] ?? null, function ($q, $genre) {
                $q->where('genre_id', $genre);
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($filters['sort'] ?? null, function ($q, $sort) {
                switch ($sort) {
                    case 'title_asc':
                        $q->orderBy('title', 'asc');
                        break;
                    case 'title_desc':
                        $q->orderBy('title', 'desc');
                        break;
                    case 'author_asc':
                        $q->orderBy('author', 'asc');
                        break;
                    case 'author_desc':
                        $q->orderBy('author', 'desc');
                        break;
                    case 'year_asc':
                        $q->orderBy('publication_year', 'asc');
                        break;
                    case 'year_desc':
                        $q->orderBy('publication_year', 'desc');
                        break;
                    default:
                        $q->latest();
                }
            }, function ($q) {
                $q->latest();
            });

        return $query->paginate($perPage);
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

    /**
     * @throws Exception
     */
    public function reserveBook(ReserveBookDTO $dto): Reservation
    {
        $book = Book::query()->lockForUpdate()->findOrFail($dto->bookId);

        if ($book->activeBorrow()->exists()) {
            throw new BookBorrowedException();
        }

        if (!$book->isAvailable()) {
            $reason = match ($book->status) {
                BookStatus::STATUS_BORROWED => 'This book is currently borrowed',
                BookStatus::STATUS_RESERVED => 'This book is currently reserved',
                default => 'This book is not available for reservation',
            };
            throw new BookNotAvailableException($reason);
        }

        if (Reservation::activeForUser($dto->userId, $dto->bookId)->exists()) {
            throw new ReservationExistsException();
        }

        return Reservation::query()->create([
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
            'expires_at' => $dto->expiresAt,
        ]);
    }

    /**
     * @throws Exception
     */
    /**
     * Borrow a book by fulfilling a reservation
     *
     * @param BorrowBookDTO $dto
     * @return Borrow
     * @throws Exception
     */
    public function borrowBook(BorrowBookDTO $dto): Borrow
    {
//        return DB::transaction(function () use ($dto) {
        // Validate book exists and is available
        $book = Book::query()->findOrFail($dto->bookId);
        $user = User::query()->findOrFail($dto->userId);

        // Check for active reservation
        $reservation = Reservation::query()->where('user_id', $dto->userId)
            ->where('book_id', $dto->bookId)
            ->where('expires_at', '>', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->first();

        if (!$reservation) {
            throw new Exception('You need an active reservation to borrow this book');
        }

        // Check book availability again in case of race condition
        if (!$book->is_available) {
            throw new Exception('This book is no longer available for borrowing');
        }

        // Create borrowing record
        $borrowing = Borrow::query()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'borrowed_at' => now(),
            'due_date' => $dto->dueDate ?? now()->addDays(config('library.default_borrow_days', 14)),
            'renewal_count' => 0,
        ]);

        // Fulfill the reservation
        $reservation->update([
            'fulfilled_at' => now(),
            'fulfilled_by_borrow_id' => $borrowing->id
        ]);

        // Update book status
        $book->update([
            'status' => BookStatus::STATUS_BORROWED->value
        ]);

        // Dispatch events
        event(new BookBorrowed($borrowing));
//            event(new ReservationFulfilled($reservation));

        return $borrowing->load('book', 'user', 'reservation');
//        });
    }
}
