<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enum\UserRoles;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

final class AdminUserController extends Controller
{
    public function index()
    {
        Log::info('AdminUserController::index - Loading users index page', [
            'user_id' => Auth::id(),
            'filters' => request()->only(['search', 'role']),
            'page' => request('page', 1),
        ]);

        try {
            $users = User::query()
                ->when(request('search'), function ($query, $search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->when(request('role'), function ($query, $role): void {
                    $query->where('role', $role);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString();

            Log::info('AdminUserController::index - Users loaded successfully', [
                'user_id' => Auth::id(),
                'total_users' => $users->total(),
                'current_page' => $users->currentPage(),
            ]);

            return Inertia::render('Admin/Users/Index', [
                'users' => $users,
                'filters' => request()->only(['search', 'role']),
                'roles' => UserRoles::values(),
            ]);
        } catch (Exception $e) {
            Log::error('AdminUserController::index - Error loading users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'An error occurred while loading users.');
        }
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
        ]);
    }

    // Store the new user
    public function store(Request $request)
    {
        Log::info('AdminUserController::store - Starting user creation', [
            'admin_user_id' => Auth::id(),
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'role' => $request->input('role'),
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'role' => 'required|in:user,librarian,admin',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('AdminUserController::store - User created successfully', [
                'admin_user_id' => Auth::id(),
                'created_user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully');
        } catch (Exception $e) {
            Log::error('AdminUserController::store - Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_user_id' => Auth::id(),
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the user.');
        }
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => $user->only('id', 'name', 'email', 'role'),
        ]);
    }

    public function update(User $user)
    {
        Log::info('AdminUserController::update - Starting user update', [
            'admin_user_id' => Auth::id(),
            'target_user_id' => $user->id,
            'original_email' => $user->email,
            'original_name' => $user->name,
            'request_data' => request()->all(),
        ]);

        try {
            $validated = request()->validate([
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required',
            ]);

            $user->update($validated);

            Log::info('AdminUserController::update - User updated successfully', [
                'admin_user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'new_email' => $user->fresh()->email,
                'new_name' => $user->fresh()->name,
                'new_role' => $user->fresh()->role,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully');
        } catch (Exception $e) {
            Log::error('AdminUserController::update - Error updating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the user.');
        }
    }

    public function destroy(User $user)
    {
        Log::info('AdminUserController::destroy - Starting user deletion', [
            'admin_user_id' => Auth::id(),
            'target_user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        try {
            $userEmail = $user->email;
            $userName = $user->name;
            $userId = $user->id;

            $user->delete();

            Log::info('AdminUserController::destroy - User deleted successfully', [
                'admin_user_id' => Auth::id(),
                'deleted_user_id' => $userId,
                'email' => $userEmail,
                'name' => $userName,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully');
        } catch (Exception $e) {
            Log::error('AdminUserController::destroy - Error deleting user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_user_id' => Auth::id(),
                'target_user_id' => $user->id,
                'email' => $user->email,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the user.');
        }
    }
}
