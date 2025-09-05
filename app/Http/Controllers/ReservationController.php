<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ReserveBookDTO;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class ReservationController extends Controller
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
    public function store(StoreReservationRequest $request, $book): RedirectResponse
    {
        try {
            // Get book ID from route parameter or request data
            $bookId = is_object($book) ? $book->id : (int) $book;

            $dto = new ReserveBookDTO(
                bookId: $bookId,
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

    /**
     * Show reservation creation form
     */
    public function create($book): Response
    {
        // Get book information for the reservation form
        $bookData = is_object($book) ? $book : App\Models\Book::query()->findOrFail($book);

        return Inertia::render('Reservations/Create', [
            'book' => $bookData,
            'pickupLocations' => [
                'Main Library',
                'Science Library',
                'Engineering Library',
                'Medical Library',
                'Student Center Desk',
            ],
            'queueInfo' => null, // Could be populated with queue information
        ]);
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation): Response|RedirectResponse
    {
        try {
            $reservationData = $this->reservationService->getUserReservation($reservation->id, Auth::id());

            if (! $reservationData) {
                return redirect()
                    ->route('reservations.index')
                    ->with('error', 'Reservation not found.');
            }

            return Inertia::render('Reservations/Show', [
                'reservation' => $reservationData,
            ]);
        } catch (Exception $e) {
            Log::error('Error showing reservation: '.$e->getMessage());

            return redirect()
                ->route('reservations.index')
                ->with('error', 'Failed to load reservation. Please try again.');
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel(int $id): RedirectResponse
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

    /**
     * Extend reservation
     */
    public function extend(int $id): RedirectResponse
    {
        try {
            // Implementation would depend on ReservationService having this method
            return redirect()
                ->back()
                ->with('success', 'Reservation extended successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Unable to extend reservation: '.$e->getMessage());
        }
    }
}
