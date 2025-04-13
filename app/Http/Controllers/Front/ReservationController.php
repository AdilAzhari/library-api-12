<?php

namespace App\Http\Controllers\Front;

use App\DTO\ReserveBookDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Services\ReservationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService
    ) {}

    /**
     * Display a listing of reservations.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $filters = $request->only(['status', 'book_id']);
            $sortBy = $request->input('sort_by', 'reserved_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $perPage = $request->input('per_page', 10);

            $reservations = $this->reservationService->getUserReservations(
                Auth::id(),
                $filters,
                $sortBy,
                $sortOrder,
                $perPage
            );

            return Inertia::render('Reservation/Index', [
                'reservations' => $reservations->all(),
                'filters' => $filters,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
            ]);
        } catch (Exception $e) {
            Log::error('Error listing reservations: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to load reservations. Please try again.');
        }
    }

    /**
     * Store a newly created reservation.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        try {
            $dto = new ReserveBookDTO(
                bookId: $request->validated('book_id'),
                userId: Auth::id()
            );

            $reservation = $this->reservationService->createReservation($dto);

            return redirect()
                ->route('reservations.index')
                ->with('success', 'Book reserved successfully. Expires on '.
                    $reservation->expires_at->format('M j, Y'));
        } catch (Exception $e) {
            Log::error('Error creating reservation: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel the specified reservation.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->reservationService->cancelReservation($id, Auth::id());

            return redirect()
                ->route('reservations.index')
                ->with('success', 'Reservation cancelled successfully.');
        } catch (Exception $e) {
            Log::error('Error cancelling reservation: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
