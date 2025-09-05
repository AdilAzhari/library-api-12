<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRoles: string
{
    case ADMIN = 'Admin';
    case LIBRARIAN = 'Librarian';
    case MEMBER = 'Member';
    case USER = 'User';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return $this->value;
    }

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                'users.*',
                'books.*',
                'borrows.*',
                'reservations.*',
                'fines.*',
                'reviews.*',
                'library_cards.*',
                'reading_lists.*',
                'statistics.*',
                'system.*',
            ],
            self::LIBRARIAN => [
                'books.*',
                'borrows.*',
                'reservations.*',
                'fines.view',
                'fines.create',
                'fines.update',
                'reviews.*',
                'library_cards.*',
                'statistics.view',
            ],
            self::MEMBER, self::USER => [
                'books.view',
                'borrows.own',
                'reservations.own',
                'reviews.own',
                'library_cards.own',
                'reading_lists.own',
            ],
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::LIBRARIAN => 'primary',
            self::MEMBER, self::USER => 'success',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Full system access and management',
            self::LIBRARIAN => 'Library operations and book management',
            self::MEMBER, self::USER => 'Basic library services access',
        };
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions();

        foreach ($permissions as $perm) {
            if ($perm === $permission) {
                return true;
            }

            // Check wildcard permissions
            if (str_ends_with((string) $perm, '.*')) {
                $prefix = str_replace('.*', '', $perm);
                if (str_starts_with($permission, $prefix.'.')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isLibrarian(): bool
    {
        return $this === self::LIBRARIAN;
    }

    public function isUser(): bool
    {
        return in_array($this, [self::MEMBER, self::USER]);
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'shield',
            self::LIBRARIAN => 'user-tie',
            self::MEMBER, self::USER => 'user',
        };
    }
}
