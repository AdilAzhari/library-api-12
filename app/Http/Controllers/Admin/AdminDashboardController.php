<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reservation;
use App\Models\User;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'active_borrowings' => Borrow::active()->count(),
            'overdue_books' => Borrow::overdue()->count(),
            'active_reservations' => Reservation::active()->count(),
        ];

        $charts = [
            'borrowings' => $this->getBorrowingsChartData(),
            'genres' => $this->getPopularGenresData()
        ];
        $recentActivities = $this->getRecentActivities();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'charts' => $charts,
            'recentActivities' => $recentActivities
        ]);
    }

    protected function getBorrowingsChartData()
    {
        $startDate = now()->subMonths(6);
        $endDate = now();

        $borrowingsData = Borrow::selectRaw('DATE_FORMAT(borrowed_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $returnsData = Borrow::selectRaw('DATE_FORMAT(returned_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('returned_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $borrowings = [];
        $returns = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $month = $current->format('Y-m');
            $monthName = $current->format('M Y');

            $labels[] = $monthName;
            $borrowings[] = $borrowingsData->firstWhere('month', $month)?->count ?? 0;
            $returns[] = $returnsData->firstWhere('month', $month)?->count ?? 0;

            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Borrowings',
                    'data' => $borrowings,
                    'backgroundColor' => '#2c3e50'
                ],
                [
                    'label' => 'Returns',
                    'data' => $returns,
                    'backgroundColor' => '#3498db'
                ]
            ]
        ];
    }

    protected function getPopularGenresData()
    {
        return Book::withCount('borrows')
            ->with('genre')
            ->orderBy('borrows_count', 'desc')
            ->limit(5)
            ->get()
            ->groupBy('genre.name')
            ->map(function ($books, $genre) {
                return [
                    'name' => $genre ?: 'Uncategorized',
                    'count' => $books->sum('borrows_count')
                ];
            })
            ->values()
            ->toArray();
    }

    protected function getRecentActivities()
    {
        $borrowings = Borrow::with(['book', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($borrowing) {
                return [
                    'type' => $borrowing->returned_at ? 'return' : 'borrow',
                    'description' => $borrowing->returned_at
                        ? 'Returned book'
                        : 'Borrowed book',
                    'user' => $borrowing->user->name,
                    'datetime' => $borrowing->returned_at
                        ? $borrowing->returned_at->toISOString()
                        : $borrowing->borrowed_at->toISOString(),
                    'time' => $borrowing->returned_at
                        ? $borrowing->returned_at->diffForHumans()
                        : $borrowing->borrowed_at->diffForHumans()
                ];
            });

        $reservations = Reservation::with(['book', 'user'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($reservation) {
                return [
                    'type' => 'book',
                    'description' => $reservation->isFulfilled()
                        ? 'Fulfilled reservation'
                        : ($reservation->isCanceled()
                            ? 'Canceled reservation'
                            : 'Created reservation'),
                    'user' => $reservation->user->name,
                    'datetime' => $reservation->updated_at->toISOString(),
                    'time' => $reservation->updated_at->diffForHumans()
                ];
            });

        $users = User::latest()
            ->limit(2)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'description' => 'Registered',
                    'user' => $user->name,
                    'datetime' => $user->created_at->toISOString(),
                    'time' => $user->created_at->diffForHumans()
                ];
            });

        return $borrowings->concat($reservations)->concat((array)$users)
            ->sortByDesc('datetime')
            ->values()
            ->take(8)
            ->toArray();
    }
}
