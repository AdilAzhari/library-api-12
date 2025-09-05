<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckOverdueBooks
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Get overdue borrows count
            $overdueCount = $user->borrows()
                ->where('status', 'active')
                ->where('due_date', '<', now())
                ->count();

            // Add overdue alert to session for UI display
            if ($overdueCount > 0) {
                session()->flash('overdue_alert', [
                    'count' => $overdueCount,
                    'message' => "You have {$overdueCount} overdue book(s). Please return them as soon as possible.",
                ]);
            }

            // Get due soon (within 3 days) for proactive alerts
            $dueSoonCount = $user->borrows()
                ->where('status', 'active')
                ->whereBetween('due_date', [now(), now()->addDays(3)])
                ->count();

            if ($dueSoonCount > 0) {
                session()->flash('due_soon_alert', [
                    'count' => $dueSoonCount,
                    'message' => "You have {$dueSoonCount} book(s) due within 3 days.",
                ]);
            }
        }

        return $next($request);
    }
}
