<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Fine;
use App\Models\LibraryCard;
use App\Models\ReadingList;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class LibraryStatisticsService
{
    /**
     * Get comprehensive library dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return [
            'overview' => $this->getOverviewStats(),
            'books' => $this->getBookStats(),
            'users' => $this->getUserStats(),
            'borrowing' => $this->getBorrowingStats(),
            'fines' => $this->getFineStats(),
            'reviews' => $this->getReviewStats(),
            'reading_lists' => $this->getReadingListStats(),
            'library_cards' => $this->getLibraryCardStats(),
            'trends' => $this->getTrendStats(),
        ];
    }

    /**
     * Get overview statistics
     */
    public function getOverviewStats(): array
    {
        return [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'active_borrows' => Borrow::active()->count(),
            'total_reviews' => Review::approved()->count(),
            'pending_reservations' => Reservation::active()->count(),
            'overdue_books' => Borrow::overdue()->count(),
            'outstanding_fines' => Fine::unpaid()->count(),
            'library_utilization' => $this->getLibraryUtilization(),
        ];
    }

    /**
     * Get book-related statistics
     */
    public function getBookStats(): array
    {
        $totalBooks = Book::count();
        $availableBooks = Book::query()->where('status', 'available')->count();
        $borrowedBooks = Book::query()->where('status', 'borrowed')->count();

        return [
            'total_books' => $totalBooks,
            'available_books' => $availableBooks,
            'borrowed_books' => $borrowedBooks,
            'reserved_books' => Book::query()->where('status', 'reserved')->count(),
            'most_popular_books' => $this->getMostPopularBooks(),
            'most_reviewed_books' => $this->getMostReviewedBooks(),
            'books_by_genre' => $this->getBooksByGenre(),
            'average_rating' => Book::query()->where('average_rating', '>', 0)->avg('average_rating'),
            'utilization_rate' => $totalBooks > 0 ? round(($borrowedBooks / $totalBooks) * 100, 2) : 0,
        ];
    }

    /**
     * Get user-related statistics
     */
    public function getUserStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => $this->getActiveUsersCount(),
            'new_users_this_month' => User::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'users_with_active_borrows' => App\Models\User::query()->whereHas('activeBorrows')->count(),
            'users_with_overdue_books' => App\Models\User::query()->whereHas('activeBorrows', function ($query): void {
                $query->where('due_date', '<', now());
            })->count(),
            'users_with_outstanding_fines' => App\Models\User::query()->whereHas('unpaidFines')->count(),
            'average_books_per_user' => $this->getAverageBooksPerUser(),
            'most_active_users' => $this->getMostActiveUsers(),
        ];
    }

    /**
     * Get borrowing statistics
     */
    public function getBorrowingStats(): array
    {
        $activeBorrows = Borrow::active()->count();
        $totalBorrows = Borrow::count();
        $overdueBorrows = Borrow::overdue()->count();

        return [
            'active_borrows' => $activeBorrows,
            'total_borrows' => $totalBorrows,
            'overdue_borrows' => $overdueBorrows,
            'borrows_this_month' => Borrow::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'returns_this_month' => Borrow::whereNotNull('returned_at')
                ->where('returned_at', '>=', now()->startOfMonth())->count(),
            'average_borrow_duration' => $this->getAverageBorrowDuration(),
            'on_time_return_rate' => $this->getOnTimeReturnRate(),
            'borrowing_trends' => $this->getBorrowingTrends(),
        ];
    }

    /**
     * Get fine statistics
     */
    public function getFineStats(): array
    {
        return Fine::getStatistics();
    }

    /**
     * Get review statistics
     */
    public function getReviewStats(): array
    {
        $totalReviews = Review::count();
        $approvedReviews = Review::approved()->count();

        return [
            'total_reviews' => $totalReviews,
            'approved_reviews' => $approvedReviews,
            'pending_reviews' => Review::pending()->count(),
            'featured_reviews' => Review::featured()->count(),
            'reviews_this_month' => Review::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            'average_rating' => Review::approved()->avg('rating'),
            'approval_rate' => $totalReviews > 0 ? round(($approvedReviews / $totalReviews) * 100, 2) : 0,
            'rating_distribution' => $this->getRatingDistribution(),
        ];
    }

    /**
     * Get reading list statistics
     */
    public function getReadingListStats(): array
    {
        return [
            'total_lists' => ReadingList::count(),
            'public_lists' => ReadingList::public()->count(),
            'private_lists' => ReadingList::private()->count(),
            'average_books_per_list' => $this->getAverageBooksPerReadingList(),
            'most_popular_list_books' => ReadingList::getPopularBooks(),
            'lists_created_this_month' => ReadingList::query()->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get library card statistics
     */
    public function getLibraryCardStats(): array
    {
        return LibraryCard::getStatistics();
    }

    /**
     * Get trend statistics for charts
     */
    public function getTrendStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'daily_borrows' => $this->getDailyBorrows($startDate),
            'daily_returns' => $this->getDailyReturns($startDate),
            'daily_registrations' => $this->getDailyRegistrations($startDate),
            'daily_reviews' => $this->getDailyReviews($startDate),
            'monthly_comparison' => $this->getMonthlyComparison(),
        ];
    }

    /**
     * Get export data for reports
     */
    public function getExportData(string $type, array $filters = []): array
    {
        return match ($type) {
            'books' => $this->exportBooksData($filters),
            'users' => $this->exportUsersData($filters),
            'borrows' => $this->exportBorrowsData($filters),
            'fines' => $this->exportFinesData($filters),
            'reviews' => $this->exportReviewsData($filters),
            default => [],
        };
    }

    /**
     * Get library utilization percentage
     */
    private function getLibraryUtilization(): float
    {
        $totalBooks = Book::count();
        if ($totalBooks === 0) {
            return 0;
        }

        $borrowedBooks = Borrow::active()->count();

        return round(($borrowedBooks / $totalBooks) * 100, 2);
    }

    /**
     * Get most popular books based on borrow count
     */
    private function getMostPopularBooks(int $limit = 10): Collection
    {
        return App\Models\Book::query()->withCount('borrows')
            ->orderByDesc('borrows_count')
            ->limit($limit)
            ->get(['id', 'title', 'author', 'isbn', 'borrows_count']);
    }

    /**
     * Get most reviewed books
     */
    private function getMostReviewedBooks(int $limit = 10): Collection
    {
        return App\Models\Book::query()->withCount(['reviews' => function ($query): void {
            $query->approved();
        }])
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get(['id', 'title', 'author', 'average_rating', 'reviews_count']);
    }

    /**
     * Get books grouped by genre
     */
    private function getBooksByGenre(): array
    {
        return Book::select('genre_id', DB::raw('count(*) as count'))
            ->groupBy('genre_id')
            ->with('genre:id,name')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->genre->name ?? 'Unknown' => $item->count])
            ->toArray();
    }

    /**
     * Get active users count (users who borrowed in last 90 days)
     */
    private function getActiveUsersCount(): int
    {
        return App\Models\User::query()->whereHas('borrows', function ($query): void {
            $query->where('created_at', '>=', now()->subDays(90));
        })->count();
    }

    /**
     * Get average books per user
     */
    private function getAverageBooksPerUser(): float
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) {
            return 0;
        }

        $totalBorrows = Borrow::count();

        return round($totalBorrows / $totalUsers, 2);
    }

    /**
     * Get most active users
     */
    private function getMostActiveUsers(int $limit = 10): Collection
    {
        return App\Models\User::query()->withCount('borrows')
            ->orderByDesc('borrows_count')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'borrows_count']);
    }

    /**
     * Get average borrow duration in days
     */
    private function getAverageBorrowDuration(): float
    {
        $avgDuration = Borrow::whereNotNull('returned_at')
            ->selectRaw('AVG(DATEDIFF(returned_at, created_at)) as avg_duration')
            ->first();

        return round($avgDuration->avg_duration ?? 0, 1);
    }

    /**
     * Get on-time return rate percentage
     */
    private function getOnTimeReturnRate(): float
    {
        $totalReturned = Borrow::whereNotNull('returned_at')->count();
        if ($totalReturned === 0) {
            return 100;
        }

        $onTimeReturns = Borrow::whereNotNull('returned_at')
            ->whereRaw('returned_at <= due_date')
            ->count();

        return round(($onTimeReturns / $totalReturned) * 100, 2);
    }

    /**
     * Get borrowing trends for the last 12 months
     */
    private function getBorrowingTrends(): array
    {
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trends[] = [
                'month' => $month->format('M Y'),
                'borrows' => Borrow::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get rating distribution for reviews
     */
    private function getRatingDistribution(): array
    {
        $distribution = [];
        for ($rating = 1; $rating <= 5; $rating++) {
            $distribution[$rating] = Review::approved()
                ->where('rating', $rating)
                ->count();
        }

        return $distribution;
    }

    /**
     * Get average books per reading list
     */
    private function getAverageBooksPerReadingList(): float
    {
        $totalLists = ReadingList::count();
        if ($totalLists === 0) {
            return 0;
        }

        $totalBooks = DB::table('reading_list_books')->count();

        return round($totalBooks / $totalLists, 1);
    }

    /**
     * Get daily borrows for trend analysis
     */
    private function getDailyBorrows(Carbon $startDate): array
    {
        return Borrow::query()->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->date => $item->count])
            ->toArray();
    }

    /**
     * Get daily returns for trend analysis
     */
    private function getDailyReturns(Carbon $startDate): array
    {
        return Borrow::whereNotNull('returned_at')
            ->where('returned_at', '>=', $startDate)
            ->selectRaw('DATE(returned_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->date => $item->count])
            ->toArray();
    }

    /**
     * Get daily user registrations
     */
    private function getDailyRegistrations(Carbon $startDate): array
    {
        return User::query()->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->date => $item->count])
            ->toArray();
    }

    /**
     * Get daily reviews
     */
    private function getDailyReviews(Carbon $startDate): array
    {
        return Review::approved()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->date => $item->count])
            ->toArray();
    }

    /**
     * Get monthly comparison statistics
     */
    private function getMonthlyComparison(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'borrows' => [
                'current' => Borrow::query()->where('created_at', '>=', $currentMonth)->count(),
                'previous' => Borrow::whereBetween('created_at', [
                    $lastMonth, $lastMonth->copy()->endOfMonth(),
                ])->count(),
            ],
            'returns' => [
                'current' => Borrow::whereNotNull('returned_at')
                    ->where('returned_at', '>=', $currentMonth)->count(),
                'previous' => Borrow::whereNotNull('returned_at')
                    ->whereBetween('returned_at', [
                        $lastMonth, $lastMonth->copy()->endOfMonth(),
                    ])->count(),
            ],
            'new_users' => [
                'current' => User::query()->where('created_at', '>=', $currentMonth)->count(),
                'previous' => User::whereBetween('created_at', [
                    $lastMonth, $lastMonth->copy()->endOfMonth(),
                ])->count(),
            ],
        ];
    }

    /**
     * Export books data
     */
    private function exportBooksData(array $filters): array
    {
        return App\Models\Book::query()->with(['genre', 'borrows', 'reviews'])
            ->get()
            ->map(fn ($book) => [
                'ID' => $book->id,
                'Title' => $book->title,
                'Author' => $book->author,
                'ISBN' => $book->isbn,
                'Genre' => $book->genre->name ?? 'N/A',
                'Status' => $book->status,
                'Total Borrows' => $book->borrows_count ?? 0,
                'Average Rating' => $book->average_rating ?? 'N/A',
                'Publication Year' => $book->publication_year,
                'Created At' => $book->created_at->format('Y-m-d H:i:s'),
            ])
            ->toArray();
    }

    /**
     * Export users data
     */
    private function exportUsersData(array $filters): array
    {
        return App\Models\User::query()->with(['borrows', 'reviews', 'fines'])
            ->get()
            ->map(fn ($user) => [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Role' => $user->role,
                'Total Borrows' => $user->borrows_count ?? 0,
                'Active Borrows' => $user->activeBorrows()->count(),
                'Outstanding Fines' => $user->hasOutstandingFines() ? 'Yes' : 'No',
                'Fine Amount' => $user->getOutstandingFineAmount(),
                'Joined Date' => $user->created_at->format('Y-m-d'),
            ])
            ->toArray();
    }

    /**
     * Export borrows data
     */
    private function exportBorrowsData(array $filters): array
    {
        return App\Models\Borrow::query()->with(['user', 'book'])
            ->get()
            ->map(fn ($borrow) => [
                'ID' => $borrow->id,
                'User' => $borrow->user->name,
                'Book' => $borrow->book->title,
                'Borrowed Date' => $borrow->created_at->format('Y-m-d'),
                'Due Date' => $borrow->due_date->format('Y-m-d'),
                'Returned Date' => $borrow->returned_at?->format('Y-m-d') ?? 'Not Returned',
                'Status' => $borrow->status,
                'Days Borrowed' => $borrow->returned_at
                    ? $borrow->created_at->diffInDays($borrow->returned_at)
                    : $borrow->created_at->diffInDays(now()),
            ])
            ->toArray();
    }

    /**
     * Export fines data
     */
    private function exportFinesData(array $filters): array
    {
        return App\Models\Fine::query()->with(['user', 'book'])
            ->get()
            ->map(fn ($fine) => [
                'ID' => $fine->id,
                'User' => $fine->user->name,
                'Book' => $fine->book->title,
                'Amount' => $fine->formatted_amount,
                'Reason' => ucwords(str_replace('_', ' ', $fine->reason)),
                'Status' => $fine->status_label,
                'Due Date' => $fine->due_date->format('Y-m-d'),
                'Paid Date' => $fine->paid_at?->format('Y-m-d') ?? 'Not Paid',
                'Balance Due' => '$'.number_format($fine->balance_due, 2),
                'Created Date' => $fine->created_at->format('Y-m-d'),
            ])
            ->toArray();
    }

    /**
     * Export reviews data
     */
    private function exportReviewsData(array $filters): array
    {
        return App\Models\Review::query()->with(['user', 'book'])
            ->get()
            ->map(fn ($review) => [
                'ID' => $review->id,
                'User' => $review->user->name,
                'Book' => $review->book->title,
                'Rating' => $review->rating,
                'Comment' => $review->comment,
                'Status' => $review->is_approved ? 'Approved' : 'Pending',
                'Featured' => $review->is_featured ? 'Yes' : 'No',
                'Helpful Count' => $review->helpful_count,
                'Review Date' => $review->created_at->format('Y-m-d'),
            ])
            ->toArray();
    }
}
