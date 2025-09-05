<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class RecommendationsController extends Controller
{
    /**
     * Display personalized recommendations
     */
    public function index(Request $request): Response
    {
        try {
            $user = Auth::user();
            $perPage = $request->integer('per_page', 12);
            $category = $request->input('category', 'all');

            $recommendations = [
                'personalized' => [],
                'trending' => [],
                'genre_based' => [],
                'similar_users' => [],
                'high_rated' => [],
                'recently_added' => [],
            ];

            // Get personalized recommendations based on user's reading history
            if ($user && $category === 'all' || $category === 'personalized') {
                $recommendations['personalized'] = $this->getPersonalizedRecommendations($user, 8);
            }

            // Get trending books (most borrowed recently)
            if ($category === 'all' || $category === 'trending') {
                $recommendations['trending'] = $this->getTrendingBooks(8);
            }

            // Get genre-based recommendations
            if ($category === 'all' || $category === 'genre_based') {
                $recommendations['genre_based'] = $this->getGenreBasedRecommendations($user, 8);
            }

            // Get recommendations based on similar users
            if ($user && ($category === 'all' || $category === 'similar_users')) {
                $recommendations['similar_users'] = $this->getSimilarUserRecommendations($user, 8);
            }

            // Get high-rated books
            if ($category === 'all' || $category === 'high_rated') {
                $recommendations['high_rated'] = $this->getHighRatedBooks(8);
            }

            // Get recently added popular books
            if ($category === 'all' || $category === 'recently_added') {
                $recommendations['recently_added'] = $this->getRecentlyAddedPopular(8);
            }

            // Filter out books user has already borrowed if authenticated
            if ($user) {
                $userBorrowedBookIds = $user->borrows()
                    ->pluck('book_id')
                    ->toArray();

                foreach ($recommendations as $key => $books) {
                    $recommendations[$key] = collect($books)->filter(fn ($book) => ! in_array($book->id, $userBorrowedBookIds))->values();
                }
            }

            // Get user stats if authenticated
            $userStats = $user ? $this->getUserRecommendationStats($user) : null;

            return Inertia::render('Recommendations/Index', [
                'recommendations' => $recommendations,
                'userStats' => $userStats,
                'filters' => [
                    'category' => $category,
                    'per_page' => $perPage,
                ],
                'categories' => [
                    ['value' => 'all', 'label' => 'All Recommendations', 'icon' => '🎯'],
                    ['value' => 'personalized', 'label' => 'For You', 'icon' => '👤'],
                    ['value' => 'trending', 'label' => 'Trending Now', 'icon' => '🔥'],
                    ['value' => 'genre_based', 'label' => 'Your Genres', 'icon' => '📚'],
                    ['value' => 'similar_users', 'label' => 'Similar Tastes', 'icon' => '👥'],
                    ['value' => 'high_rated', 'label' => 'Highly Rated', 'icon' => '⭐'],
                    ['value' => 'recently_added', 'label' => 'New & Popular', 'icon' => '🆕'],
                ],
                'isAuthenticated' => (bool) $user,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to load recommendations: '.$e->getMessage());

            return redirect()
                ->route('books.index')
                ->with('error', 'Failed to load recommendations. Please try again.');
        }
    }

    /**
     * Get personalized recommendations based on user's reading history
     */
    private function getPersonalizedRecommendations(User $user, int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            // Get user's favorite genres based on borrowing history
            $favoriteGenres = $user->borrows()
                ->join('books', 'borrows.book_id', '=', 'books.id')
                ->select('books.genre_id', DB::raw('COUNT(*) as count'))
                ->whereNotNull('books.genre_id')
                ->groupBy('books.genre_id')
                ->orderByDesc('count')
                ->limit(3)
                ->pluck('genre_id');

            if ($favoriteGenres->isEmpty()) {
                return collect();
            }

            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->whereIn('genre_id', $favoriteGenres)
                ->where('status', 'available')
                ->whereNotIn('id', $user->borrows()->pluck('book_id'))
                ->orderByDesc('average_rating')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get personalized recommendations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get trending books (most borrowed in the last 30 days)
     */
    private function getTrendingBooks(int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->select('books.*', DB::raw('COUNT(borrows.id) as borrow_count'))
                ->leftJoin('borrows', function ($join): void {
                    $join->on('books.id', '=', 'borrows.book_id')
                        ->where('borrows.borrowed_at', '>=', now()->subDays(30));
                })
                ->where('books.status', 'available')
                ->groupBy('books.id')
                ->orderByDesc('borrow_count')
                ->orderByDesc('books.average_rating')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get trending books: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get genre-based recommendations for user
     */
    private function getGenreBasedRecommendations(?User $user, int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            if (! $user) {
                // For guest users, get books from popular genres
                $popularGenres = App\Models\Genre::query()->withCount('books')
                    ->orderByDesc('books_count')
                    ->limit(3)
                    ->pluck('id');

                return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                    ->whereIn('genre_id', $popularGenres)
                    ->where('status', 'available')
                    ->orderByDesc('average_rating')
                    ->limit($limit)
                    ->get();
            }

            // Get genres from user's highly rated reviews
            $favoriteGenres = $user->reviews()
                ->where('rating', '>=', 4)
                ->join('books', 'reviews.book_id', '=', 'books.id')
                ->select('books.genre_id')
                ->whereNotNull('books.genre_id')
                ->groupBy('books.genre_id')
                ->pluck('genre_id');

            if ($favoriteGenres->isEmpty()) {
                return collect();
            }

            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->whereIn('genre_id', $favoriteGenres)
                ->where('status', 'available')
                ->whereNotIn('id', $user->borrows()->pluck('book_id'))
                ->where('average_rating', '>=', 4.0)
                ->orderByDesc('average_rating')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get genre-based recommendations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get recommendations based on similar users' preferences
     */
    private function getSimilarUserRecommendations(User $user, int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            // Find users who have borrowed similar books
            $userBooks = $user->borrows()->pluck('book_id');

            if ($userBooks->isEmpty()) {
                return collect();
            }

            $similarUsers = App\Models\User::query()->whereHas('borrows', function ($query) use ($userBooks): void {
                $query->whereIn('book_id', $userBooks);
            })
                ->where('id', '!=', $user->id)
                ->withCount(['borrows' => function ($query) use ($userBooks): void {
                    $query->whereIn('book_id', $userBooks);
                }])
                ->orderByDesc('borrows_count')
                ->limit(10)
                ->pluck('id');

            if ($similarUsers->isEmpty()) {
                return collect();
            }

            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->whereHas('borrows', function ($query) use ($similarUsers): void {
                    $query->whereIn('user_id', $similarUsers);
                })
                ->whereNotIn('id', $userBooks)
                ->where('status', 'available')
                ->where('average_rating', '>=', 3.5)
                ->orderByDesc('average_rating')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get similar user recommendations: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get highly rated books
     */
    private function getHighRatedBooks(int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->where('status', 'available')
                ->where('average_rating', '>=', 4.5)
                ->whereHas('reviews', function ($query): void {
                    $query->havingRaw('COUNT(*) >= 3'); // At least 3 reviews
                })
                ->orderByDesc('average_rating')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get high rated books: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get recently added popular books
     */
    private function getRecentlyAddedPopular(int $limit = 8): \Illuminate\Support\Collection
    {
        try {
            return App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'genre', 'reviews'])
                ->where('created_at', '>=', now()->subDays(90))
                ->where('status', 'available')
                ->orderByDesc('average_rating')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

        } catch (Exception $e) {
            Log::error('Failed to get recently added popular books: '.$e->getMessage());

            return collect();
        }
    }

    /**
     * Get user stats for personalization insights
     */
    private function getUserRecommendationStats(User $user): array
    {
        try {
            $stats = [
                'total_borrows' => $user->borrows()->count(),
                'total_reviews' => $user->reviews()->count(),
                'average_rating_given' => round((float) ($user->reviews()->avg('rating') ?: 0), 1),
                'favorite_genre' => null,
                'reading_streak' => 0,
            ];

            // Get favorite genre
            $favoriteGenre = $user->borrows()
                ->join('books', 'borrows.book_id', '=', 'books.id')
                ->join('genres', 'books.genre_id', '=', 'genres.id')
                ->select('genres.name', DB::raw('COUNT(*) as count'))
                ->groupBy('genres.id', 'genres.name')
                ->orderByDesc('count')
                ->first();

            if ($favoriteGenre) {
                $stats['favorite_genre'] = [
                    'name' => $favoriteGenre->name,
                    'count' => $favoriteGenre->count,
                ];
            }

            return $stats;

        } catch (Exception $e) {
            Log::error('Failed to get user recommendation stats: '.$e->getMessage());

            return [];
        }
    }
}
