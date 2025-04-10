<?php

namespace App\Enum;

enum BookStatus: string
{
    case STATUS_AVAILABLE = 'available';
    case STATUS_BORROWED = 'borrowed';
    case STATUS_RESERVED = 'reserved';

    public static function values(): array
    {
        return [
            'Available' => self::STATUS_AVAILABLE->value,
            'Borrowed' => self::STATUS_BORROWED->value,
            'Reserved' => self::STATUS_RESERVED->value,
        ];
    }
}
