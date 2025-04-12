<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Inertia\Inertia;

class AdminBookController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Books/Index', [
            'books' => Book::query()
                ->with(['genre','activeReservation'])
                ->when(request('search'), function ($query, $search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('author', 'like', "%$search%")
                        ->orWhere('ISBN', 'like', "%$search%");
                })
                ->when(request('status'), function ($query, $status) {
                    $query->where('status', $status);
                })
                ->paginate(10)
                ->withQueryString(),
            'filters' => request()->only(['search', 'status'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Books/Form', [
            'genres' => Genre::all()
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
            'book' => $book->load('genre'),
            'genres' => Genre::all()
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
