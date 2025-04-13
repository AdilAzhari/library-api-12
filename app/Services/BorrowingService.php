<?php

namespace App\Services;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Events\BookBorrowed;
use App\Events\BookOverdue;
use App\Events\BookReturned;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    public function borrowBook(BorrowBookDTO $dto): Borrow
    {
        return DB::transaction(function () use ($dto) {
            $book = Book::findOrFail($dto->bookId);
            $user = User::findOrFail($dto->userId);

            $this->validateBorrow($book, $user);

            $borrow = Borrow::create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'borrowed_at' => $dto->borrowedAt,
                'due_date' => $dto->dueDate,
                'renewal_count' => 0,
            ]);

            $this->fulfillReservationIfExists($user->id, $book->id, $borrow->id);
            $this->updateBookStatus($book, 'borrowed');

            event(new BookBorrowed($borrow));

            return $borrow->load('book', 'user');
        });
    }

    public function returnBook(ReturnBookDTO $dto): Borrow
    {
        return DB::transaction(function () use ($dto) {
            $borrow = Borrow::where('id', $dto->borrowId)
                ->where('book_id', $dto->bookId)
                ->where('user_id', $dto->userId)
                ->whereNull('returned_at')
                ->firstOrFail();

            $lateFee = $this->calculateLateFee($borrow, $dto->returnedAt);

            $borrow->update([
                'returned_at' => $dto->returnedAt,
                'late_fee' => $lateFee,
                'notes' => $dto->notes ?? null,
            ]);

            $this->updateBookStatus($borrow->book, 'Available');

            event(new BookReturned($borrow));

            return $borrow->load('book', 'user');
        });
    }

    public function renewBorrow(Borrow $borrow, int $days = 14): Borrow
    {
        return DB::transaction(function () use ($borrow, $days) {
            $this->validateRenewal($borrow);

            $borrow->update([
                'due_date' => $borrow->due_date->addDays($days),
                'renewal_count' => $borrow->renewal_count + 1,
            ]);

            return $borrow->fresh();
        });
    }

    public function getUserBorrows(
        int $userId,
        ?string $status = null,
        string $sortBy = 'due_date',
        string $sortOrder = 'asc',
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Borrow::with('book', 'user')
            ->where('user_id', $userId);

        $this->applyFilters($query, $status, $sortBy, $sortOrder, $search);

        return $query->paginate($perPage);
    }

    public function getAllBorrows(
        ?string $status = null,
        string $sortBy = 'due_date',
        string $sortOrder = 'asc',
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Borrow::with(['book', 'user']);

        $this->applyFilters($query, $status, $sortBy, $sortOrder, $search);

        return $query->paginate($perPage);
    }

    public function getBorrowDetails(int $id): Borrow
    {
        return Borrow::with(['book', 'user', 'fulfilledReservation'])
            ->findOrFail($id);
    }

    public function getBookBorrowHistory(int $bookId, int $perPage = 5): LengthAwarePaginator
    {
        return Borrow::with('user')
            ->where('book_id', $bookId)
            ->orderBy('borrowed_at', 'desc')
            ->paginate($perPage);
    }

    public function canRenew(Borrow $borrow): bool
    {
        return $borrow->returned_at === null
            && $borrow->renewal_count < config('library.max_renewals', 2)
            && ! $borrow->isOverdue();
    }

    public function checkOverdueBooks(): void
    {
        Borrow::overdue()
            ->get()
            ->each(function ($borrow) {
                event(new BookOverdue($borrow));
            });
    }

    /**
     * @throws Exception
     */
    protected function validateBorrow(Book $book, User $user): void
    {
        if (! $book->is_available) {
            throw new Exception('This book is not currently available for borrowing');
        }

        // Check if user has reached max borrow limit
        $currentBorrows = Borrow::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        if ($currentBorrows >= config('library.max_borrows_per_user', 5)) {
            throw new Exception('You have reached the maximum number of borrowed books');
        }

        if (! $user->is_admin && ! $this->hasActiveReservation($user->id, $book->id)) {
            throw new Exception('You need an active reservation to borrow this book');
        }
    }

    /**
     * @throws Exception
     */
    protected function validateRenewal(Borrow $borrow): void
    {
        if ($borrow->returned_at) {
            throw new Exception('Cannot renew a returned book');
        }

        if ($borrow->renewal_count >= config('library.max_renewals', 2)) {
            throw new Exception('Maximum renewal limit reached');
        }

        if ($borrow->isOverdue()) {
            throw new Exception('Cannot renew an overdue book');
        }
        if (! $borrow->book->canBeRenewed()) {
            throw new \Exception('This book cannot be renewed at this time');
        }
    }

    protected function calculateLateFee(Borrow $borrow, ?string $returnedAt = null): float
    {
        $returnDate = $returnedAt ? now()->parse($returnedAt) : now();
        $gracePeriod = config('library.grace_period_days', 1);

        if ($borrow->due_date->addDays($gracePeriod)->lt($returnDate)) {
            $daysLate = $borrow->due_date->diffInDays($returnDate) - $gracePeriod;

            return max(0, $daysLate) * config('library.daily_late_fee', 0.50);
        }

        return 0;
    }

    protected function fulfillReservationIfExists(int $userId, int $bookId, int $borrowId): void
    {
        Reservation::activeForUser($userId, $bookId)
            ->first()?->update([
                'fulfilled_by_borrow_id' => $borrowId,
                'expires_at' => now(),
            ]);
    }

    protected function updateBookStatus(Book $book, string $status): void
    {
        $book->update(['status' => $status]);
    }

    protected function hasActiveReservation(int $userId, int $bookId): bool
    {
        return Reservation::activeForUser($userId, $bookId)->exists();
    }

    protected function applyFilters($query, ?string $status, string $sortBy, string $sortOrder, ?string $search): void
    {
        // Apply status filter
        match ($status) {
            'active' => $query->whereNull('returned_at'),
            'overdue' => $query->overdue(),
            'returned' => $query->whereNotNull('returned_at'),
            default => null,
        };

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }

        // Apply sorting
        $validSorts = [
            'borrowed_at', 'due_date', 'returned_at',
            'book.title', 'book.author', 'user.name',
        ];

        if (! in_array($sortBy, $validSorts)) {
            $sortBy = 'due_date';
        }

        if (str_contains($sortBy, '.')) {
            [$relation, $column] = explode('.', $sortBy);
            $query->join($relation.'s', 'borrows.book_id', '=', $relation.'s.id')
                ->orderBy("$relation.$column", $sortOrder)
                ->select('borrows.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
    }

    public function deleteBorrow(int $id): void
    {
        Borrow::query()->where('id', $id)->delete();
    }
}
