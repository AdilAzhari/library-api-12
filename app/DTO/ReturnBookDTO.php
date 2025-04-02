<?php

namespace App\DTO;
class ReturnBookDTO
{
    public function __construct(
        public int $borrowId,
        public int $bookId,
        public int $userId,
        public ?string $returnedAt = null,
        public ?float $lateFee = null
    ) {
        $this->returnedAt = $returnedAt ?? now()->toDateString();
    }
}
