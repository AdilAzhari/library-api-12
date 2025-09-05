<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

final class GenreController extends Controller
{
    /**
     * Display all genres with book counts
     */
    public function index(): Response
    {
        try {
            $genres = Genre::query()->withCount(['books' => function ($query): void {
                $query->where('status', 'available');
            }])
                ->orderBy('name')
                ->get()
                ->map(fn ($genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'description' => $genre->description,
                    'books_count' => $genre->books_count,
                    'color' => $this->getGenreColor($genre->name),
                    'icon' => $this->getGenreIcon($genre->name),
                ]);

            return Inertia::render('Genres/Index', [
                'genres' => $genres,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch genres: '.$e->getMessage());

            return redirect()
                ->route('books.index')
                ->with('error', 'Failed to load genres. Please try again.');
        }
    }

    /**
     * Display books for a specific genre
     */
    public function show(Request $request, Genre $genre): Response
    {
        try {
            $perPage = $request->integer('per_page', 12);
            $sort = $request->input('sort', 'title');
            $order = $request->input('order', 'asc');

            $books = Book::query()->where('genre_id', $genre->id)
                ->with(['activeBorrow', 'activeReservation', 'reviews'])
                ->orderBy($sort, $order)
                ->paginate($perPage)
                ->withQueryString();

            return Inertia::render('Genres/Show', [
                'genre' => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'description' => $genre->description,
                    'color' => $this->getGenreColor($genre->name),
                    'icon' => $this->getGenreIcon($genre->name),
                ],
                'books' => $books,
                'filters' => [
                    'sort' => $sort,
                    'order' => $order,
                    'per_page' => $perPage,
                ],
                'sortOptions' => [
                    ['value' => 'title', 'label' => 'Title'],
                    ['value' => 'author', 'label' => 'Author'],
                    ['value' => 'publication_year', 'label' => 'Publication Year'],
                    ['value' => 'average_rating', 'label' => 'Rating'],
                    ['value' => 'created_at', 'label' => 'Date Added'],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch genre books: '.$e->getMessage());

            return redirect()
                ->route('genres.index')
                ->with('error', 'Failed to load books for this genre. Please try again.');
        }
    }

    /**
     * Get color class for genre
     */
    private function getGenreColor(string $genreName): string
    {
        $colors = [
            'fiction' => 'bg-blue-500',
            'non-fiction' => 'bg-green-500',
            'science fiction' => 'bg-purple-500',
            'mystery' => 'bg-gray-700',
            'romance' => 'bg-pink-500',
            'fantasy' => 'bg-indigo-500',
            'thriller' => 'bg-red-500',
            'horror' => 'bg-gray-900',
            'biography' => 'bg-yellow-600',
            'history' => 'bg-orange-500',
            'science' => 'bg-cyan-500',
            'philosophy' => 'bg-violet-500',
            'psychology' => 'bg-teal-500',
            'business' => 'bg-emerald-600',
            'self-help' => 'bg-amber-500',
        ];

        $lowercaseName = mb_strtolower($genreName);

        foreach ($colors as $genre => $color) {
            if (str_contains($lowercaseName, $genre)) {
                return $color;
            }
        }

        // Default colors for unknown genres
        $defaultColors = ['bg-slate-500', 'bg-zinc-500', 'bg-neutral-500', 'bg-stone-500'];

        return $defaultColors[crc32($genreName) % count($defaultColors)];
    }

    /**
     * Get icon for genre
     */
    private function getGenreIcon(string $genreName): string
    {
        $icons = [
            'fiction' => '📚',
            'non-fiction' => '📖',
            'science fiction' => '🚀',
            'mystery' => '🔍',
            'romance' => '💕',
            'fantasy' => '🐉',
            'thriller' => '⚡',
            'horror' => '👻',
            'biography' => '👤',
            'history' => '🏛️',
            'science' => '🔬',
            'philosophy' => '🤔',
            'psychology' => '🧠',
            'business' => '💼',
            'self-help' => '🌟',
        ];

        $lowercaseName = mb_strtolower($genreName);

        foreach ($icons as $genre => $icon) {
            if (str_contains($lowercaseName, $genre)) {
                return $icon;
            }
        }

        return '📕'; // Default book icon
    }
}
