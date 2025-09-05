<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\FineReason;
use App\Enum\FineStatus;
use Carbon\Carbon;

final class FineCreateDTO extends BaseDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $book_id,
        public readonly float $amount,
        public readonly FineReason $reason,
        public readonly ?int $borrow_id = null,
        public readonly ?string $description = null,
        public readonly ?Carbon $due_date = null,
        public readonly FineStatus $status = FineStatus::PENDING,
        public readonly ?string $notes = null,
    ) {}

    public static function forOverdueBorrow(int $userId, int $bookId, int $borrowId, int $overdueDays): self
    {
        $dailyRate = config('library.settings.overdue_fee_per_day', 0.50);
        $amount = $overdueDays * $dailyRate;

        return new self(
            user_id: $userId,
            book_id: $bookId,
            amount: $amount,
            reason: FineReason::OVERDUE,
            borrow_id: $borrowId,
            description: "Overdue fine for $overdueDays days",
            due_date: Carbon::now()->addDays(30),
        );
    }

    public static function forLostBook(int $userId, int $bookId, int $borrowId, float $replacementCost): self
    {
        return new self(
            user_id: $userId,
            book_id: $bookId,
            amount: $replacementCost,
            reason: FineReason::LOST_BOOK,
            borrow_id: $borrowId,
            description: 'Replacement cost for lost book',
        );
    }

    public function toModel(): array
    {
        return [
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'amount' => $this->amount,
            'reason' => $this->reason->value,
            'borrow_id' => $this->borrow_id,
            'description' => $this->description ?? $this->reason->description(),
            'due_date' => $this->due_date ?? Carbon::now()->addDays(30),
            'status' => $this->status->value,
            'notes' => $this->notes,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->amount <= 0) {
            $errors['amount'] = 'Fine amount must be greater than zero';
        }

        if ($this->amount > 9999.99) {
            $errors['amount'] = 'Fine amount cannot exceed $9,999.99';
        }

        if ($this->due_date && $this->due_date->isPast()) {
            $errors['due_date'] = 'Due date cannot be in the past';
        }

        return $errors;
    }
}
