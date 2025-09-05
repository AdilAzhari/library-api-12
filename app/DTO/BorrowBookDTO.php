<?php

declare(strict_types=1);

namespace App\DTO;

final class BorrowBookDTO
{
    public function __construct(
        public int $bookId,
        public int $userId,
        public ?string $borrowedAt = null,
        public ?string $dueDate = null
    ) {
        $this->borrowedAt = $borrowedAt ?? now()->toDateTimeString();
        $this->dueDate = $dueDate ?? now()->addDays(config('library.default_borrow_days', 14))->toDateString();
    }
}
