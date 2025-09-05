<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enum\BookStatus;
use App\Enum\ReservationStatus;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class ReserveBookAction
{
    public function __construct(
        private ReservationService $reservationService,
    ) {}

    public function execute(User $user, Book $book, array $options = []): Reservation
    {
        return DB::transaction(function () use ($user, $book, $options) {
            // Validate the reservation request
            $this->validate($user, $book);

            // Calculate expiry date
            $expiryDays = $options['expiry_days'] ?? config('library.settings.reservation_expiry_days', 7);
            $expiresAt = Carbon::now()->addDays($expiryDays);

            // Create reservation
            $reservation = App\Models\Reservation::query()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'expires_at' => $expiresAt,
                'status' => ReservationStatus::ACTIVE->value,
                'notes' => $options['notes'] ?? null,
            ]);

            // Update book status if it's currently available
            if ($book->status === BookStatus::AVAILABLE->value) {
                $book->update(['status' => BookStatus::RESERVED->value]);
            }

            // Send reservation confirmation
            $this->sendReservationConfirmation($reservation);

            return $reservation;
        });
    }

    public function cancelReservation(Reservation $reservation, ?string $reason = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $reason) {
            if (! $reservation->canCancel()) {
                throw new InvalidArgumentException('Reservation cannot be cancelled');
            }

            $reservation->update([
                'status' => ReservationStatus::CANCELLED->value,
                'canceled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Update book status if no other active reservations
            $book = $reservation->book;
            $hasOtherReservations = $book->reservations()
                ->where('status', ReservationStatus::ACTIVE->value)
                ->where('id', '!=', $reservation->id)
                ->exists();

            if (! $hasOtherReservations && $book->status === BookStatus::RESERVED->value) {
                // Check if book is currently borrowed
                $isCurrentlyBorrowed = $book->activeBorrow()->exists();
                $newStatus = $isCurrentlyBorrowed ? BookStatus::BORROWED : BookStatus::AVAILABLE;
                $book->update(['status' => $newStatus->value]);
            }

            // Notify next person in queue if applicable
            $this->notifyNextInQueue($book);

            return $reservation->fresh();
        });
    }

    public function extendReservation(Reservation $reservation, int $additionalDays): Reservation
    {
        $maxExtensionDays = config('library.settings.max_reservation_extension_days', 7);

        if ($additionalDays > $maxExtensionDays) {
            throw new InvalidArgumentException(
                'Extension cannot exceed '.$maxExtensionDays.' days'
            );
        }

        if (! $reservation->isActive()) {
            throw new InvalidArgumentException('Only active reservations can be extended');
        }

        $reservation->update([
            'expires_at' => $reservation->expires_at->addDays($additionalDays),
            'notes' => ($reservation->notes ?? '')."\nExtended by {$additionalDays} days on ".now()->format('Y-m-d'),
        ]);

        return $reservation->fresh();
    }

    private function validate(User $user, Book $book): void
    {
        // Check if user can make reservations
        if (! $user->canBorrowMoreBooks()) {
            throw new InvalidArgumentException('User has reached maximum borrow limit and cannot make reservations');
        }

        // Check if user has overdue books
        if ($user->hasOverdueBooks()) {
            throw new InvalidArgumentException('User has overdue books and cannot make reservations');
        }

        // Check if user has outstanding fines above threshold
        $maxFineAmount = config('library.settings.max_fine_amount_for_reservations', 5.00);
        if ($user->hasOutstandingFines() && $user->getOutstandingFineAmount() > $maxFineAmount) {
            throw new InvalidArgumentException(
                'User has outstanding fines exceeding $'.$maxFineAmount.' and cannot make reservations'
            );
        }

        // Check if book can be reserved
        if (! $book->canBeReserved()) {
            throw new InvalidArgumentException('Book cannot be reserved');
        }

        // Check if user already has this book borrowed
        if ($user->activeBorrows()->where('book_id', $book->id)->exists()) {
            throw new InvalidArgumentException('User already has this book borrowed');
        }

        // Check if user already has an active reservation for this book
        if ($user->activeReservations()->where('book_id', $book->id)->exists()) {
            throw new InvalidArgumentException('User already has an active reservation for this book');
        }

        // Check reservation limits
        $maxReservations = config('library.settings.max_reservations_per_user', 3);
        if ($user->activeReservations()->count() >= $maxReservations) {
            throw new InvalidArgumentException(
                'User has reached maximum number of reservations ('.$maxReservations.')'
            );
        }

        // Check if user's library card is valid
        $libraryCard = $user->activeLibraryCard;
        if (! $libraryCard || ! $libraryCard->canBorrow()) {
            throw new InvalidArgumentException('User does not have a valid library card');
        }

        // Check queue position limit if configured
        $maxQueuePosition = config('library.settings.max_reservation_queue_position', 10);
        $currentQueueSize = Reservation::query()->where('book_id', $book->id)
            ->where('status', ReservationStatus::ACTIVE->value)
            ->count();

        if ($currentQueueSize >= $maxQueuePosition) {
            throw new InvalidArgumentException(
                'Reservation queue for this book is full (maximum '.$maxQueuePosition.' reservations)'
            );
        }
    }

    private function sendReservationConfirmation(Reservation $reservation): void
    {
        // Calculate queue position
        $queuePosition = Reservation::query()->where('book_id', $reservation->book_id)
            ->where('status', ReservationStatus::ACTIVE->value)
            ->where('created_at', '<', $reservation->created_at)
            ->count() + 1;

        // Implementation would send confirmation with queue position
        // Could include estimated availability date based on current borrows and queue
    }

    private function notifyNextInQueue(Book $book): void
    {
        $nextReservation = $book->reservations()
            ->where('status', ReservationStatus::ACTIVE->value)
            ->orderBy('created_at')
            ->first();

        if ($nextReservation) {
            // Send notification that they moved up in the queue
            // Implementation would send notification
        }
    }
}
