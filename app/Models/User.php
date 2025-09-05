<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enum\UserRoles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['is_admin'];

    public function getIsAdminAttribute(): bool
    {
        return $this->role === UserRoles::ADMIN;
    }

    /**
     * Scope to filter admin users
     */
    public function scopeIsAdmin($query)
    {
        return $query->where('role', 'Admin');
    }

    /**
     * Scope to filter by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * User's borrowed books
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * User's current active borrows
     */
    public function activeBorrows(): HasMany
    {
        return $this->hasMany(Borrow::class)->whereNull('returned_at');
    }

    /**
     * User's book reservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * User's active reservations
     */
    public function activeReservations(): HasMany
    {
        return $this->hasMany(Reservation::class)
            ->where('expires_at', '>', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at');
    }

    /**
     * User's book reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * User's library cards
     */
    public function libraryCards(): HasMany
    {
        return $this->hasMany(LibraryCard::class);
    }

    /**
     * User's fines
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * User's unpaid fines
     */
    public function unpaidFines(): HasMany
    {
        return $this->hasMany(Fine::class)->whereIn('status', ['pending', 'partial']);
    }

    /**
     * User's reading lists
     */
    public function readingLists(): HasMany
    {
        return $this->hasMany(ReadingList::class);
    }

    /**
     * User's default reading list
     */
    public function defaultReadingList(): HasOne
    {
        return $this->hasOne(ReadingList::class)->where('is_default', true);
    }

    /**
     * User's active library card
     */
    public function activeLibraryCard(): HasOne
    {
        return $this->hasOne(LibraryCard::class)
            ->where('status', 'active')
            ->where('expires_date', '>', now());
    }

    /**
     * Check if user can borrow more books
     */
    public function canBorrowMoreBooks(): bool
    {
        $maxLoans = config('library.settings.max_loans_per_user', 5);

        return $this->activeBorrows()->count() < $maxLoans;
    }

    /**
     * Check if user has overdue books
     */
    public function hasOverdueBooks(): bool
    {
        return $this->activeBorrows()
            ->where('due_date', '<', now())
            ->exists();
    }

    /**
     * Check if user has outstanding fines
     */
    public function hasOutstandingFines(): bool
    {
        return $this->unpaidFines()->exists();
    }

    /**
     * Get user's total outstanding fine amount
     */
    public function getOutstandingFineAmount(): float
    {
        return $this->unpaidFines()->sum('amount') - $this->unpaidFines()->sum('paid_amount');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoles::class,
        ];
    }
}
