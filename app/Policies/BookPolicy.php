<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{

    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('librarian');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('librarian');
    }
}
