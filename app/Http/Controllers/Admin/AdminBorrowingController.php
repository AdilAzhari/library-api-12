<?php

declare(strict_types=1);

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
        $searchQuery = request()->search;
        $status = request()->status;

        $borrowings = Borrow::with(['book', 'user'])
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->whereHas('book', function ($bookQuery) use ($searchQuery) {
                        $bookQuery->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('author', 'like', "%{$searchQuery}%");
                    })
                        ->orWhereHas('user', function ($userQuery) use ($searchQuery) {
                            $userQuery->where('name', 'like', "%{$searchQuery}%")
                                ->orWhere('email', 'like', "%{$searchQuery}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                if ($status === 'returned') {
                    $query->whereNotNull('returned_at');
                } elseif ($status === 'overdue') {
                    $query->whereNull('returned_at')
                        ->where('due_date', '<', now());
                } elseif ($status === 'active') {
                    $query->whereNull('returned_at')
                        ->where('due_date', '>=', now());
                }
            })
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
                        : ($borrowing->isOverdue() ? 'overdue' : 'active'),
                ];
            });

        return Inertia::render('Admin/Borrowings/Index', [
            'borrowings' => $borrowings,
            'filters' => request()->all('search', 'status'),
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
        $searchQuery = request()->input('search');

        $availableBooks = $searchQuery
            ? Book::search($searchQuery)->where('available', true)->get()
            : Book::query()->available()->get();

        return Inertia::render('Admin/Borrowings/Create', [
            'availableBooks' => $availableBooks,
            'users' => User::all(),
            'filters' => request()->only('search'),
        ]);
    }

    public function markReturned(Borrow $borrowing)
    {
        $borrowing->update([
            'returned_at' => now(),
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
            'renewal_count' => $borrowing->renewal_count + 1,
        ]);

        return back()->with('success', 'Borrowing renewed successfully');
    }

    public function destroy(Borrow $borrowing)
    {
        if (! $borrowing->returned_at) {
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
            'borrow' => [
                'id' => $borrowing->id,
                'book' => [
                    'id' => $borrowing->book->id,
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
                    : ($borrowing->isOverdue() ? 'overdue' : 'active'),
                'late_fee' => $borrowing->late_fee,
                'renewal_count' => $borrowing->renewal_count,
                'notes' => $borrowing->notes,
            ],
            'borrowHistory' => $borrowing->book->borrows()
                ->with('user')
                ->where('id', '!=', $borrowing->id)
                ->latest()
                ->paginate(5),
            'canRenew' => ! $borrowing->returned_at &&
                $borrowing->renewal_count < config('library.max_renewals') &&
                ! $borrowing->isOverdue(),
        ]);
    }
}
