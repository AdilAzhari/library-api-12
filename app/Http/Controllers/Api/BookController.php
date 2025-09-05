<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookExportService;
use App\Services\BookService;
use App\Services\GoogleBooksService;
use App\Services\TranslationService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;

final class BookController extends Controller
{
    use ApiMessages;

    public function __construct(
        protected BookService $bookService,
        protected GoogleBooksService $googleBooksService,
        protected BookExportService $bookExportService,
        protected TranslationService $translationService
    ) {}

    public function index(Request $request)
    {
        Log::info('BookController::index - Starting book retrieval', [
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'locale' => $request->header('Accept-Language', 'en'),
        ]);

        try {
            $locale = $request->header('Accept-Language', 'en');

            if (! in_array($locale, ['en', 'ar'])) {
                $locale = 'en';
            }
            App::setLocale($locale);

            $cacheKey = 'books_'.md5(serialize($request->all()));

            //        $books = Cache::remember($cacheKey, 600, function () use ($request) {
            $books = $this->bookService->getAllBooks($request->all(), $request->integer('per_page', 10));
            //        });

            // Transform the paginated data using BookResource
            $books->getCollection()->transform(fn ($book) => (new BookResource($book))->toArray(request()));

            Log::info('BookController::index - Books retrieved successfully', [
                'count' => $books->count(),
                'total' => $books->total(),
                'per_page' => $books->perPage(),
                'locale' => $locale,
            ]);

            return $this->successResponse('Books retrieved successfully', $books);
        } catch (Exception $e) {
            Log::error('BookController::index - Error retrieving books', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while retrieving books.');
        }
    }

    public function store(StoreBookRequest $request)
    {
        Log::info('BookController::store - Starting book creation', [
            'validated_data' => $request->validated(),
            'user_id' => Auth::id(),
        ]);

        $validatedData = $request->validated();
        try {
            $dto = BookCreateDTO::fromRequest($request);
            $book = $this->bookService->createBook($dto);

            Log::info('BookController::store - Book created successfully', [
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'user_id' => Auth::id(),
            ]);

            return $this->successResponse('Book created successfully', new BookResource($book), 201);
        } catch (Exception $e) {
            Log::error('BookController::store - Error creating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'validated_data' => $validatedData,
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while creating the book.');
        }
    }

    /**
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    public function show(Request $request, Book $book)
    {
        Log::info('BookController::show - Starting book retrieval', [
            'book_id' => $book->id,
            'title' => $book->title,
            'user_id' => Auth::id(),
            'locale' => $request->header('Accept-Language', 'en'),
        ]);

        try {
            $locale = $request->header('Accept-Language', 'en');

            if ($locale !== 'en') {
                $book->title = $this->translationService->translate($book->title, $locale);
                $book->description = $this->translationService->translate($book->description, $locale);
            }

            Log::info('BookController::show - Book retrieved successfully', [
                'book_id' => $book->id,
                'locale' => $locale,
                'translation_required' => $locale !== 'en',
            ]);

            return $this->successResponse('Book retrieved successfully', new BookResource($book));
        } catch (Exception $e) {
            Log::error('BookController::show - Error retrieving book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while retrieving the book.');
        }
    }

    public function update(Request $request, Book $book)
    {
        Log::info('BookController::update - Starting book update', [
            'book_id' => $book->id,
            'original_title' => $book->title,
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
        ]);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|digits:4|integer|min:1900|max:'.now()->format('Y'),
            'cover_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'genre_id' => 'required|integer|exists:genres,id|nullable',
        ]);

        try {
            $dto = BookUpdateDTO::fromRequest($request);
            $updatedBook = $this->bookService->updateBook($book, $dto);

            Log::info('BookController::update - Book updated successfully', [
                'book_id' => $updatedBook->id,
                'new_title' => $updatedBook->title,
                'user_id' => Auth::id(),
            ]);

            return $this->successResponse('Book updated successfully', new BookResource($updatedBook));
        } catch (Exception $e) {
            Log::error('BookController::update - Error updating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $book->id,
                'validated_data' => $validatedData,
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while updating the book: '.$e->getMessage());
        }
    }

    public function destroy(Book $book)
    {
        Log::info('BookController::destroy - Starting book deletion', [
            'book_id' => $book->id,
            'title' => $book->title,
            'user_id' => Auth::id(),
        ]);

        try {
            $this->bookService->deleteBook($book->id);

            Log::info('BookController::destroy - Book moved to trash successfully', [
                'book_id' => $book->id,
                'user_id' => Auth::id(),
            ]);

            return $this->successResponse('Book moved to trash');
        } catch (Exception $e) {
            Log::error('BookController::destroy - Error deleting book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while deleting the book.');
        }
    }

    public function restore($id)
    {
        $book = $this->bookService->restoreBook($id);

        return $this->successResponse('Book restored successfully');
    }

    public function export()
    {
        return $this->bookExportService->exportBooks();
    }

    public function fetchFromGoogleBooks(Request $request)
    {
        $results = $this->googleBooksService->searchBooks($request->input('query'));

        return $this->successResponse('Books fetched from Google Books', $results);
    }

    public function recommend(Book $book)
    {
        $relatedBooks = $this->bookService->recommendBooks($book);

        return $this->successResponse('Recommended books retrieved successfully', $relatedBooks);
    }

    public function health()
    {
        return $this->successResponse('API is healthy', ['status' => 'ok']);
    }

    /**
     * Search books with query and filters
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $filters = $request->only(['genre_id', 'author', 'year', 'status']);
            $perPage = min($request->integer('per_page', 15), 50);

            $books = $this->bookService->searchBooks($query, $filters, $perPage);

            // Transform the paginated search results using BookResource
            if (method_exists($books, 'getCollection')) {
                $books->getCollection()->transform(fn ($book) => (new BookResource($book))->toArray(request()));
            }

            return $this->successResponse('Books search completed', $books);
        } catch (Exception $e) {
            Log::error('Error searching books: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to search books');
        }
    }

    /**
     * Get autocomplete suggestions for book titles
     */
    public function autocomplete(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $limit = min($request->integer('limit', 10), 20);

            if (mb_strlen((string) $query) < 2) {
                return $this->successResponse('Autocomplete results', []);
            }

            $suggestions = Book::query()->where('title', 'LIKE', "%{$query}%")
                ->select('id', 'title', 'author')
                ->limit($limit)
                ->get()
                ->map(fn ($book) => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'label' => "{$book->title} by {$book->author}",
                ]);

            return $this->successResponse('Autocomplete results retrieved', $suggestions);
        } catch (Exception $e) {
            Log::error('Error getting autocomplete: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get autocomplete suggestions');
        }
    }

    /**
     * Get personalized book recommendations
     */
    public function recommendations(Request $request)
    {
        try {
            $user = auth()->user();
            $limit = min($request->integer('limit', 6), 20);

            $recommendations = $this->bookService->getPersonalizedRecommendations($user->id, $limit);

            return $this->successResponse('Recommendations retrieved successfully', $recommendations);
        } catch (Exception $e) {
            Log::error('Error getting recommendations: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get recommendations');
        }
    }

    /**
     * Check book availability and queue information
     */
    public function availability(Request $request, Book $book)
    {
        try {
            $availability = [
                'status' => $book->status,
                'is_available' => $book->status === 'available',
                'queue_length' => $book->reservations()->active()->count(),
                'estimated_wait_days' => $book->reservations()->active()->count() * 14, // Assuming 14 day loan periods
                'next_available_date' => null,
            ];

            if ($book->status === 'borrowed') {
                $currentBorrow = $book->borrows()->active()->first();
                $availability['due_date'] = $currentBorrow?->due_date;
                $availability['next_available_date'] = $currentBorrow?->due_date;
            }

            return $this->successResponse('Book availability retrieved', $availability);
        } catch (Exception $e) {
            Log::error('Error checking book availability: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to check book availability');
        }
    }

    /**
     * Get detailed queue information for a book
     */
    public function queueInfo(Request $request, Book $book)
    {
        try {
            $queueInfo = [
                'book_id' => $book->id,
                'current_status' => $book->status,
                'total_in_queue' => $book->reservations()->active()->count(),
                'user_position' => null,
                'estimated_wait_time' => null,
            ];

            if (auth()->check()) {
                $userReservation = $book->reservations()
                    ->where('user_id', auth()->id())
                    ->active()
                    ->first();

                if ($userReservation) {
                    $position = $book->reservations()
                        ->active()
                        ->where('created_at', '<', $userReservation->created_at)
                        ->count() + 1;

                    $queueInfo['user_position'] = $position;
                    $queueInfo['estimated_wait_time'] = $position * 14; // days
                }
            }

            return $this->successResponse('Queue information retrieved', $queueInfo);
        } catch (Exception $e) {
            Log::error('Error getting queue info: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get queue information');
        }
    }

    /**
     * Get related books based on genre and author
     */
    public function related(Request $request, Book $book)
    {
        try {
            $limit = min($request->integer('limit', 6), 20);

            $relatedBooks = Book::query()->where('id', '!=', $book->id)
                ->where(function ($query) use ($book): void {
                    $query->where('genre_id', $book->genre_id)
                        ->orWhere('author', $book->author);
                })
                ->where('status', 'available')
                ->with(['genre'])
                ->limit($limit)
                ->get();

            return $this->successResponse('Related books retrieved', BookResource::collection($relatedBooks));
        } catch (Exception $e) {
            Log::error('Error getting related books: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get related books');
        }
    }

    /**
     * Get trending books based on recent borrows and reviews
     */
    public function trending(Request $request)
    {
        try {
            $limit = min($request->integer('limit', 10), 50);
            $days = $request->integer('days', 30);

            // Get books with most activity in the specified period
            $trendingBooks = App\Models\Book::query()->withCount([
                'borrows' => function ($query) use ($days): void {
                    $query->where('borrowed_at', '>=', now()->subDays($days));
                },
                'reviews' => function ($query) use ($days): void {
                    $query->where('created_at', '>=', now()->subDays($days));
                },
            ])
                ->having('borrows_count', '>', 0)
                ->orderByDesc('borrows_count')
                ->orderByDesc('reviews_count')
                ->with(['genre'])
                ->limit($limit)
                ->get();

            return $this->successResponse('Trending books retrieved', BookResource::collection($trendingBooks));
        } catch (Exception $e) {
            Log::error('Error getting trending books: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get trending books');
        }
    }

    /**
     * Get newly added books
     */
    public function newArrivals(Request $request)
    {
        try {
            $limit = min($request->integer('limit', 10), 50);
            $days = $request->integer('days', 30);

            $newBooks = Book::query()->where('created_at', '>=', now()->subDays($days))
                ->where('status', 'available')
                ->with(['genre'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            return $this->successResponse('New arrivals retrieved', BookResource::collection($newBooks));
        } catch (Exception $e) {
            Log::error('Error getting new arrivals: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get new arrivals');
        }
    }
}
