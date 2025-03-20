<?php

namespace App\Enum;

enum UserRoles: string
{
    case Admin = 'Admin';
    case librarian = 'librarian';
    case member = 'member';

    public static function values(): array
    {
        return [
            'Admin' => 'Admin',
            'librarian' => 'librarian',
            'member' => 'member',
        ];
    }

    public static function labelValue(): string
    {
        return __('UserRoles');
    }
}
