<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        Log::info('AuthController::register - Starting user registration', [
            'email' => $request->input('email'),
            'name' => $request->string('name'),
            'ip_address' => $request->ip(),
        ]);

        try {
            $user = User::query()->create([
                'name' => $request->string('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'User', // Default role for new registrations
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('AuthController::register - User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (Exception $e) {
            Log::error('AuthController::register - Error during registration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
                'name' => $request->string('name'),
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['error' => 'An error occurred during registration.'], 500);
        }
    }

    /**
     * Authenticate a user and return a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        Log::info('AuthController::login - Starting user login', [
            'email' => $request->input('email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        try {
            if (! Auth::attempt($request->only('email', 'password'))) {
                Log::warning('AuthController::login - Invalid login attempt', [
                    'email' => $request->input('email'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);

                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('AuthController::login - User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            Log::error('AuthController::login - Error during login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['error' => 'An error occurred during login.'], 500);
        }
    }

    /**
     * Revoke the user's token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        Log::info('AuthController::logout - Starting user logout', [
            'user_id' => $request->user()->id ?? null,
            'email' => $request->user()->email ?? null,
            'ip_address' => $request->ip(),
        ]);

        try {
            $user = $request->user();
            $userId = $user->id;
            $userEmail = $user->email;

            $request->user()->currentAccessToken()->delete();

            Log::info('AuthController::logout - User logged out successfully', [
                'user_id' => $userId,
                'email' => $userEmail,
                'ip_address' => $request->ip(),
            ]);

            return response()->json(['message' => 'User logged out successfully']);
        } catch (Exception $e) {
            Log::error('AuthController::logout - Error during logout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()->id ?? null,
                'ip_address' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['error' => 'An error occurred during logout.'], 500);
        }
    }
}
