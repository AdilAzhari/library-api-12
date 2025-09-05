<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\UserCreateDTO;
use App\DTO\UserUpdateDTO;
use App\Enum\UserRoles;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

final class UserService
{
    public function create(UserCreateDTO $dto): User
    {
        // Validate unique email
        if (User::query()->where('email', $dto->email)->exists()) {
            throw new InvalidArgumentException('Email already exists');
        }

        $user = App\Models\User::query()->create($dto->toModel());

        // Create default reading list for the user
        app(ReadingListService::class)->createDefault($user->id);

        // Issue library card if auto-issue is enabled
        if (config('library.features.auto_issue_card', true)) {
            app(LibraryCardService::class)->issue($user->id);
        }

        return $user->fresh();
    }

    public function update(User $user, UserUpdateDTO $dto): User
    {
        $data = $dto->toModel();

        // Check email uniqueness if being updated
        if (isset($data['email']) && User::query()->where('email', $data['email'])->where('id', '!=', $user->id)->exists()) {
            throw new InvalidArgumentException('Email already exists');
        }

        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        // Check if user has active borrows
        if ($user->activeBorrows()->exists()) {
            throw new InvalidArgumentException('Cannot delete user with active borrows');
        }

        // Check if user has outstanding fines
        if ($user->hasOutstandingFines()) {
            throw new InvalidArgumentException('Cannot delete user with outstanding fines');
        }

        return $user->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with(['activeBorrows', 'unpaidFines']);

        if (isset($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['has_overdue'])) {
            $query->whereHas('activeBorrows', function ($q): void {
                $q->where('due_date', '<', now());
            });
        }

        if (isset($filters['has_fines'])) {
            $query->whereHas('unpaidFines');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getStats(User $user): array
    {
        return [
            'total_borrows' => $user->borrows()->count(),
            'active_borrows' => $user->activeBorrows()->count(),
            'overdue_books' => $user->activeBorrows()->where('due_date', '<', now())->count(),
            'total_reviews' => $user->reviews()->count(),
            'approved_reviews' => $user->reviews()
            // ->approved()
                ->count(),
            'reading_lists' => $user->readingLists()->count(),
            'outstanding_fines' => $user->getOutstandingFineAmount(),
            'unpaid_fines_count' => $user->unpaidFines()->count(),
            'library_card_status' => $user->activeLibraryCard?->status ?? 'none',
            'member_since' => $user->created_at->format('M Y'),
            'last_borrow' => $user->borrows()->latest()->first()?->created_at?->format('M j, Y'),
        ];
    }

    public function changeRole(User $user, UserRoles $newRole): User
    {
        // Only admins can change roles to admin
        if ($newRole === UserRoles::ADMIN && ! auth()->user()->role === 'Admin') {
            throw new InvalidArgumentException('Only admins can assign admin role');
        }

        $user->update(['role' => $newRole->value]);

        return $user->fresh();
    }

    public function changePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return $user;
    }

    public function getActiveUsers(int $days = 30): Collection
    {
        return App\Models\User::query()->whereHas('borrows', function ($query) use ($days): void {
            $query->where('created_at', '>=', now()->subDays($days));
        })->get();
    }

    public function getUsersWithOverdueBooks(): Collection
    {
        return App\Models\User::query()->whereHas('activeBorrows', function ($query): void {
            $query->where('due_date', '<', now());
        })->with(['activeBorrows' => function ($query): void {
            $query->where('due_date', '<', now());
        }])->get();
    }

    public function getUsersWithFines(): Collection
    {
        return App\Models\User::query()->whereHas('unpaidFines')
            ->with('unpaidFines')
            ->get();
    }

    public function suspend(User $user, ?string $reason = null): User
    {
        // Suspend library card
        if ($user->activeLibraryCard) {
            $user->activeLibraryCard->suspend($reason);
        }

        // Add suspension note
        $user->update([
            'notes' => ($user->notes ?? '')."\nSuspended: ".($reason ?? 'No reason provided').' - '.now()->format('Y-m-d H:i:s'),
        ]);

        return $user->fresh();
    }

    public function unsuspend(User $user): User
    {
        // Reactivate library card
        if ($user->activeLibraryCard && $user->activeLibraryCard->status === 'suspended') {
            $user->activeLibraryCard->activate();
        }

        return $user->fresh();
    }
}
