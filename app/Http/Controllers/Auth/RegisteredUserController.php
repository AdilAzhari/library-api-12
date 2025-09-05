<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        Log::info('RegisteredUserController::create - Loading registration page', [
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        try {
            return Inertia::render('Auth/Register');
        } catch (Exception $e) {
            Log::error('RegisteredUserController::create - Error loading registration page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => request()->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('welcome')->with('error', 'Unable to load registration page.');
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('RegisteredUserController::store - Starting user registration', [
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::query()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'User',
            ]);

            event(new Registered($user));

            Auth::login($user);

            Log::info('RegisteredUserController::store - User registered and logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
                'ip_address' => $request->ip(),
            ]);

            return redirect(route('dashboard', absolute: false));
        } catch (ValidationException $e) {
            Log::warning('RegisteredUserController::store - Validation failed during registration', [
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'errors' => $e->errors(),
                'ip_address' => $request->ip(),
            ]);

            throw $e; // Re-throw validation exceptions to let Laravel handle them
        } catch (Exception $e) {
            Log::error('RegisteredUserController::store - Error during registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput($request->only('name', 'email'))
                ->with('error', 'An error occurred during registration.');
        }
    }
}
