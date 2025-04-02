<?php

namespace App\DTO;

class ReserveBookDTO
{
    public function __construct(
        public int $bookId,
        public int $userId,
        public ?string $reservedAt = null,
        public ?string $expiresAt = null
    ) {
        $this->reservedAt = $reservedAt ?? now()->toDateTimeString();
        $this->expiresAt = $expiresAt ?? now()->addDays(config('library.reservation_expiry_days', 7))->toDateTimeString();
    }
}
