<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Services\BorrowingService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class BorrowController extends Controller
{
    use ApiMessages;

    public function __construct(protected BorrowingService $borrowingService) {}

    public function borrowBook(Request $request): JsonResponse
    {
        Log::info('BorrowController::borrowBook - Starting book borrow process', [
            'user_id' => $request->integer('user_id'),
            'book_id' => $request->integer('book_id'),
            'request_data' => $request->all(),
            'authenticated_user' => Auth::id(),
        ]);

        try {
            $dto = new BorrowBookDTO(
                $request->integer('book_id'),
                $request->integer('user_id'),
            );

            $borrowing = $this->borrowingService->borrowBook($dto);

            Log::info('BorrowController::borrowBook - Book borrowed successfully', [
                'borrow_id' => $borrowing->id ?? null,
                'user_id' => $dto->userId,
                'book_id' => $dto->bookId,
            ]);

            return $this->successResponse('Book borrowed successfully', $borrowing, 201);
        } catch (Exception $e) {
            Log::error('BorrowController::borrowBook - Error borrowing book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->integer('user_id'),
                'book_id' => $request->integer('book_id'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while borrowing the book.');
        }
    }

    public function returnBook(Request $request): JsonResponse
    {
        Log::info('BorrowController::returnBook - Starting book return process', [
            'user_id' => $request->integer('user_id'),
            'book_id' => $request->integer('book_id'),
            'request_data' => $request->all(),
            'authenticated_user' => Auth::id(),
        ]);

        try {
            // Find the active borrow record first
            $borrow = Borrow::where('book_id', $request->integer('book_id'))
                ->where('user_id', $request->integer('user_id'))
                ->whereNull('returned_at')
                ->firstOrFail();

            $dto = new ReturnBookDTO(
                borrowId: $borrow->id,
                bookId: $request->integer('book_id'),
                userId: $request->integer('user_id'),
            );

            $borrowing = $this->borrowingService->returnBook($dto);

            Log::info('BorrowController::returnBook - Book returned successfully', [
                'borrow_id' => $borrowing->id ?? null,
                'user_id' => $dto->userId,
                'book_id' => $dto->bookId,
            ]);

            return $this->successResponse('Book returned successfully', $borrowing);
        } catch (Exception $e) {
            Log::error('BorrowController::returnBook - Error returning book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->integer('user_id'),
                'book_id' => $request->integer('book_id'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while returning the book.');
        }
    }

    /**
     * Get user's active borrowed books
     */
    public function active(Request $request)
    {
        Log::info('BorrowController::active - Getting active borrows', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
        ]);

        try {
            $user = Auth::user();
            $activeBorrows = $user->borrows()
                ->where('status', 'active')
                ->with(['book.genre'])
                ->orderBy('borrowed_at', 'desc')
                ->get();

            Log::info('BorrowController::active - Active borrows retrieved successfully', [
                'user_id' => Auth::id(),
                'count' => $activeBorrows->count(),
            ]);

            return $this->successResponse('Active borrows retrieved successfully', $activeBorrows);
        } catch (Exception $e) {
            Log::error('BorrowController::active - Error getting active borrows', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('Unable to get active borrows');
        }
    }

    /**
     * Get user's overdue books
     */
    public function overdue(Request $request)
    {
        try {
            $user = Auth::user();
            $overdueBorrows = $user->borrows()
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->with(['book.genre'])
                ->orderBy('due_date')
                ->get()
                ->map(function ($borrow) {
                    $borrow->days_overdue = now()->diffInDays($borrow->due_date);

                    return $borrow;
                });

            return $this->successResponse('Overdue borrows retrieved successfully', $overdueBorrows);
        } catch (Exception $e) {
            Log::error('Error getting overdue borrows: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get overdue borrows');
        }
    }

    /**
     * Get books due soon (within next 3 days)
     */
    public function dueSoon(Request $request)
    {
        try {
            $user = Auth::user();
            $dueSoonBorrows = $user->borrows()
                ->where('status', 'active')
                ->whereBetween('due_date', [now(), now()->addDays(3)])
                ->with(['book.genre'])
                ->orderBy('due_date')
                ->get()
                ->map(function ($borrow) {
                    $borrow->days_until_due = now()->diffInDays($borrow->due_date, false);

                    return $borrow;
                });

            return $this->successResponse('Due soon borrows retrieved successfully', $dueSoonBorrows);
        } catch (Exception $e) {
            Log::error('Error getting due soon borrows: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get due soon borrows');
        }
    }

    /**
     * Get user's borrowing history
     */
    public function history(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = min($request->integer('per_page', 15), 50);

            $borrowHistory = $user->borrows()
                ->where('status', 'returned')
                ->with(['book.genre'])
                ->orderBy('returned_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse('Borrow history retrieved successfully', $borrowHistory);
        } catch (Exception $e) {
            Log::error('Error getting borrow history: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get borrow history');
        }
    }

    /**
     * Renew a borrowed book
     */
    public function renew(Request $request, Borrow $borrow)
    {
        Log::info('BorrowController::renew - Starting book renewal', [
            'borrow_id' => $borrow->id,
            'user_id' => Auth::id(),
            'book_id' => $borrow->book_id,
            'current_due_date' => $borrow->due_date,
        ]);

        try {
            // Check if user owns this borrow (middleware should handle this)
            if ($borrow->user_id !== Auth::id()) {
                Log::warning('BorrowController::renew - Unauthorized renewal attempt', [
                    'borrow_id' => $borrow->id,
                    'borrow_user_id' => $borrow->user_id,
                    'authenticated_user_id' => Auth::id(),
                ]);

                return $this->errorResponse('Unauthorized', 403);
            }

            $renewedBorrow = $this->borrowingService->renewBook($borrow);

            Log::info('BorrowController::renew - Book renewed successfully', [
                'borrow_id' => $renewedBorrow->id,
                'user_id' => Auth::id(),
                'new_due_date' => $renewedBorrow->due_date,
            ]);

            return $this->successResponse('Book renewed successfully', $renewedBorrow);
        } catch (Exception $e) {
            Log::error('BorrowController::renew - Error renewing book', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'borrow_id' => $borrow->id,
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('Unable to renew book');
        }
    }

    /**
     * Check if a book can be renewed
     */
    public function canRenew(Request $request, Borrow $borrow)
    {
        try {
            // Check if user owns this borrow
            if ($borrow->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $canRenew = $this->borrowingService->canRenewBook($borrow);
            $renewalInfo = [
                'can_renew' => $canRenew,
                'renewals_used' => $borrow->renewal_count ?? 0,
                'max_renewals' => config('library.max_renewals', 2),
                'has_reservations' => $borrow->book->hasPendingReservations(),
                'is_overdue' => $borrow->due_date < now(),
            ];

            return $this->successResponse('Renewal eligibility checked', $renewalInfo);
        } catch (Exception $e) {
            Log::error('Error checking renewal eligibility: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to check renewal eligibility');
        }
    }

    /**
     * List borrowed books (legacy endpoint)
     */
    public function listBorrowedBooks(Request $request)
    {
        return $this->active($request);
    }
}
