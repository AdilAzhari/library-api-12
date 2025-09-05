<?php

declare(strict_types=1);

namespace App\Enum;

enum BorrowStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'secondary',
            self::OVERDUE => 'danger',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Book is currently borrowed and active',
            self::COMPLETED => 'Book has been returned successfully',
            self::CANCELLED => 'Borrow request was cancelled',
            self::OVERDUE => 'Book return is past due date',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::ACTIVE, self::OVERDUE]);
    }

    public function canReturn(): bool
    {
        return in_array($this, [self::ACTIVE, self::OVERDUE]);
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'book-open',
            self::COMPLETED => 'check-circle',
            self::CANCELLED => 'x-circle',
            self::OVERDUE => 'exclamation-triangle',
        };
    }
}
