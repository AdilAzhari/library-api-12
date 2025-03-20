<?php

namespace App\Services;

use App\DTO\ReserveBookDTO;
use App\Models\Reservation;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ReservationService
{
    /**
     * @throws Exception
     */
    public function reserveBook(ReserveBookDTO $dto)
    {
        try {
            return Reservation::query()->create([
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
                'reserved_at' => $dto->reserved_at,
                'fulfilled_at' => $dto->fulfilled_at
            ]);
        } catch (Exception $e) {
            throw new Exception('Error reserving book: ' . $e->getMessage());
        }
    }

    public function listReservations(): Collection
    {
        return Reservation::with(['book', 'user'])->get();
    }
}
