<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enum\BookStatus;
use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Reservation;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminReservationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => Reservation::with(['book', 'user'])
                ->when(request('search'), function ($query, $search) {
                    $query->whereHas('book', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    })->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                })
                ->when(request('status'), function ($query, $status) {
                    if ($status === 'active') {
                        $query->active();
                    } elseif ($status === 'expired') {
                        $query->expired();
                    } elseif ($status === 'fulfilled') {
                        $query->fulfilled();
                    } elseif ($status === 'canceled') {
                        $query->whereNotNull('canceled_at');
                    }
                })
                ->latest()
                ->paginate(10)
                ->through(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'book' => [
                            'title' => $reservation->book->title,
                            'author' => $reservation->book->author,
                            'cover_image_url' => $reservation->book->cover_image_url,
                        ],
                        'user' => [
                            'name' => $reservation->user->name,
                            'email' => $reservation->user->email,
                        ],
                        'reserved_at' => $reservation->reserved_at,
                        'expires_at' => $reservation->expires_at,
                        'fulfilled_by_borrow_id' => $reservation->fulfilled_by_borrow_id,
                        'canceled_at' => $reservation->canceled_at,
                        'is_fulfilled' => $reservation->isFulfilled(),
                        'is_canceled' => $reservation->isCanceled(),
                        'is_expired' => $reservation->isExpired(),
                        'is_active' => ! $reservation->isFulfilled() &&
                            ! $reservation->isCanceled() &&
                            $reservation->expires_at > now(),
                    ];
                }),
            'filters' => request()->only(['search', 'status']),
        ]);
    }

    public function fulfill(Reservation $reservation)
    {
        $borrowing = Borrow::create([
            'book_id' => $reservation->book_id,
            'user_id' => $reservation->user_id,
            'borrowed_at' => now(),
            'due_date' => request('due_date', Carbon::now()->addWeeks(2)),
        ]);

        $reservation->update([
            'fulfilled_by_borrow_id' => $borrowing->id,
            'fulfilled_at' => now(),
        ]);

        // Update book status
        $reservation->book->update(['status' => BookStatus::STATUS_AVAILABLE->value]);

        return back()->with('success', 'Reservation fulfilled successfully');
    }

    public function cancel(Reservation $reservation)
    {
        $reservation->update([
            'canceled_at' => now(),
        ]);

        // Update book status if it was reserved
        if ($reservation->book->status === 'reserved') {
            $reservation->book->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation canceled successfully');
    }
}
