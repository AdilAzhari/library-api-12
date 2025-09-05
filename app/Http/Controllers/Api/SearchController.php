<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingList;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class SearchController extends Controller
{
    use ApiMessages;

    /**
     * Global search across books, reading lists, and other content
     */
    public function global(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');
            $limit = min($request->integer('limit', 10), 50);

            if (empty($query) || mb_strlen((string) $query) < 2) {
                return $this->errorResponse('Search query must be at least 2 characters', 400);
            }

            // Search books
            $books = Book::query()->where('status', 'available')
                ->where(function ($q) use ($query): void {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('author', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhereHas('genre', function ($genreQuery) use ($query): void {
                            $genreQuery->where('name', 'LIKE', "%{$query}%");
                        });
                })
                ->with(['genre'])
                ->limit($limit)
                ->get()
                ->map(fn ($book) => [
                    'type' => 'book',
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'genre' => $book->genre?->name,
                    'cover_image' => $book->cover_image,
                    'url' => route('books.show', $book),
                ]);

            // Search public reading lists
            $readingLists = ReadingList::query()->where('is_public', true)
                ->where(function ($q) use ($query): void {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->with(['user:id,name'])
                ->limit($limit)
                ->get()
                ->map(fn ($list) => [
                    'type' => 'reading_list',
                    'id' => $list->id,
                    'name' => $list->name,
                    'description' => $list->description,
                    'creator' => $list->user?->name,
                    'book_count' => $list->books()->count(),
                    'url' => route('reading-lists.show', $list),
                ]);

            $results = $books->concat($readingLists)->take($limit);

            return $this->successResponse('Search results retrieved successfully', [
                'query' => $query,
                'results' => $results,
                'total' => $results->count(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to perform global search: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to perform search');
        }
    }

    /**
     * Search books with advanced filtering
     */
    public function books(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');
            $genreId = $request->integer('genre_id');
            $author = $request->input('author');
            $year = $request->integer('year');
            $status = $request->input('status', 'available');
            $limit = min($request->integer('limit', 20), 100);

            $booksQuery = Book::query();

            if (! empty($query)) {
                $booksQuery->where(function ($q) use ($query): void {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                });
            }

            if ($genreId) {
                $booksQuery->where('genre_id', $genreId);
            }

            if ($author) {
                $booksQuery->where('author', 'LIKE', "%{$author}%");
            }

            if ($year) {
                $booksQuery->where('publication_year', $year);
            }

            if ($status && in_array($status, ['available', 'borrowed', 'reserved'])) {
                $booksQuery->where('status', $status);
            }

            $books = $booksQuery->with(['genre'])
                ->orderBy('title')
                ->limit($limit)
                ->get();

            return $this->successResponse('Book search results retrieved successfully', [
                'query' => $query,
                'filters' => [
                    'genre_id' => $genreId,
                    'author' => $author,
                    'year' => $year,
                    'status' => $status,
                ],
                'books' => $books,
                'total' => $books->count(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to search books: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to search books');
        }
    }

    /**
     * Search reading lists
     */
    public function readingLists(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = $request->input('q', '');
            $includeOwn = $request->boolean('include_own', true);
            $publicOnly = $request->boolean('public_only', false);
            $limit = min($request->integer('limit', 20), 100);

            $listsQuery = ReadingList::query();

            if (! empty($query)) {
                $listsQuery->where(function ($q) use ($query): void {
                    $q->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%");
                });
            }

            if ($publicOnly) {
                $listsQuery->where('is_public', true);
            } else {
                if ($includeOwn) {
                    $listsQuery->where(function ($q) use ($user): void {
                        $q->where('is_public', true)
                            ->orWhere('user_id', $user->id);
                    });
                } else {
                    $listsQuery->where('is_public', true)
                        ->where('user_id', '!=', $user->id);
                }
            }

            $readingLists = $listsQuery->with(['user:id,name'])
                ->withCount('books')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return $this->successResponse('Reading list search results retrieved successfully', [
                'query' => $query,
                'reading_lists' => $readingLists,
                'total' => $readingLists->count(),
            ]);

        } catch (Exception $e) {
            Log::error('Failed to search reading lists: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to search reading lists');
        }
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');
            $type = $request->input('type', 'books'); // books, authors, genres
            $limit = min($request->integer('limit', 10), 20);

            if (empty($query) || mb_strlen((string) $query) < 2) {
                return $this->successResponse('Suggestions retrieved successfully', []);
            }

            $suggestions = [];

            switch ($type) {
                case 'books':
                    $suggestions = Book::query()->where('title', 'LIKE', "%{$query}%")
                        ->where('status', 'available')
                        ->select('id', 'title', 'author')
                        ->limit($limit)
                        ->get()
                        ->map(fn ($book) => [
                            'id' => $book->id,
                            'text' => $book->title,
                            'subtitle' => "by {$book->author}",
                            'type' => 'book',
                        ]);
                    break;

                case 'authors':
                    $suggestions = Book::select('author')
                        ->where('author', 'LIKE', "%{$query}%")
                        ->groupBy('author')
                        ->limit($limit)
                        ->get()
                        ->map(fn ($book) => [
                            'text' => $book->author,
                            'type' => 'author',
                        ]);
                    break;

                case 'genres':
                    $suggestions = DB::table('genres')
                        ->where('name', 'LIKE', "%{$query}%")
                        ->select('id', 'name')
                        ->limit($limit)
                        ->get()
                        ->map(fn ($genre) => [
                            'id' => $genre->id,
                            'text' => $genre->name,
                            'type' => 'genre',
                        ]);
                    break;
            }

            return $this->successResponse('Suggestions retrieved successfully', $suggestions);

        } catch (Exception $e) {
            Log::error('Failed to get search suggestions: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get suggestions');
        }
    }
}
