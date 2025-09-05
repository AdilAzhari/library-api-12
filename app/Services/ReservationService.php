<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ReserveBookDTO;
use App\Events\ReservationCancelled;
use App\Events\ReservationCreated;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ReservationService
{
    /**
     * Reserve a book.
     *
     * @throws Exception
     */
    /**
     * Reserve a book.
     *
     * @param  ReserveBookDTO  $dto  The DTO containing reservation details.
     * @return Reservation The created reservation.
     *
     * @throws Exception If the reservation fails to be created.
     */
    public function reserveBook(ReserveBookDTO $dto): Reservation
    {
        try {
            return Reservation::query()->create([
                'book_id' => $dto->bookId,
                'user_id' => auth()->user()->id,
                'reserved_at' => now(),
                'fulfilled_at' => null,
            ]);
        } catch (Exception $e) {
            Log::error('Error reserving book: '.$e->getMessage());
            throw new Exception('Failed to reserve book. Please try again.');
        }
    }

    /**
     * List reservations with filtering, sorting, and pagination.
     */
    public function listReservations(array $filters = [], string $sortBy = 'reserved_at', string $sortOrder = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        $query = Reservation::with(['book.activeBorrow', 'book.activeReservation', 'user']);

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }
        if (isset($filters['fulfilled'])) {
            $query->whereNotNull('fulfilled_at');
        }
        if (isset($filters['unfulfilled'])) {
            $query->whereNull('fulfilled_at');
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get a single reservation by ID.
     *
     * @throws Exception
     */
    public function getReservation(int $reservationId): Reservation
    {
        try {
            return Reservation::with(['book.activeBorrow', 'book.activeReservation', 'user'])->findOrFail($reservationId);
        } catch (Exception $e) {
            Log::error('Error fetching reservation: '.$e->getMessage());
            throw new Exception('Failed to fetch reservation. Please try again.');
        }
    }

    /**
     * Update a reservation.
     *
     * @throws Exception
     */
    public function updateReservation(int $reservationId, array $data): Reservation
    {
        try {
            $reservation = Reservation::query()->findOrFail($reservationId);
            $reservation->update($data);

            return $reservation;
        } catch (Exception $e) {
            Log::error('Error updating reservation: '.$e->getMessage());
            throw new Exception('Failed to update reservation. Please try again.');
        }
    }

    /**
     * Delete a reservation.
     *
     * @throws Exception
     */
    public function deleteReservation(int $reservationId): void
    {
        try {
            $reservation = Reservation::query()->findOrFail($reservationId);
            $reservation->delete();
        } catch (Exception $e) {
            Log::error('Error deleting reservation: '.$e->getMessage());
            throw new Exception('Failed to delete reservation. Please try again.');
        }
    }

    /**
     * Get paginated list of reservations for a specific user with filtering and sorting
     *
     * @throws Exception
     */
    public function getUserReservations(
        int $userId,
        array $filters = [],
        string $sortBy = 'reserved_at',
        string $sortOrder = 'desc',
        int $perPage = 10
    ): LengthAwarePaginator {
        try {
            $query = Reservation::with(['book.activeBorrow', 'book.activeReservation', 'borrowing'])
                ->where('user_id', $userId)
                ->whereNull('canceled_at');

            // Apply status filter
            if (isset($filters['status'])) {
                match ($filters['status']) {
                    'active' => $query->active(),
                    'expired' => $query->expired(),
                    'fulfilled' => $query->fulfilled(),
                    default => null,
                };
            }

            // Apply book filter
            if (isset($filters['book_id'])) {
                $query->where('book_id', $filters['book_id']);
            }

            // Validate sort column
            $sortBy = in_array($sortBy, ['reserved_at', 'expires_at', 'book.title'])
                ? $sortBy
                : 'reserved_at';

            // Special handling for book title sorting
            if ($sortBy === 'book.title') {
                $query->join('books', 'reservations.book_id', '=', 'books.id')
                    ->orderBy('books.title', $sortOrder)
                    ->select('reservations.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            return $query->paginate($perPage);

        } catch (Exception $e) {
            Log::error('Failed to fetch user reservations: '.$e->getMessage());
            throw new Exception('Failed to retrieve reservations. Please try again.');
        }
    }

    /**
     * Get a single reservation for a user
     *
     * @throws Exception
     */
    public function getUserReservation(int $reservationId, int $userId): ?Reservation
    {
        try {
            return Reservation::with(['book.activeBorrow', 'book.activeReservation', 'borrowing'])
                ->where('id', $reservationId)
                ->where('user_id', $userId)
                ->whereNull('canceled_at')
                ->first();
        } catch (Exception $e) {
            Log::error('Failed to fetch user reservation: '.$e->getMessage());
            throw new Exception('Failed to retrieve reservation. Please try again.');
        }
    }

    /**
     * Create a new book reservation
     *
     * @throws Exception
     */
    public function createReservation(ReserveBookDTO $dto): Reservation
    {
        return DB::transaction(function () use ($dto) {
            try {
                $book = Book::query()->findOrFail($dto->bookId);
                $user = User::Query()->findOrFail($dto->userId);

                // Check book availability
                if (! $book->isAvailable()) {
                    throw new Exception('This book is not currently available for reservation.');
                }

                // Check for existing active reservation
                if ($this->hasActiveReservation($user->id, $book->id)) {
                    throw new Exception('You already have an active reservation for this book.');
                }

                // Calculate expiry date (default 7 days)
                $expiryDays = config('library.reservation_expiry_days', 7);
                $expiresAt = now()->addDays($expiryDays);

                $reservation = Reservation::query()->create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'expires_at' => $expiresAt,
                ]);

                // Trigger event
                event(new ReservationCreated($reservation));

                return $reservation->load('book');

            } catch (Exception $e) {
                Log::error('Reservation creation failed: '.$e->getMessage());
                throw new Exception('Could not create reservation. '.$e->getMessage());
            }
        });
    }

    /**
     * Cancel an existing reservation
     *
     * @throws Exception
     */
    public function cancelReservation(int $reservationId, int $userId): void
    {
        DB::transaction(function () use ($reservationId, $userId): void {
            try {
                $reservation = Reservation::query()->where('id', $reservationId)
                    ->where('user_id', $userId)
                    ->whereNull('fulfilled_by_borrowing_id')
                    ->whereNull('canceled_at')
                    ->firstOrFail();

                // Only allow cancellation if reservation is still active
                if ($reservation->expires_at < now()) {
                    throw new Exception('Cannot cancel an expired reservation.');
                }

                $reservation->update([
                    'canceled_at' => now(),
                    'canceled_by' => $userId,
                ]);

                // Trigger event
                event(new ReservationCancelled($reservation));

            } catch (Exception $e) {
                Log::error('Reservation cancellation failed: '.$e->getMessage());
                throw new Exception('Could not cancel reservation. '.$e->getMessage());
            }
        });
    }

    /**
     * Check if user has an active reservation for a book
     */
    public function hasActiveReservation(int $userId, int $bookId): bool
    {
        return Reservation::activeForUser($userId, $bookId)->exists();
    }

    /**
     * Get active reservation for a user and book
     */
    public function getActiveReservation(int $userId, int $bookId): ?Reservation
    {
        return Reservation::activeForUser($userId, $bookId)->first();
    }

    /**
     * Get all active reservations for a user
     */
    public function getActiveReservationsForUser(int $userId)
    {
        return Reservation::activeForUser($userId)
            ->with(['book.activeBorrow', 'book.activeReservation'])
            ->orderBy('expires_at')
            ->get();
    }
}
