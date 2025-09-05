<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class NewReleasesController extends Controller
{
    /**
     * Display new releases with filtering options
     */
    public function index(Request $request): Response
    {
        try {
            $perPage = $request->integer('per_page', 12);
            $sort = $request->input('sort', 'created_at');
            $order = $request->input('order', 'desc');
            $genre = $request->input('genre');
            $search = $request->input('search');
            $timeframe = $request->input('timeframe', '30'); // days

            // Build query for new releases (books added within timeframe)
            $query = App\Models\Book::query()->with(['activeBorrow', 'activeReservation', 'reviews', 'genre'])
                ->where('created_at', '>=', now()->subDays((int) $timeframe));

            // Apply genre filter
            if ($genre) {
                $query->where('genre_id', $genre);
            }

            // Apply search filter
            if ($search) {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('author', 'LIKE', "%{$search}%")
                        ->orWhere('isbn', 'LIKE', "%{$search}%");
                });
            }

            // Apply sorting
            $allowedSorts = ['title', 'author', 'publication_year', 'average_rating', 'created_at'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'average_rating') {
                    $query->orderByDesc('average_rating');
                } else {
                    $query->orderBy($sort, $order);
                }
            }

            // Get paginated results
            $books = $query->paginate($perPage)->withQueryString();

            // Get available genres for filter
            $genres = \App\Models\Genre::query()->orderBy('name')->get(['id', 'name']);

            // Get statistics
            $stats = [
                'total_new_books' => Book::query()->where('created_at', '>=', now()->subDays((int) $timeframe))->count(),
                'this_week' => Book::query()->where('created_at', '>=', now()->subWeek())->count(),
                'this_month' => Book::query()->where('created_at', '>=', now()->subMonth())->count(),
                'popular_genre' => $this->getMostPopularNewGenre($timeframe),
            ];

            return Inertia::render('NewReleases/Index', [
                'books' => $books,
                'genres' => $genres,
                'stats' => $stats,
                'filters' => [
                    'search' => $search,
                    'genre' => $genre,
                    'sort' => $sort,
                    'order' => $order,
                    'per_page' => $perPage,
                    'timeframe' => $timeframe,
                ],
                'sortOptions' => [
                    ['value' => 'created_at', 'label' => 'Date Added'],
                    ['value' => 'title', 'label' => 'Title'],
                    ['value' => 'author', 'label' => 'Author'],
                    ['value' => 'publication_year', 'label' => 'Publication Year'],
                    ['value' => 'average_rating', 'label' => 'Rating'],
                ],
                'timeframeOptions' => [
                    ['value' => '7', 'label' => 'Last 7 days'],
                    ['value' => '14', 'label' => 'Last 2 weeks'],
                    ['value' => '30', 'label' => 'Last 30 days'],
                    ['value' => '60', 'label' => 'Last 2 months'],
                    ['value' => '90', 'label' => 'Last 3 months'],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch new releases: '.$e->getMessage());

            return redirect()
                ->route('books.index')
                ->with('error', 'Failed to load new releases. Please try again.');
        }
    }

    /**
     * Get the most popular genre among new releases
     */
    private function getMostPopularNewGenre(string $timeframe): ?array
    {
        try {
            $result = App\Models\Book::query()->with('genre')
                ->where('created_at', '>=', now()->subDays((int) $timeframe))
                ->whereNotNull('genre_id')
                ->selectRaw('genre_id, COUNT(*) as count')
                ->groupBy('genre_id')
                ->orderByDesc('count')
                ->first();

            if ($result && $result->genre) {
                return [
                    'name' => $result->genre->name,
                    'count' => $result->count,
                ];
            }

            return null;
        } catch (Exception $e) {
            Log::error('Failed to get popular new genre: '.$e->getMessage());

            return null;
        }
    }
}
