<?php

namespace App\Http\Controllers\Front;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\BorrowingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BorrowController extends Controller
{
    public function __construct(
        protected BorrowingService $borrowingService
    )
    {
    }

    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $status = $request->input('status');
            $sortBy = $request->input('sort_by', 'due_date');
            $sortOrder = $request->input('sort_order', 'asc');
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $user = Auth::user();
            $isAdmin = $user->is_admin;

            $borrows = $isAdmin
                ? $this->borrowingService->getAllBorrows($status, $sortBy, $sortOrder, $perPage, $search)
                : $this->borrowingService->getUserBorrows($user->id, $status, $sortBy, $sortOrder, $perPage, $search);
            $borrows->all();
            return Inertia::render('Borrow/Index', [
                'borrowings' => $borrows->load('book.genre', 'user', 'book.activeReservation'),
                'filters' => [
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'search' => $search,
                ],
                'isAdmin' => $isAdmin,
                'maxRenewals' => config('library.max_renewals', 2),
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to load borrowings: ' . $e->getMessage());
        }
    }

    public function show(int $id): Response|RedirectResponse
    {
        try {
            $borrow = $this->borrowingService->getBorrowDetails($id);

            Gate::authorize('view', $borrow);

            $borrowHistory = $this->borrowingService->getBookBorrowHistory($borrow->book_id);

            return Inertia::render('Borrow/Show', [
                'borrow' => $borrow,
                'borrowHistory' => $borrowHistory,
                'canRenew' => $this->borrowingService->canRenew($borrow),
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('borrows.index')
                ->with('error', 'Failed to load borrowing details: ' . $e->getMessage());
        }
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $dto = new BorrowBookDTO(
                bookId: $request->book_id,
                userId: Auth::id(),
                borrowedAt: now(),
                dueDate: now()->addDays(config('library.default_borrow_days', 14))
            );

            $borrow = $this->borrowingService->borrowBook($dto);

            return redirect()
                ->route('borrows.index')
                ->with('success', 'Book borrowed successfully. Due on ' .
                    $borrow->due_date->format('M j, Y'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Borrowing failed: ' . $e->getMessage());
        }
    }

    public function renew(int $id): RedirectResponse
    {
        try {
            $borrow = $this->borrowingService->getBorrowDetails($id);
            Gate::authorize('renew', $borrow);

            $borrow = $this->borrowingService->renewBorrow($borrow);

            return redirect()
                ->back()
                ->with('success', 'Borrowing renewed until ' .
                    $borrow->due_date->format('M j, Y'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Renewal failed: ' . $e->getMessage());
        }
    }

    public function return(int $id): RedirectResponse
    {
        try {

            $borrow = $this->borrowingService->getBorrowDetails($id);

            Gate::authorize('return', $borrow);

            $dto = new ReturnBookDTO(
                borrowId: $borrow->id,
                bookId: $borrow->book_id,
                userId: $borrow->user_id,
                returnedAt: now()
            );

            $borrow = $this->borrowingService->returnBook($dto);

            $message = $borrow->late_fee > 0
                ? 'Book returned with late fee: $' . number_format($borrow->late_fee, 2)
                : 'Book returned successfully';

            return redirect()
                ->route('borrows.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Return failed: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->borrowingService->deleteBorrow($id);
        return redirect()
            ->route('borrows.index')
            ->with('success', 'Borrowing cancelled successfully');
    }
}
