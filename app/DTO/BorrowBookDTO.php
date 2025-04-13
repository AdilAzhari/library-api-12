<?php

namespace App\DTO;

class BorrowBookDTO
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
