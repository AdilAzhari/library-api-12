<?php

namespace App\Enum;

enum UserRoles: string
{
    case Admin = 'Admin';
    case Librarian = 'Librarian';
    case Member = 'Member';

    public static function values(): array
    {
        return [
            'Admin' => 'Admin',
            'Librarian' => 'Librarian',
            'Member' => 'Member',
        ];
    }

    public static function labelValue(): string
    {
        return __('UserRoles');
    }
}
