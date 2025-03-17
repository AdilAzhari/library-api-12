<?php

namespace App\Enum;

enum GenreStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';

    public static function enumValues(): array
    {
        return [
            'ACTIVE' => __('Active'),
            'INACTIVE' => __('Inactive'),
        ];
    }

    public static function labelValue(): string
    {
        return __('Status');
    }
}
