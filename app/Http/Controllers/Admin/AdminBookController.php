<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

final class AdminBookController extends Controller
{
    public function index()
    {
        Log::info('AdminBookController::index - Loading books index page', [
            'user_id' => Auth::id(),
            'filters' => request()->only(['search', 'status']),
            'page' => request('page', 1),
        ]);

        try {
            $books = Book::query()
                ->with(['genre', 'activeReservation'])
                ->when(request('search'), function ($query, $search): void {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%")
                        ->orWhere('ISBN', 'like', "%$search%");
                })
                ->when(request('status'), function ($query, $status): void {
                    $query->where('status', $status);
                })
                ->paginate(10)
                ->withQueryString();

            Log::info('AdminBookController::index - Books loaded successfully', [
                'user_id' => Auth::id(),
                'total_books' => $books->total(),
                'current_page' => $books->currentPage(),
            ]);

            return Inertia::render('Admin/Books/Index', [
                'books' => $books,
                'filters' => request()->only(['search', 'status']),
            ]);
        } catch (Exception $e) {
            Log::error('AdminBookController::index - Error loading books', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'An error occurred while loading books.');
        }
    }

    public function create()
    {
        return Inertia::render('Admin/Books/Form', [
            'genres' => Genre::all(),
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        Log::info('AdminBookController::store - Starting book creation', [
            'user_id' => Auth::id(),
            'validated_data' => $request->validated(),
        ]);

        try {
            $book = Book::create($request->validated());

            Log::info('AdminBookController::store - Book created successfully', [
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Book created successfully');
        } catch (Exception $e) {
            Log::error('AdminBookController::store - Error creating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'validated_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the book.');
        }
    }

    public function edit(Book $book)
    {
        return Inertia::render('Admin/Books/Form', [
            'book' => $book->load('genre'),
            'genres' => Genre::all(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        Log::info('AdminBookController::update - Starting book update', [
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'original_title' => $book->title,
            'validated_data' => $request->validated(),
        ]);

        try {
            $book->update($request->validated());

            Log::info('AdminBookController::update - Book updated successfully', [
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'new_title' => $book->fresh()->title,
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Book updated successfully');
        } catch (Exception $e) {
            Log::error('AdminBookController::update - Error updating book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'validated_data' => $request->validated(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the book.');
        }
    }

    public function destroy(Book $book)
    {
        Log::info('AdminBookController::destroy - Starting book deletion', [
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'title' => $book->title,
        ]);

        try {
            $bookTitle = $book->title;
            $bookId = $book->id;

            $book->delete();

            Log::info('AdminBookController::destroy - Book deleted successfully', [
                'user_id' => Auth::id(),
                'book_id' => $bookId,
                'title' => $bookTitle,
            ]);

            return redirect()->route('admin.books.index')
                ->with('success', 'Book deleted successfully');
        } catch (Exception $e) {
            Log::error('AdminBookController::destroy - Error deleting book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'title' => $book->title,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the book.');
        }
    }
}
