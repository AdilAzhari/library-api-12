<?php

namespace App\Http\Controllers\Api;

use App\DTO\ReserveBookDTO;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    use ApiMessages;

    public function __construct(protected ReservationService $reservationService) {}

    public function reserveBook(Request $request)
    {
        try {
            $dto = new ReserveBookDTO(
                bookId: $request->input('book_id'),
                userId: $request->input('user_id'),
                reserved_at: $request->input('reserved_at') ?? null,
                fulfilled_at: $request->input('fulfilled_at') ?? null,
            );

            $reservation = $this->reservationService->reserveBook($dto);

            return $this->successResponse('Book reserved successfully', $reservation, 201);
        } catch (Exception $e) {
            Log::error('Error reserving book: '.$e->getMessage());

            return $this->serverErrorResponse('An error occurred while reserving the book.');
        }
    }

    public function listReservations()
    {
        try {
            $reservations = $this->reservationService->listReservations();

            return $this->successResponse('Reservations retrieved successfully', $reservations);
        } catch (Exception $e) {
            Log::error('Error listing reservations: '.$e->getMessage());

            return $this->serverErrorResponse('An error occurred while listing reservations.');
        }
    }
}
