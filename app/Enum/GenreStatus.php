<?php

declare(strict_types=1);

namespace App\Enum;

enum GenreStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public static function enumValues(): array
    {
        return [
            'ACTIVE' => 1,
            'INACTIVE' => 0,
        ];
    }

    public static function labelValue(): string
    {
        return __('Status');
    }
}
