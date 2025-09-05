<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\BookStatus;
use App\Observers\BookObserver;
use App\States\AvailableState;
use App\States\BorrowedState;
use App\States\ReservedState;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Laravel\Scout\Searchable;

#[ObservedBy([BookObserver::class])]
final class Book extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'description', 'publication_year',
        'genre_id', 'cover_image', 'average_rating', 'ISBN', 'status',
    ];

    protected $attributes = [
        'status' => 'available',
    ];

    protected $appends = [
        'is_available',
        'cover_image_url',
        'has_active_reservation_for_user',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'status' => BookStatus::class,
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeBorrow(): HasOne
    {
        return $this->hasOne(Borrow::class)
            ->whereNull('returned_at')
            ->latest();
    }


    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where('expires_at', '>', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->latest();
    }

    public function readingLists(): BelongsToMany
    {
        return $this->belongsToMany(ReadingList::class, 'reading_list_books')
            ->withPivot(['added_at', 'notes', 'priority'])
            ->withTimestamps();
    }

    // Attribute Accessors
    public function getIsAvailableAttribute(): bool
    {
        return $this->canBeBorrowed();
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && Storage::exists('public/'.$this->cover_image)) {
            return Storage::url($this->cover_image);
        }

        return asset('images/book-cover-placeholder.jpg');
    }

    public function getHasActiveReservationForUserAttribute(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        // Check if the relation is already loaded to avoid lazy loading
        if ($this->relationLoaded('activeReservation')) {
            return $this->activeReservation && $this->activeReservation->user_id === auth()->id();
        }

        // Otherwise, use a query to check
        return $this->reservations()
            ->where('user_id', auth()->id())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->exists();
    }

    // State Management
    public function state(): AvailableState|BorrowedState|ReservedState
    {
        return match ($this->status) {
            BookStatus::AVAILABLE->value => new AvailableState($this),
            BookStatus::BORROWED->value => new BorrowedState($this),
            BookStatus::RESERVED->value => new ReservedState($this),
            default => throw new InvalidArgumentException('Invalid book status'),
        };
    }

    // Status Checks
    public function canBeBorrowed(): bool
    {
        // Available books can always be borrowed
        if ($this->status === BookStatus::AVAILABLE) {
            return true;
        }

        // Reserved books can be borrowed if the reservation is expired
        if ($this->status === BookStatus::RESERVED) {
            // Check if the relation is loaded to avoid lazy loading
            if ($this->relationLoaded('activeReservation')) {
                $activeReservation = $this->activeReservation;
            } else {
                // Use a query to check without loading the relationship
                $activeReservation = $this->activeReservation()->first();
            }

            return ! $activeReservation || $activeReservation->isExpired();
        }

        // Borrowed books cannot be borrowed again
        return false;
    }

    public function canBeReserved(): bool
    {
        // Check if activeBorrow relation is loaded to avoid lazy loading
        if ($this->relationLoaded('activeBorrow')) {
            $activeBorrow = $this->activeBorrow;
        } else {
            $activeBorrow = $this->activeBorrow()->first();
        }

        // User can't reserve a book they already have borrowed
        if ($activeBorrow && $activeBorrow->user_id === auth()->id()) {
            return false;
        }

        // Check if activeReservation relation is loaded to avoid lazy loading
        if ($this->relationLoaded('activeReservation')) {
            $activeReservation = $this->activeReservation;
        } else {
            $activeReservation = $this->activeReservation()->first();
        }

        return $this->status === BookStatus::AVAILABLE ||
            ($this->status === BookStatus::BORROWED && ! $activeReservation) ||
            ($this->status === BookStatus::RESERVED &&
                (! $activeReservation || $activeReservation->isExpired()));
    }

    public function canBeRenewed(): bool
    {
        // Book can be renewed if it doesn't have pending reservations
        return ! $this->hasPendingReservations();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', BookStatus::AVAILABLE->value)
            ->whereDoesntHave('activeBorrow')
            ->whereDoesntHave('activeReservation');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', BookStatus::BORROWED->value);
    }

    // Searchable
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'genre' => $this->genre->name ?? null,
            'publication_year' => $this->publication_year,
            'ISBN' => $this->ISBN,
            'status' => $this->status->value,
            'average_rating' => $this->average_rating,
            'available' => $this->is_available,
            'has_reservation' => $this->activeReservation()->exists(),
        ];
    }

    public function searchableAs(): string
    {
        return 'books';
    }

    public function currentBorrow(): HasOne
    {
        return $this->hasOne(Borrow::class)
            ->whereNull('returned_at');
    }

    public function currentReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->whereNull('canceled_at')
            ->whereNull('fulfilled_by_borrow_id')
            ->where('expires_at', '>', now());
    }

    public function hasPendingReservations(): bool
    {
        return $this->reservations()
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->where('expires_at', '>', now())
            ->exists();
    }

    protected static function boot(): void
    {
        parent::boot();

        self::created(function ($model): void {
            $model->searchable();
        });

        self::updated(function ($model): void {
            $model->searchable();
        });

        self::deleted(function ($model): void {
            $model->unsearchable();
        });
    }
}
