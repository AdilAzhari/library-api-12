<?php

namespace App\Http\Controllers\Api;

use App\Aggregates\BookAggregate;
use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Services\BookExportService;
use App\Services\BookService;
use App\Services\GoogleBooksService;
use App\Services\TranslationService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;

class BookController extends Controller
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
        $locale = $request->header('Accept-Language', 'en');

        if (! in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        App::setLocale($locale);

        $cacheKey = 'books_'.md5(serialize($request->all()));

        //        $books = Cache::remember($cacheKey, 600, function () use ($request) {
        return $this->bookService->getAllBooks($request->all(), $request->input('per_page', 10));
        //        });

        return $this->successResponse('Books retrieved successfully', $books);
    }

    public function store(StoreBookRequest $request)
    {
        $request->validated();
        try {
            $dto = BookCreateDTO::fromRequest($request);
            $bookAggregate = BookAggregate::retrieve($request->input('id') ?? uniqid());
            $bookAggregate->createBook($dto)->persist();

            return $this->successResponse('Book created successfully', [], 201);
        } catch (Exception $e) {
            Log::error('Error creating book: '.$e->getMessage());

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
        $locale = $request->header('Accept-Language', 'en');

        if ($locale !== 'en') {
            $book->title = $this->translationService->translate($book->title, $locale);
            $book->description = $this->translationService->translate($book->description, $locale);
        }

        return $this->successResponse('Book retrieved successfully', $book);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|digits:4|integer|min:1900|max:'.now()->format('Y'),
            'cover_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'genre_id' => 'required|integer|exists:genres,id|nullable',
        ]);

        try {
            $dto = BookUpdateDTO::fromRequest($request);
            $bookAggregate = BookAggregate::retrieve($book->id);
            $bookAggregate->updateBook($dto)->persist();

            return $this->successResponse('Book updated successfully');
        } catch (Exception $e) {
            Log::error('Error updating book: '.$e->getMessage());

            return $this->serverErrorResponse('An error occurred while updating the book.');
        }
    }

    public function destroy(Book $book)
    {
        try {
            $bookAggregate = BookAggregate::retrieve($book->id);
            $bookAggregate->deleteBook($book)->persist();

            return $this->successResponse('Book moved to trash');
        } catch (Exception $e) {
            Log::error('Error deleting book: '.$e->getMessage());

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
}
