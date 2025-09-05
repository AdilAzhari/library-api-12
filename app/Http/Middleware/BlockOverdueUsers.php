<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlockOverdueUsers
{
    /**
     * Block users with overdue books from borrowing more books.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Check if user has any overdue books
            $hasOverdue = $user->borrows()
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                // Get overdue books for detailed message
                $overdueBooks = $user->borrows()
                    ->with(['book.activeBorrow', 'book.activeReservation'])
                    ->where('status', 'active')
                    ->where('due_date', '<', now())
                    ->get();

                $bookTitles = $overdueBooks->pluck('book.title')->take(3)->join(', ');
                $moreCount = $overdueBooks->count() > 3 ? ' and '.($overdueBooks->count() - 3).' more' : '';

                return redirect()->route('borrows.index', ['filter' => 'overdue'])
                    ->with('error', "You cannot borrow new books while you have overdue items: {$bookTitles}{$moreCount}. Please return them first.")
                    ->with('blocked_action', 'borrow');
            }

            // Also check for unpaid fines above threshold
            $unpaidFines = $user->fines()
                ->whereIn('status', ['pending', 'partial'])
                ->sum('amount') - $user->fines()
                ->whereIn('status', ['pending', 'partial'])
                ->sum('paid_amount');

            $fineThreshold = config('library.settings.max_unpaid_fines', 25.00);

            if ($unpaidFines > $fineThreshold) {
                return redirect()->route('fines.index')
                    ->with('error', "You have {$unpaidFines} in unpaid fines. Please pay your outstanding balance before borrowing more books.")
                    ->with('blocked_action', 'borrow');
            }
        }

        return $next($request);
    }
}
