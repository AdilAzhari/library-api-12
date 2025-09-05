<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTO\BorrowCreateDTO;
use App\Enum\BookStatus;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class BorrowBookAction
{
    public function __construct(
        private ReservationService $reservationService,
    ) {}

    public function execute(BorrowCreateDTO $dto): Borrow
    {
        return DB::transaction(function () use ($dto) {
            $user = App\Models\User::query()->findOrFail($dto->user_id);
            $book = App\Models\Book::query()->findOrFail($dto->book_id);

            // Validate the borrow request
            $this->validate($user, $book);

            // Create the borrow record
            $borrow = App\Models\Borrow::query()->create($dto->toModel());

            // Update book status
            $book->update(['status' => BookStatus::BORROWED->value]);

            // Fulfill any active reservation for this user and book
            $this->fulfillReservation($user, $book, $borrow);

            // Send confirmation notification
            $this->sendBorrowConfirmation($borrow);

            return $borrow;
        });
    }

    private function validate(User $user, Book $book): void
    {
        // Check if user can borrow more books
        if (! $user->canBorrowMoreBooks()) {
            throw new InvalidArgumentException(
                'User has reached the maximum number of borrowed books ('.
                config('library.settings.max_loans_per_user', 5).')'
            );
        }

        // Check if user has overdue books
        if ($user->hasOverdueBooks()) {
            throw new InvalidArgumentException('User has overdue books and cannot borrow until returned');
        }

        // Check if user has outstanding fines
        if ($user->hasOutstandingFines()) {
            $maxFineAmount = config('library.settings.max_fine_amount_for_borrowing', 10.00);
            if ($user->getOutstandingFineAmount() > $maxFineAmount) {
                throw new InvalidArgumentException(
                    'User has outstanding fines exceeding $'.$maxFineAmount.' and cannot borrow'
                );
            }
        }

        // Check if book is available
        if (! $book->canBeBorrowed()) {
            throw new InvalidArgumentException('Book is not available for borrowing');
        }

        // Check if user already has this book borrowed
        if ($user->activeBorrows()->where('book_id', $book->id)->exists()) {
            throw new InvalidArgumentException('User already has this book borrowed');
        }

        // Check if book has active reservation for another user
        if ($book->activeReservation && $book->activeReservation->user_id !== $user->id) {
            throw new InvalidArgumentException('Book is reserved for another user');
        }

        // Check if user's library card is valid
        $libraryCard = $user->activeLibraryCard;
        if (! $libraryCard || ! $libraryCard->canBorrow()) {
            throw new InvalidArgumentException('User does not have a valid library card');
        }
    }

    private function fulfillReservation(User $user, Book $book, Borrow $borrow): void
    {
        $reservation = $user->activeReservations()
            ->where('book_id', $book->id)
            ->first();

        if ($reservation) {
            $this->reservationService->fulfill($reservation, $borrow);
        }
    }

    private function sendBorrowConfirmation(Borrow $borrow): void
    {
        // Implementation would send confirmation email/notification
        // Could integrate with mail service, SMS service, etc.
    }
}
