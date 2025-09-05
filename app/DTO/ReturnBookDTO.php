<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

final class ReturnBookDTO
{
    public readonly string $returnedAt;

    public function __construct(
        public int $borrowId,
        public int $bookId,
        public int $userId,
        Carbon|string|null $returnedAt = null,
        public ?float $lateFee = null
    ) {
        $this->returnedAt = $returnedAt instanceof Carbon
            ? $returnedAt->toDateString()
            : ($returnedAt ?? now()->toDateString());
    }
}
