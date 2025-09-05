<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

final class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        Log::info('AuthenticatedSessionController::create - Loading login page', [
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        try {
            return Inertia::render('Auth/Login', [
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
            ]);
        } catch (Exception $e) {
            Log::error('AuthenticatedSessionController::create - Error loading login page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => request()->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('welcome')->with('error', 'Unable to load login page.');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        Log::info('AuthenticatedSessionController::store - Starting login attempt', [
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        try {
            $request->authenticate();

            $request->session()->regenerate();

            $user = Auth::user();

            Log::info('AuthenticatedSessionController::store - User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role ?? 'user',
                'is_admin' => $user->isAdmin ?? false,
                'ip_address' => $request->ip(),
            ]);

            if (Auth::user()->isAdmin) {
                return to_route('admin.dashboard');
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (Exception $e) {
            Log::error('AuthenticatedSessionController::store - Error during login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput($request->only('email'))
                ->with('error', 'An error occurred during login.');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Log::info('AuthenticatedSessionController::destroy - Starting logout', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? null,
            'ip_address' => $request->ip(),
        ]);

        try {
            $user = Auth::user();
            $userId = $user->id ?? null;
            $userEmail = $user->email ?? null;

            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            Log::info('AuthenticatedSessionController::destroy - User logged out successfully', [
                'user_id' => $userId,
                'email' => $userEmail,
                'ip_address' => $request->ip(),
            ]);

            return redirect('/');
        } catch (Exception $e) {
            Log::error('AuthenticatedSessionController::destroy - Error during logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('/')->with('error', 'An error occurred during logout.');
        }
    }
}
