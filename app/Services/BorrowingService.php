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
use Illuminate\Database\Eloquent\Collection;
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

            $this->updateBookStatus($borrow->book, 'available');

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
        int     $userId,
        ?string $status = null,
        string  $sortBy = 'due_date',
        string  $sortOrder = 'asc',
        int     $perPage = 10
    ): LengthAwarePaginator
    {
        $query = Borrow::with('book')
            ->where('user_id', $userId);

        $this->applyStatusFilter($query, $status);
        $this->applySorting($query, $sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getAllBorrows(
        ?string $status = null,
        string  $sortBy = 'due_date',
        string  $sortOrder = 'asc',
        int     $perPage = 15
    ): LengthAwarePaginator
    {
        $query = Borrow::with(['book', 'user']);

        $this->applyStatusFilter($query, $status);
        $this->applySorting($query, $sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getBorrowDetails(int $id): Borrow
    {
        return Borrow::with(['book', 'user', 'reservation'])
            ->findOrFail($id);
    }

    public function canRenew(Borrow $borrow): bool
    {
        return $borrow->returned_at === null
            && $borrow->renewal_count < config('library.max_renewals', 2)
            && !$borrow->isOverdue();
    }

    public function checkOverdueBooks(): void
    {
        Borrow::overdue()
            ->get()
            ->each(function ($borrow) {
                event(new BookOverdue($borrow));
            });
    }

    protected function calculateLateFee(Borrow $borrow, ?string $returnedAt = null): float
    {
        $returnDate = $returnedAt ? now()->parse($returnedAt) : now();

        if ($borrow->due_date->lt($returnDate)) {
            $daysLate = $borrow->due_date->diffInDays($returnDate);
            return $daysLate * config('library.daily_late_fee', 0.50);
        }

        return 0;
    }

    protected function validateBorrow(Book $book, User $user): void
    {
        if (!$book->is_available) {
            throw new \Exception('This book is not currently available for borrowing');
        }

        if (!$user->is_admin && !$this->hasActiveReservation($user->id, $book->id)) {
            throw new \Exception('You need an active reservation to borrow this book');
        }
    }

    protected function validateRenewal(Borrow $borrow): void
    {
        if ($borrow->returned_at) {
            throw new \Exception('Cannot renew a returned book');
        }

        if ($borrow->renewal_count >= config('library.max_renewals', 2)) {
            throw new \Exception('Maximum renewal limit reached');
        }

        if ($borrow->isOverdue()) {
            throw new \Exception('Cannot renew an overdue book');
        }
    }

    protected function fulfillReservationIfExists(int $userId, int $bookId, int $borrowId): void
    {
        Reservation::activeForUser($userId, $bookId)
            ->first()?->update([
                'fulfilled_by_borrow_id' => $borrowId
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

    protected function applyStatusFilter($query, ?string $status): void
    {
        match ($status) {
            'active' => $query->whereNull('returned_at'),
            'overdue' => $query->overdue(),
            'returned' => $query->whereNotNull('returned_at'),
            default => null,
        };
    }

    protected function applySorting($query, string $sortBy, string $sortOrder): void
    {
        $validSorts = [
            'borrowed_at', 'due_date', 'returned_at',
            'book.title', 'book.author', 'user.name'
        ];

        if (!in_array($sortBy, $validSorts)) {
            $sortBy = 'due_date';
        }

        if (str_contains($sortBy, '.')) {
            [$relation, $column] = explode('.', $sortBy);
            $query->join($relation . 's', 'borrows.book_id', '=', $relation . 's.id')
                ->orderBy("$relation.$column", $sortOrder)
                ->select('borrows.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
    }
}
