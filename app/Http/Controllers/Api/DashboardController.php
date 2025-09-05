<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class DashboardController extends Controller
{
    use ApiMessages;

    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Get dashboard data for authenticated user
     */
    public function dashboardData(Request $request): JsonResponse
    {
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
                    'average_rating' => round($user->reviews()->avg('rating'), 1),
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

            // Quick Actions Data
            $quickActions = [
                'can_borrow' => $user->canBorrowMoreBooks() && ! $user->hasOverdueBooks(),
                'overdue_count' => $user->activeBorrows()->where('due_date', '<', now())->count(),
                'ready_reservations' => $user->reservations()
                    ->whereNull('fulfilled_by_borrow_id')
                    ->whereNull('canceled_at')
                    ->count(),
            ];

            // Recommendations
            $recommendations = $this->dashboardService->getPersonalizedRecommendations($user->id, 6);

            return $this->successResponse('Dashboard data retrieved successfully', [
                'personal_stats' => $personalStats,
                'recent_activity' => $recentActivity,
                'quick_actions' => $quickActions,
                'recommendations' => $recommendations,
                'greeting' => $this->dashboardService->getPersonalizedGreeting($user),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load dashboard data: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to load dashboard data');
        }
    }

    /**
     * Get user notifications
     */
    public function notifications(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $notifications = $this->dashboardService->getUserNotifications($user->id);

            return $this->successResponse('Notifications retrieved successfully', $notifications);

        } catch (Exception $e) {
            Log::error('Failed to load notifications: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to load notifications');
        }
    }

    /**
     * Get quick stats for dashboard widgets
     */
    public function quickStats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $stats = [
                'active_borrows' => $user->activeBorrows()->count(),
                'overdue_books' => $user->activeBorrows()->where('due_date', '<', now())->count(),
                'due_soon' => $user->activeBorrows()->where('due_date', '<=', now()->addDays(3))->count(),
                'active_reservations' => $user->reservations()
                    ->whereNull('fulfilled_by_borrow_id')
                    ->whereNull('canceled_at')
                    ->count(),
                'ready_reservations' => $user->reservations()
                    ->whereNotNull('available_at')
                    ->whereNull('fulfilled_by_borrow_id')
                    ->whereNull('canceled_at')
                    ->count(),
                'outstanding_fine_amount' => $user->getOutstandingFineAmount(),
                'reading_lists' => $user->readingLists()->count(),
            ];

            return $this->successResponse('Quick stats retrieved successfully', $stats);

        } catch (Exception $e) {
            Log::error('Failed to load quick stats: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to load quick stats');
        }
    }
}
