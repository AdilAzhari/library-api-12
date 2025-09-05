<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Borrow;
use App\Models\Fine;
use App\Models\Reservation;
use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class VerifyOwnership
{
    /**
     * Verify that the authenticated user owns the requested resource.
     */
    public function handle(Request $request, Closure $next, string $model): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $resource = $request->route($model);
        $user = auth()->user();

        // Handle case where route parameter doesn't match the model name
        if ($resource === null) {
            // For reservation creation routes, the parameter is 'book', not 'reservation'
            if ($model === 'reservation' && $request->route('book')) {
                // For reservation creation, we don't need to verify ownership of a reservation
                // that doesn't exist yet. Just continue.
                return $next($request);
            }
            abort(404, 'Resource not found');
        }

        // Handle case where we get a string instead of model instance or ID
        if (is_string($resource) && ! is_numeric($resource)) {
            // This might be the route parameter name instead of value
            // For reservation creation routes, skip ownership verification
            if ($model === 'reservation') {
                return $next($request);
            }
            abort(404, 'Resource not found');
        }

        // If we get an ID instead of a model instance, fetch the model
        if (is_numeric($resource)) {
            $resource = match ($model) {
                'borrow' => App\Models\Borrow::query()->findOrFail($resource),
                'reservation' => App\Models\Reservation::query()->findOrFail($resource),
                'fine' => App\Models\Fine::query()->findOrFail($resource),
                'reading-list', 'readingList' => App\Models\ReadingList::query()->findOrFail($resource),
                default => throw new InvalidArgumentException("Unknown model for ownership verification: {$model}")
            };
        }

        // Verify ownership
        if ($resource->user_id !== $user->id) {
            abort(403, 'You do not have permission to access this resource.');
        }

        // Additional checks for specific models
        switch ($model) {
            case 'borrow':
                // Ensure borrow is still active for certain actions
                if (in_array($request->route()->getActionMethod(), ['showReturn', 'processReturn', 'renew'])) {
                    if ($resource->status !== 'active') {
                        return redirect()->route('borrows.index')
                            ->with('error', 'This borrow record is no longer active.');
                    }
                }
                break;

            case 'reservation':
                // Ensure reservation is in correct state for pickup
                if ($request->route()->getActionMethod() === 'confirmPickup') {
                    if ($resource->status !== 'ready') {
                        return redirect()->route('reservations.index')
                            ->with('error', 'This reservation is not ready for pickup.');
                    }
                }
                break;

            case 'fine':
                // Ensure fine is payable
                if (in_array($request->route()->getActionMethod(), ['showPayment', 'processPayment'])) {
                    if (in_array($resource->status, ['paid', 'waived'])) {
                        return redirect()->route('fines.index')
                            ->with('info', 'This fine has already been resolved.');
                    }
                }
                break;
        }

        return $next($request);
    }
}
