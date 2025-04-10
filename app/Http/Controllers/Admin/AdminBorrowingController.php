<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBorrowRequest;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminBorrowingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Borrowings/Index', [
            'borrowings' => Borrow::with(['book', 'user'])
                ->filter(request()->only('search', 'status'))
                ->latest()
                ->paginate(10)
                ->through(function ($borrowing) {
                    return [
                        'id' => $borrowing->id,
                        'book' => [
                            'title' => $borrowing->book->title,
                            'author' => $borrowing->book->author,
                            'cover_image_url' => $borrowing->book->cover_image_url,
                        ],
                        'user' => [
                            'name' => $borrowing->user->name,
                            'email' => $borrowing->user->email,
                        ],
                        'borrowed_at' => $borrowing->borrowed_at,
                        'due_date' => $borrowing->due_date,
                        'returned_at' => $borrowing->returned_at,
                        'is_overdue' => $borrowing->isOverdue(),
                        'status' => $borrowing->returned_at
                            ? 'returned'
                            : ($borrowing->isOverdue() ? 'overdue' : 'active')
                    ];
                }),
            'filters' => request()->all('search', 'status')
        ]);
    }

    public function store(StoreBorrowRequest $request)
    {
        $borrowing = Borrow::create([
            'book_id' => $request->book_id,
            'user_id' => $request->user_id,
            'borrowed_at' => $request->borrowed_at ?? now(),
            'due_date' => $request->due_date,
        ]);

        // Update book status
        $borrowing->book->update(['status' => 'borrowed']);

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Book checked out successfully');
    }

    public function create()
    {
        return Inertia::render('Admin/Borrowings/Create', [
            'availableBooks' => Book::query()->available()->get(),
            'users' => User::all()
        ]);
    }

    public function markReturned(Borrow $borrowing)
    {
        $borrowing->update([
            'returned_at' => now()
        ]);

        // Update book status
        $borrowing->book->update(['status' => 'available']);

        return back()->with('success', 'Book marked as returned');
    }

    public function renew(Borrow $borrowing)
    {
        $newDueDate = Carbon::parse($borrowing->due_date)->addWeeks(2);

        $borrowing->update([
            'due_date' => $newDueDate,
            'renewal_count' => $borrowing->renewal_count + 1
        ]);

        return back()->with('success', 'Borrowing renewed successfully');
    }

    public function destroy(Borrow $borrowing)
    {
        if (!$borrowing->returned_at) {
            $borrowing->book->update(['status' => 'available']);
        }

        $borrowing->delete();

        return back()->with('success', 'Borrowing record deleted');
    }

    public function return(Borrow $borrowing)
    {
        $borrowing->update(['returned_at' => now()]);

        return back()->with('success', 'Borrowing marked as returned');
    }

    public function show(Borrow $borrowing)
    {
        return Inertia::render('Admin/Borrowings/Show', [
            'borrowing' => [
                'id' => $borrowing->id,
                'book' => [
                    'title' => $borrowing->book->title,
                    'author' => $borrowing->book->author,
                    'cover_image_url' => $borrowing->book->cover_image_url,
                ],
                'user' => [
                    'name' => $borrowing->user->name,
                    'email' => $borrowing->user->email,
                ],
                'borrowed_at' => $borrowing->borrowed_at,
                'due_date' => $borrowing->due_date,
                'returned_at' => $borrowing->returned_at,
                'is_overdue' => $borrowing->isOverdue(),
                'status' => $borrowing->returned_at
                    ? 'returned'
                    : ($borrowing->isOverdue() ? 'overdue' : 'active')
            ]
        ]);
    }
}
