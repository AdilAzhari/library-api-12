<?php

namespace App\Http\Controllers\Api;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\BookService;
use App\Services\GoogleBooksService;
use App\Services\BookExportService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    public function __construct(
        protected BookService        $bookService,
        protected GoogleBooksService $googleBooksService,
        protected BookExportService  $bookExportService
    )
    {
    }

    public function index(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        App::setLocale($locale);

        $cacheKey = 'books_' . md5(serialize($request->all()));
        $books = Cache::remember($cacheKey, 600, function () use ($request) {
            return $this->bookService->getAllBooks($request->all(), $request->input('per_page', 10));
        });

        return response()->json($books);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:books,title,NULL,id,author,' . $request->author,
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|digits:4|integer|min:1900|max:' . now()->format('Y'),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.unique' => 'A book with this title and author already exists.',
            'publication_year.max' => 'The publication year cannot be in the future.',
        ]);

        try {
            $dto = BookCreateDTO::fromRequest($request);
            $book = $this->bookService->createBook($dto);

            dd('here');
            return response()->json($book, 201);
        } catch (Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the book.'], 500);
        }
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|digits:4|integer|min:1900|max:' . now()->format('Y'),
            'cover_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'genre_id' => 'required|integer|exists:genres,id|nullable',
        ]);
        $dto = BookUpdateDTO::fromRequest($request);
        $book = $this->bookService->updateBook($book, $dto);
        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $this->bookService->deleteBook($book);
        return response()->json(['message' => 'Book moved to trash']);
    }

    public function restore($id)
    {
        $book = $this->bookService->restoreBook($id);
        return response()->json(['message' => 'Book restored successfully']);
    }

    public function export()
    {
        return $this->bookExportService->exportBooks();
    }

    public function fetchFromGoogleBooks(Request $request)
    {
        $results = $this->googleBooksService->searchBooks($request->input('query'));
        return response()->json($results);
    }

    public function recommend(Book $book)
    {
        $relatedBooks = $this->bookService->recommendBooks($book);
        return response()->json($relatedBooks);
    }

    public function health()
    {
        return response()->json(['status' => 'ok']);
    }
}
