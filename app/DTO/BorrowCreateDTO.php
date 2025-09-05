<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\BorrowStatus;
use Carbon\Carbon;

final class BorrowCreateDTO extends BaseDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $book_id,
        public readonly Carbon $due_date,
        public readonly BorrowStatus $status = BorrowStatus::ACTIVE,
        public readonly ?string $notes = null,
    ) {}

    public function toModel(): array
    {
        return [
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'due_date' => $this->due_date,
            'status' => $this->status->value,
            'notes' => $this->notes,
            'borrowed_at' => now(),
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->due_date->isPast()) {
            $errors['due_date'] = 'Due date cannot be in the past';
        }

        if ($this->due_date->diffInDays(now()) > config('library.settings.max_borrow_days', 90)) {
            $errors['due_date'] = 'Borrow period cannot exceed maximum allowed days';
        }

        return $errors;
    }
}
