<?php

namespace App\Policies;

use App\Models\Borrow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BorrowPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Borrow $borrow): bool
    {
        return $user->is_admin || $borrow->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function renew(User $user, Borrow $borrow): bool
    {
        return $borrow->user_id === $user->id
            && $borrow->returned_at === null
            && $borrow->renewal_count < config('library.max_renewals', 2)
            && ! $borrow->isOverdue();
    }

    public function return(User $user, Borrow $borrow): bool
    {
        return $user->is_admin || $borrow->user_id === $user->id;
    }
}
