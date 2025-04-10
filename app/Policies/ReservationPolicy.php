<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id || $user->isAdmin;
    }

    public function create(User $user): true
    {
        return true; // Any authenticated user can create reservations
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            && $reservation->expires_at > now()
            && !$reservation->isFulfilled();
    }
}
