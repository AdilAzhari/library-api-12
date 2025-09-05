<?php

declare(strict_types=1);

namespace App\Enum;

enum LibraryCardStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case EXPIRED = 'expired';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::EXPIRED => 'Expired',
            self::LOST => 'Lost',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'warning',
            self::EXPIRED => 'secondary',
            self::LOST => 'danger',
            self::CANCELLED => 'dark',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Card is active and can be used',
            self::SUSPENDED => 'Card is temporarily suspended',
            self::EXPIRED => 'Card has expired and needs renewal',
            self::LOST => 'Card has been reported lost',
            self::CANCELLED => 'Card has been permanently cancelled',
        };
    }

    public function canBorrow(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canRenew(): bool
    {
        return in_array($this, [self::ACTIVE, self::EXPIRED]);
    }

    public function isValid(): bool
    {
        return $this === self::ACTIVE;
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'check-circle',
            self::SUSPENDED => 'pause-circle',
            self::EXPIRED => 'clock',
            self::LOST => 'exclamation-triangle',
            self::CANCELLED => 'x-circle',
        };
    }
}
