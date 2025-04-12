<?php

namespace App\Enum;

enum BookStatus: string
{
    case STATUS_AVAILABLE = 'Available';
    case STATUS_BORROWED = 'Borrowed';
    case STATUS_RESERVED = 'Reserved';

    public static function values(): array
    {
        return [
            'Available' => self::STATUS_AVAILABLE->value,
            'Borrowed' => self::STATUS_BORROWED->value,
            'Reserved' => self::STATUS_RESERVED->value,
        ];
    }
}
