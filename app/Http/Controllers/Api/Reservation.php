<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\ReserveBookDTO;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use Exception;
use Illuminate\Http\Request;

final class Reservation extends Controller
{
    public function __construct(protected ReservationService $reservationService) {}

    /**
     * @throws Exception
     */
    public function reserveBook(Request $request)
    {
        $dto = new ReserveBookDTO(
            $request->integer('book_id'),
            $request->integer('user_id'),
            $request->input('fulfilled_at'),
            $request->input('fulfilled_at'),
        );

        $reservation = $this->reservationService->reserveBook($dto);

        return response()->json($reservation, 201);
    }
}
