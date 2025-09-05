<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display user dashboard with personal stats and quick actions
     */
    public function index(): Response|RedirectResponse
    {
        Log::info('DashboardController::index - Loading user dashboard', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? null,
        ]);

        try {
            $user = Auth::user();

            // Personal Statistics
            $personalStats = [
                'active_borrows' => [
                    'count' => $user->activeBorrows()->count(),
                    'limit' => config('library.settings.max_loans_per_user', 5),
                ],
                'reading_lists' => [
                    'count' => $user->readingLists()->count(),
                ],
                'reviews_written' => [
                    'count' => $user->reviews()->count(),
                    'average_rating' => round((float) ($user->reviews()->avg('rating') ?? 0), 1),
                ],
                'outstanding_fines' => [
                    'amount' => $user->getOutstandingFineAmount(),
                    'count' => $user->unpaidFines()->count(),
                ],
                'library_card' => $user->activeLibraryCard,
            ];

            // Recent Activity
            $recentActivity = [
                'recently_borrowed' => $user->borrows()
                    ->with(['book.activeBorrow', 'book.activeReservation'])
                    ->latest('borrowed_at')
                    ->limit(3)
                    ->get(),
                'recently_reviewed' => $user->reviews()
                    ->with(['book.activeBorrow', 'book.activeReservation'])
                    ->latest('created_at')
                    ->limit(3)
                    ->get(),
                'upcoming_due_dates' => $user->activeBorrows()
                    ->with(['book.activeBorrow', 'book.activeReservation'])
                    ->where('due_date', '<=', now()->addDays(7))
                    ->orderBy('due_date')
                    ->limit(5)
                    ->get(),
            ];

            // Notifications
            $notifications = $this->dashboardService->getUserNotifications($user->id);

            // Quick Actions Data
            $quickActions = [
                'can_borrow' => $user->canBorrowMoreBooks() && ! $user->hasOverdueBooks(),
                'overdue_count' => $user->activeBorrows()->where('due_date', '<', now())->count(),
                'ready_reservations' => $user->reservations()
                    ->whereNull('fulfilled_by_borrow_id')
                    ->whereNull('canceled_at')
                    ->count(),
            ];

            // Recommendations (books user might like)
            $recommendations = $this->dashboardService->getPersonalizedRecommendations($user->id, 6);

            Log::info('DashboardController::index - Dashboard loaded successfully', [
                'user_id' => $user->id,
                'active_borrows_count' => $personalStats['active_borrows']['count'],
                'notifications_count' => count($notifications),
                'recommendations_count' => count($recommendations),
            ]);

            return Inertia::render('Dashboard', [
                'personalStats' => $personalStats,
                'recentActivity' => $recentActivity,
                'notifications' => $notifications,
                'quickActions' => $quickActions,
                'recommendations' => $recommendations,
                'greeting' => $this->dashboardService->getPersonalizedGreeting($user),
            ]);

        } catch (Exception $e) {
            Log::error('DashboardController::index - Failed to load dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('books.index')
                ->with('error', 'Unable to load dashboard. Please try again.');
        }
    }
}
