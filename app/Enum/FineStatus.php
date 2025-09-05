<?php

declare(strict_types=1);

namespace App\Enum;

enum FineStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case WAIVED = 'waived';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::PARTIAL => 'Partial Payment',
            self::WAIVED => 'Waived',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::PARTIAL => 'info',
            self::WAIVED => 'secondary',
            self::CANCELLED => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::PARTIAL]);
    }

    public function isResolved(): bool
    {
        return in_array($this, [self::PAID, self::WAIVED, self::CANCELLED]);
    }
}
