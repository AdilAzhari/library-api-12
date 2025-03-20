<?php

namespace App\Http\Controllers\Api;

use App\DTO\ReserveBookDTO;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class Reservation extends Controller
{
    public function __construct(protected ReservationService $reservationService)
    {
    }

    public function reserveBook(Request $request)
    {
        $dto = new ReserveBookDTO(
            bookId: $request->input('book_id'),
            userId: $request->input('user_id'),
        );

        $reservation = $this->reservationService->reserveBook($dto);

        return response()->json($reservation, 201);
    }
}
