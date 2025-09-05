<?php

declare(strict_types=1);

namespace App\Enum;

enum ReservationStatus: string
{
    case ACTIVE = 'active';
    case FULFILLED = 'fulfilled';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::FULFILLED => 'Fulfilled',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'primary',
            self::FULFILLED => 'success',
            self::EXPIRED => 'warning',
            self::CANCELLED => 'secondary',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Reservation is active and waiting',
            self::FULFILLED => 'Reservation has been fulfilled by borrowing',
            self::EXPIRED => 'Reservation has expired',
            self::CANCELLED => 'Reservation was cancelled',
        };
    }

    public function canCancel(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canFulfill(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'clock',
            self::FULFILLED => 'check-circle',
            self::EXPIRED => 'x-circle',
            self::CANCELLED => 'slash',
        };
    }
}
