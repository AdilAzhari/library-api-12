<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Enum\BookStatus;
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
use Illuminate\Support\Facades\Log;

final class BorrowingService
{
    public function borrowBook(BorrowBookDTO $dto): Borrow
    {
        Log::info('BorrowingService::borrowBook - Starting book borrowing', [
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
            'due_date' => $dto->dueDate,
        ]);

        try {
            return DB::transaction(function () use ($dto) {
                $book = Book::query()->findOrFail($dto->bookId);
                $user = User::query()->findOrFail($dto->userId);

                $this->validateBorrow($book, $user);

                $borrow = Borrow::query()->create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'borrowed_at' => $dto->borrowedAt,
                    'due_date' => $dto->dueDate,
                    'renewal_count' => 0,
                ]);

                $this->fulfillReservationIfExists($user->id, $book->id, $borrow->id);
                $this->updateBookStatus($book, BookStatus::BORROWED);

                event(new BookBorrowed($borrow));

                Log::info('BorrowingService::borrowBook - Book borrowed successfully', [
                    'borrow_id' => $borrow->id,
                    'book_id' => $dto->bookId,
                    'user_id' => $dto->userId,
                    'due_date' => $borrow->due_date,
                ]);

                return $borrow->load('book', 'user');
            });
        } catch (Exception $e) {
            Log::error('BorrowingService::borrowBook - Error borrowing book', [
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

    public function returnBook(ReturnBookDTO $dto): Borrow
    {
        Log::info('BorrowingService::returnBook - Starting book return', [
            'borrow_id' => $dto->borrowId ?? null,
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
        ]);

        try {
            return DB::transaction(function () use ($dto) {
                $borrow = Borrow::query()->where('id', $dto->borrowId)
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

                $this->updateBookStatus($borrow->book, BookStatus::AVAILABLE);

                event(new BookReturned($borrow));

                Log::info('BorrowingService::returnBook - Book returned successfully', [
                    'borrow_id' => $borrow->id,
                    'book_id' => $dto->bookId,
                    'user_id' => $dto->userId,
                    'late_fee' => $lateFee,
                ]);

                return $borrow->load('book', 'user');
            });
        } catch (Exception $e) {
            Log::error('BorrowingService::returnBook - Error returning book', [
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
        $query = Borrow::query()->with('book', 'user')
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
        $query = Borrow::query()->with(['book', 'user']);

        $this->applyFilters($query, $status, $sortBy, $sortOrder, $search);

        return $query->paginate($perPage);
    }

    public function getBorrowDetails(int $id): Borrow
    {
        return Borrow::query()->with(['book', 'user', 'fulfilledReservation'])
            ->findOrFail($id);
    }

    public function getBookBorrowHistory(int $bookId, int $perPage = 5): LengthAwarePaginator
    {
        return Borrow::query()->with('user')
            ->where('book_id', $bookId)
            ->orderBy('borrowed_at', 'desc')
            ->paginate($perPage);
    }

    public function renewBook(Borrow $borrow): Borrow
    {
        return $this->renewBorrow($borrow);
    }

    public function canRenewBook(Borrow $borrow): bool
    {
        return $this->canRenew($borrow);
    }

    public function canRenew(Borrow $borrow): bool
    {
        return $borrow->returned_at === null
            && $borrow->renewal_count < config('library.max_renewals', 2)
            && ! $borrow->isOverdue()
            && ! $borrow->book->hasPendingReservations();
    }

    public function checkOverdueBooks(): void
    {
        Borrow::overdue()
            ->get()
            ->each(function ($borrow): void {
                event(new BookOverdue($borrow));
            });
    }

    public function deleteBorrow(int $id): void
    {
        Borrow::query()->where('id', $id)->delete();
    }

    /**
     * @throws Exception
     */
    private function validateBorrow(Book $book, User $user): void
    {
        if (! $book->canBeBorrowed()) {
            throw new Exception('This book is not currently available for borrowing');
        }

        // Check if user has reached max borrow limit
        $currentBorrows = Borrow::query()->where('user_id', $user->id)
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
    private function validateRenewal(Borrow $borrow): void
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
            throw new Exception('This book cannot be renewed at this time');
        }
    }

    private function calculateLateFee(Borrow $borrow, ?string $returnedAt = null): float
    {
        $returnDate = $returnedAt ? now()->parse($returnedAt) : now();
        $gracePeriod = config('library.grace_period_days', 1);

        if ($borrow->due_date->addDays($gracePeriod)->lt($returnDate)) {
            $daysLate = $borrow->due_date->diffInDays($returnDate) - $gracePeriod;

            return max(0, $daysLate) * config('library.daily_late_fee', 0.50);
        }

        return 0;
    }

    private function fulfillReservationIfExists(int $userId, int $bookId, int $borrowId): void
    {
        Reservation::activeForUser($userId, $bookId)
            ->first()?->update([
                'fulfilled_by_borrow_id' => $borrowId,
                'expires_at' => now(),
            ]);
    }

    private function updateBookStatus(Book $book, BookStatus $status): void
    {
        $book->update(['status' => $status]);
    }

    private function hasActiveReservation(int $userId, int $bookId): bool
    {
        return Reservation::activeForUser($userId, $bookId)->exists();
    }

    private function applyFilters($query, ?string $status, string $sortBy, string $sortOrder, ?string $search): void
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
            $query->where(function ($q) use ($search): void {
                $q->whereHas('book', function ($q) use ($search): void {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%");
                })->orWhereHas('user', function ($q) use ($search): void {
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
}
