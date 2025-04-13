<?php

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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Laravel\Scout\Searchable;

#[ObservedBy([BookObserver::class])]
class Book extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'description', 'publication_year',
        'genre_id', 'cover_image', 'average_rating', 'ISBN', 'status',
    ];

    protected $attributes = [
        'status' => BookStatus::STATUS_AVAILABLE->value,
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

    // Attribute Accessors
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === BookStatus::STATUS_AVAILABLE;
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

        return $this->activeReservation && $this->activeReservation->user_id === auth()->id();
    }

    // State Management
    public function state(): AvailableState|BorrowedState|ReservedState
    {
        return match ($this->status) {
            BookStatus::STATUS_AVAILABLE => new AvailableState($this),
            BookStatus::STATUS_BORROWED => new BorrowedState($this),
            BookStatus::STATUS_RESERVED => new ReservedState($this),
            default => throw new InvalidArgumentException('Invalid book status'),
        };
    }

    // Status Checks
    public function canBeBorrowed(): bool
    {
        return $this->status === BookStatus::STATUS_AVAILABLE ||
            ($this->status === BookStatus::STATUS_RESERVED &&
                optional($this->activeReservation)->isExpired());
    }

    public function canBeReserved(): bool
    {
        // User can't reserve a book they already have borrowed
        if ($this->activeBorrow && $this->activeBorrow->user_id === auth()->id()) {
            return false;
        }

        return $this->status === BookStatus::STATUS_AVAILABLE ||
            ($this->status === BookStatus::STATUS_BORROWED &&
                ! $this->activeReservation) ||
            ($this->status === BookStatus::STATUS_RESERVED &&
                optional($this->activeReservation)->isExpired());
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', BookStatus::STATUS_AVAILABLE->value)
            ->whereDoesntHave('activeBorrow')
            ->whereDoesntHave('activeReservation');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', BookStatus::STATUS_BORROWED->value);
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

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            $model->searchable();
        });

        static::updated(function ($model) {
            $model->searchable();
        });

        static::deleted(function ($model) {
            $model->unsearchable();
        });
    }

    public function hasPendingReservations(): bool
    {
        return $this->reservations()
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at')
            ->where('expires_at', '>', now())
            ->exists();
    }
}
