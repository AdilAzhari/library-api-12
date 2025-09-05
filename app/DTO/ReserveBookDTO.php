<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;
use InvalidArgumentException;

final class ReserveBookDTO
{
    public function __construct(
        public readonly int $bookId,
        public readonly int $userId,
        public ?DateTimeImmutable $expiresAt = null
    ) {
        if ($this->bookId <= 0) {
            throw new InvalidArgumentException('Invalid book ID');
        }

        if ($this->userId <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $this->expiresAt = $expiresAt ?? now()->addDays(7)->toDateTimeImmutable();
    }
}
