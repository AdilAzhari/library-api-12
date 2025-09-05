<?php

declare(strict_types=1);

namespace App\Enum;

enum BookStatus: string
{
    case AVAILABLE = 'available';
    case BORROWED = 'borrowed';
    case RESERVED = 'reserved';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::BORROWED => 'Borrowed',
            self::RESERVED => 'Reserved',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::BORROWED => 'warning',
            self::RESERVED => 'info',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Book is available for borrowing',
            self::BORROWED => 'Book is currently borrowed',
            self::RESERVED => 'Book is reserved for another user',
        };
    }

    public function canBeBorrowed(): bool
    {
        return $this === self::AVAILABLE;
    }

    public function canBeReserved(): bool
    {
        return in_array($this, [self::BORROWED, self::RESERVED]);
    }

    public function icon(): string
    {
        return match ($this) {
            self::AVAILABLE => 'check-circle',
            self::BORROWED => 'user',
            self::RESERVED => 'bookmark',
        };
    }
}
