<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\ReserveBookDTO;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Reservation;
use App\Services\ReservationService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class ReservationController extends Controller
{
    use ApiMessages;

    public function __construct(protected ReservationService $reservationService) {}

    public function reserveBook(Request $request)
    {
        Log::info('ReservationController::reserveBook - Starting book reservation', [
            'book_id' => $request->input('book_id'),
            'user_id' => $request->input('user_id'),
            'request_data' => $request->all(),
            'authenticated_user' => Auth::id(),
        ]);

        try {
            $dto = new ReserveBookDTO(
                bookId: $request->input('book_id'),
                userId: $request->input('user_id'),
                reserved_at: $request->input('reserved_at') ?? null,
                fulfilled_at: $request->input('fulfilled_at') ?? null,
            );

            $reservation = $this->reservationService->reserveBook($dto);

            Log::info('ReservationController::reserveBook - Book reserved successfully', [
                'reservation_id' => $reservation->id ?? null,
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
            ]);

            return $this->successResponse('Book reserved successfully', $reservation, 201);
        } catch (Exception $e) {
            Log::error('ReservationController::reserveBook - Error reserving book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $request->input('book_id'),
                'user_id' => $request->input('user_id'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while reserving the book.');
        }
    }

    public function listReservations()
    {
        Log::info('ReservationController::listReservations - Starting reservations listing', [
            'user_id' => Auth::id(),
        ]);

        try {
            $reservations = $this->reservationService->listReservations();

            Log::info('ReservationController::listReservations - Reservations retrieved successfully', [
                'count' => is_countable($reservations) ? count($reservations) : 'unknown',
                'user_id' => Auth::id(),
            ]);

            return $this->successResponse('Reservations retrieved successfully', $reservations);
        } catch (Exception $e) {
            Log::error('ReservationController::listReservations - Error listing reservations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while listing reservations.');
        }
    }

    /**
     * Get user's active reservations
     */
    public function active(Request $request)
    {
        Log::info('ReservationController::active - Getting active reservations', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
        ]);

        try {
            $user = Auth::user();
            $activeReservations = $user->reservations()
                ->whereNull('fulfilled_by_borrow_id')
                ->whereNull('canceled_at')
                ->with(['book.genre'])
                ->orderBy('created_at')
                ->get()
                ->map(function ($reservation) {
                    $queuePosition = Reservation::query()->where('book_id', $reservation->book_id)
                        ->whereNull('fulfilled_by_borrow_id')
                        ->whereNull('canceled_at')
                        ->where('created_at', '<', $reservation->created_at)
                        ->count() + 1;

                    $reservation->queue_position = $queuePosition;
                    $reservation->estimated_wait_days = $queuePosition * 14; // Assume 14-day loans

                    return $reservation;
                });

            Log::info('ReservationController::active - Active reservations retrieved successfully', [
                'user_id' => Auth::id(),
                'count' => $activeReservations->count(),
            ]);

            return $this->successResponse('Active reservations retrieved successfully', $activeReservations);
        } catch (Exception $e) {
            Log::error('ReservationController::active - Error getting active reservations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('Unable to get active reservations');
        }
    }

    /**
     * Get reservations ready for pickup
     */
    public function ready(Request $request)
    {
        try {
            $user = Auth::user();
            $readyReservations = $user->reservations()
                ->whereNull('fulfilled_by_borrow_id')
                ->whereNull('canceled_at')
                ->with(['book.genre'])
                ->orderBy('created_at')
                ->get()
                ->map(function ($reservation) {
                    $reservation->days_until_expires = now()->diffInDays($reservation->expires_at, false);

                    return $reservation;
                });

            return $this->successResponse('Ready reservations retrieved successfully', $readyReservations);
        } catch (Exception $e) {
            Log::error('Error getting ready reservations: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get ready reservations');
        }
    }

    /**
     * Get user's reservation history
     */
    public function history(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = min($request->integer('per_page', 15), 50);

            $reservationHistory = $user->reservations()
                ->where(function ($query): void {
                    $query->whereNotNull('fulfilled_by_borrow_id')
                        ->orWhereNotNull('canceled_at');
                })
                ->with(['book.genre'])
                ->orderBy('updated_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse('Reservation history retrieved successfully', $reservationHistory);
        } catch (Exception $e) {
            Log::error('Error getting reservation history: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get reservation history');
        }
    }

    /**
     * Get queue information for a specific book
     */
    public function queueInfo(Request $request, Book $book)
    {
        try {
            $queueLength = $book->reservations()
                ->whereNull('fulfilled_by_borrow_id')
                ->whereNull('canceled_at')
                ->count();

            $userPosition = null;
            if (Auth::check()) {
                $userReservation = $book->reservations()
                    ->where('user_id', Auth::id())
                    ->whereNull('fulfilled_by_borrow_id')
                    ->whereNull('canceled_at')
                    ->first();

                if ($userReservation) {
                    $userPosition = $book->reservations()
                        ->whereNull('fulfilled_by_borrow_id')
                        ->whereNull('canceled_at')
                        ->where('created_at', '<', $userReservation->created_at)
                        ->count() + 1;
                }
            }

            $queueInfo = [
                'book_id' => $book->id,
                'queue_length' => $queueLength,
                'user_position' => $userPosition,
                'estimated_wait_time' => $queueLength * 14, // days
                'current_status' => $book->status,
            ];

            return $this->successResponse('Queue information retrieved successfully', $queueInfo);
        } catch (Exception $e) {
            Log::error('Error getting queue info: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get queue information');
        }
    }

    /**
     * Get specific reservation queue position
     */
    public function queuePosition(Request $request, Reservation $reservation)
    {
        try {
            if ($reservation->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $position = $reservation->book->reservations()
                ->whereNull('fulfilled_by_borrow_id')
                ->whereNull('canceled_at')
                ->where('created_at', '<', $reservation->created_at)
                ->count() + 1;

            $positionInfo = [
                'reservation_id' => $reservation->id,
                'position' => $position,
                'estimated_wait_days' => $position * 14,
                'created_at' => $reservation->created_at,
            ];

            return $this->successResponse('Queue position retrieved successfully', $positionInfo);
        } catch (Exception $e) {
            Log::error('Error getting queue position: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get queue position');
        }
    }

    /**
     * Extend reservation hold time
     */
    public function extend(Request $request, Reservation $reservation)
    {
        try {
            if ($reservation->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $extendedReservation = $this->reservationService->extendReservation($reservation);

            return $this->successResponse('Reservation extended successfully', $extendedReservation);
        } catch (Exception $e) {
            Log::error('Error extending reservation: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to extend reservation');
        }
    }

    /**
     * Confirm pickup of reserved book
     */
    public function confirmPickup(Request $request, Reservation $reservation)
    {
        try {
            if ($reservation->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $borrow = $this->reservationService->confirmPickup($reservation);

            return $this->successResponse('Book pickup confirmed successfully', $borrow);
        } catch (Exception $e) {
            Log::error('Error confirming pickup: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to confirm pickup');
        }
    }
}
