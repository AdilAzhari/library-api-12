<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class DashboardService
{
    /**
     * Get personalized book recommendations for user
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 6): Collection
    {
        $user = User::query()->find($userId);

        // Get user's borrowing history to understand preferences
        $borrowedGenres = $user->borrows()
            ->join('books', 'borrows.book_id', '=', 'books.id')
            ->join('genres', 'books.genre_id', '=', 'genres.id')
            ->select('genres.id', 'genres.name')
            ->groupBy('genres.id', 'genres.name')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(3)
            ->pluck('genres.id');

        // Get highly rated books in user's preferred genres that they haven't borrowed
        $recommendations = Book::query()->whereIn('genre_id', $borrowedGenres)
            ->where('average_rating', '>=', 4.0)
            ->whereNotIn('id', function ($query) use ($userId): void {
                $query->select('book_id')
                    ->from('borrows')
                    ->where('user_id', $userId);
            })
            ->available()
            ->with(['genre', 'reviews', 'activeBorrow', 'activeReservation'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // If not enough recommendations, fill with popular books
        if ($recommendations->count() < $limit) {
            $additional = Book::query()->where('average_rating', '>=', 4.0)
                ->whereNotIn('id', $recommendations->pluck('id'))
                ->whereNotIn('id', function ($query) use ($userId): void {
                    $query->select('book_id')
                        ->from('borrows')
                        ->where('user_id', $userId);
                })
                ->available()
                ->with(['activeBorrow', 'activeReservation'])
                ->orderByDesc('average_rating')
                ->limit($limit - $recommendations->count())
                ->get();

            $recommendations = $recommendations->merge($additional);
        }

        return $recommendations;
    }

    /**
     * Get user notifications (overdue books, ready reservations, etc.)
     */
    public function getUserNotifications(int $userId): array
    {
        $user = User::query()->find($userId);
        $notifications = [];

        // Overdue books
        $overdueBooks = $user->activeBorrows()
            ->where('due_date', '<', now())
            ->with(['book.activeBorrow', 'book.activeReservation'])
            ->get();

        if ($overdueBooks->count() > 0) {
            $notifications[] = [
                'type' => 'overdue',
                'title' => 'Overdue Books',
                'message' => $overdueBooks->count() === 1
                    ? 'You have 1 overdue book'
                    : "You have {$overdueBooks->count()} overdue books",
                'action_url' => route('borrows.index', ['status' => 'overdue']),
                'priority' => 'high',
                'data' => $overdueBooks,
            ];
        }

        // Ready reservations (active reservations)
        $readyReservations = $user->reservations()
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->where('expires_at', '>', now())
            ->with(['book.activeBorrow', 'book.activeReservation'])
            ->get();

        if ($readyReservations->count() > 0) {
            $notifications[] = [
                'type' => 'reservation_ready',
                'title' => 'Reserved Books Ready',
                'message' => $readyReservations->count() === 1
                    ? '1 reserved book is ready for pickup'
                    : "{$readyReservations->count()} reserved books are ready for pickup",
                'action_url' => route('reservations.index'),
                'priority' => 'medium',
                'data' => $readyReservations,
            ];
        }

        // Due soon (within 3 days)
        $dueSoon = $user->activeBorrows()
            ->whereBetween('due_date', [now(), now()->addDays(3)])
            ->with(['book.activeBorrow', 'book.activeReservation'])
            ->get();

        if ($dueSoon->count() > 0) {
            $notifications[] = [
                'type' => 'due_soon',
                'title' => 'Books Due Soon',
                'message' => $dueSoon->count() === 1
                    ? '1 book is due within 3 days'
                    : "{$dueSoon->count()} books are due within 3 days",
                'action_url' => route('borrows.index'),
                'priority' => 'medium',
                'data' => $dueSoon,
            ];
        }

        // Outstanding fines
        $outstandingFines = $user->getOutstandingFineAmount();
        if ($outstandingFines > 0) {
            $notifications[] = [
                'type' => 'outstanding_fines',
                'title' => 'Outstanding Fines',
                'message' => 'You have $'.number_format($outstandingFines, 2).' in outstanding fines',
                'action_url' => route('fines.index'),
                'priority' => 'medium',
                'data' => ['amount' => $outstandingFines],
            ];
        }

        return $notifications;
    }

    /**
     * Generate personalized greeting based on time and user activity
     */
    public function getPersonalizedGreeting(User $user): string
    {
        $hour = Carbon::now()->hour;
        $timeGreeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening'
        };

        $personalizedPart = '';

        // Add activity-based greeting
        $activeBorrowsCount = $user->activeBorrows()->count();
        $readingListsCount = $user->readingLists()->count();

        if ($activeBorrowsCount > 0) {
            $personalizedPart = $activeBorrowsCount === 1
                ? ' You have 1 book to enjoy!'
                : " You have {$activeBorrowsCount} books to enjoy!";
        } elseif ($readingListsCount > 0) {
            $personalizedPart = ' Ready to discover your next great read?';
        } else {
            $personalizedPart = ' Welcome to your library dashboard!';
        }

        return $timeGreeting.', '.$user->name.'!'.$personalizedPart;
    }

    /**
     * Get library-wide statistics for dashboard
     */
    public function getLibraryStats(): array
    {
        return [
            'total_books' => Book::count(),
            'available_books' => Book::available()->count(),
            'total_members' => User::query()->where('role', '!=', 'admin')->count(),
            'active_borrows' => DB::table('borrows')->whereNull('returned_at')->count(),
            'books_borrowed_today' => DB::table('borrows')
                ->whereDate('borrowed_at', today())
                ->count(),
        ];
    }
}
