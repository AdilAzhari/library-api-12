<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\DTO\BorrowBookDTO;
use App\DTO\ReserveBookDTO;
use App\DTO\ReviewBookDTO;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Services\BookService;
use App\Services\CoverImageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

final class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService,
        protected CoverImageService $coverService
    ) {}

    public function index(Request $request): \Inertia\Response|RedirectResponse
    {
        try {
            $perPage = $request->integer('per_page', 12);

            $filters = [
                'search' => $request->input('search'),
                'genre' => $request->input('genre'),
                'status' => $request->input('status'),
                'sort' => $request->input('sort'),
                'year_from' => $request->input('year_from'),
                'year_to' => $request->input('year_to'),
                'min_rating' => $request->input('min_rating'),
                'author' => $request->input('author'),
                'isbn' => $request->input('isbn'),
            ];

            $books = $this->bookService->getAllBooks($filters, $perPage);

            $statusFacets = Book::select('status')
                ->selectRaw('count(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn ($item) => ['value' => $item->status, 'count' => $item->count]);

            return Inertia::render('Books/Index', [
                'books' => $books->withQueryString()->all(),
                'genres' => Genre::query()->get(),
                'filters' => $filters,
                'pagination' => [
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                    'per_page' => $books->perPage(),
                    'total' => $books->total(),
                    'links' => $books->linkCollection()->toArray(),
                ],
                'facets' => [
                    ['field' => 'status', 'values' => $statusFacets],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch books: '.$e->getMessage());

            return back()->with('error', 'Failed to fetch books. Please try again.');
        }
    }

    public function create()
    {
        return Inertia::render('Books/Create', [
            'genres' => Genre::query()->all()]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validatedData = BookCreateDTO::validate($request);
            $bookDTO = BookCreateDTO::fromArray($validatedData)->sanitize();

            $bookData = $bookDTO->toArray();

            if ($bookDTO->hasCoverImage()) {
                $bookData['cover_image'] = $this->coverService->store(
                    $bookDTO->cover_image,
                    $bookDTO->title,
                    $bookDTO->author,
                    $bookDTO->publication_year
                );
            }

            $book = $this->bookService->createBook($bookDTO);

            Log::info('New book created: '.$book->id);

            return redirect()->route('books.index')
                ->with('success', 'Book created successfully.');

        } catch (Exception $e) {
            Log::error('Failed to create book: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to create book. Please try again.');
        }
    }

    public function show(Book $book): \Inertia\Response|RedirectResponse
    {
        try {
            $book->load(['reviews', 'currentBorrow', 'currentReservation', 'genre', 'reviews.user', 'activeReservation', 'activeBorrow']);
            $book->setAppends(['is_available']);
            $recommendedBooks = $this->bookService->getRecommendedBooks($book);

            return Inertia::render('Books/Show', [
                'book' => $book,
                'recommendedBooks' => $recommendedBooks,
                'isReserved' => $book->currentReservation?->user_id === auth()->id(),
                'isBorrowed' => $book->currentBorrow?->user_id === auth()->id(),
                'flashMessage' => [
                    'success' => session('success'),
                    'error' => session('error'),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch book details: '.$e->getMessage());

            return back()->with('error', 'Failed to fetch book details. Please try again.');
        }
    }

    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book,
            'genres' => Genre::query()->all(),
            'currentCover' => $book->cover_image ? Storage::url($book->cover_image) : null,
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        try {
            $validatedData = $request->validated();
            $bookDTO = BookUpdateDTO::fromArray($validatedData)->sanitize();

            $bookData = $bookDTO->toArray();

            if ($bookDTO->hasCoverImage()) {
                $bookData['cover_image'] = $this->coverService->update(
                    $bookDTO->cover_image,
                    $book->cover_image,
                    $bookDTO->title,
                    $bookDTO->author,
                    $bookDTO->publication_year
                );
            }

            $this->bookService->updateBook($book, $bookDTO);

            return redirect()->route('books.show', $book)
                ->with('success', 'Book updated successfully.');

        } catch (Exception $e) {
            Log::error('Failed to update book: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update book. Please try again.');
        }
    }

    public function reserve(Book $book)
    {

        try {
            $reservationDTO = new ReserveBookDTO(
                bookId: $book->id,
                userId: auth()->id()
            );

            $this->bookService->reserveBook($reservationDTO);

            return redirect()->back()->with('success', 'Book reserved successfully.');
        } catch (Exception $e) {
            Log::error('Failed to reserve book: '.$e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle borrowing a book
     *
     * @return RedirectResponse
     */
    public function borrow(Book $book)
    {
        try {
            $dto = new BorrowBookDTO(
                bookId: $book->id,
                userId: auth()->id()
            );

            $borrow = $this->bookService->borrowBook($dto);

            return redirect()
                ->route('borrows.index')
                ->with('success', sprintf(
                    'Book "%s" borrowed successfully. Due on %s',
                    $book->title,
                    $borrow->due_date->format('M j, Y')
                ));

        } catch (Exception $e) {
            Log::error('Book borrowing failed - Book ID: '.$book->id.' - '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Borrowing failed: '.$e->getMessage());
        }
    }

    public function review(StoreReviewRequest $request, Book $book)
    {
        try {
            if ($book->reviews()->where('user_id', auth()->id())->exists()) {
                return back()->with('error', 'You have already reviewed this book.');
            }

            $reviewDTO = new ReviewBookDTO(
                bookId: $book->id,
                rating: $request->rating,
                comment: $request->comment
            );

            Review::query()->create([
                'book_id' => $reviewDTO->bookId,
                'user_id' => auth()->id(),
                'rating' => $reviewDTO->rating,
                'comment' => $reviewDTO->comment,
            ]);

            return redirect()->back()->with('success', 'Review submitted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to submit review: '.$e->getMessage());

            return back()->with('error', 'Failed to submit review. Please try again.');
        }
    }
}
