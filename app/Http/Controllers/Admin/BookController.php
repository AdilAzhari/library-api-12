<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Models\Book;
use Inertia\Inertia;

final class BookController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Books/Index', [
            'books' => Book::with('genre')
                ->filter(request()->only('search', 'status'))
                ->paginate(10)
                ->withQueryString(),
            'filters' => request()->all('search', 'status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Books/Form', [
            'genres' => \App\Models\Genre::all(),
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());

        return redirect()->route('admin.books.index')
            ->with('success', 'Book created successfully');
    }

    public function edit(Book $book)
    {
        return Inertia::render('Admin/Books/Form', [
            'book' => $book,
            'genres' => \App\Models\Genre::all(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return redirect()->route('admin.books.index')
            ->with('success', 'Book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Book deleted successfully');
    }
}
