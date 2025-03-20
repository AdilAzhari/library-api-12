<?php

namespace App\DTO;

readonly class ReserveBookDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $bookId,
        public int $userId,
        public ?string $reserved_at,
        public ?string $fulfilled_at,
    )
    {
    }

}
