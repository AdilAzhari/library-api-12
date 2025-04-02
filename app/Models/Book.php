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
use Laravel\Scout\Searchable;

#[ObservedBy([BookObserver::class])]
class Book extends Model
{
    use SoftDeletes, HasFactory, Searchable;

    protected $fillable = [
        'title', 'author', 'description', 'publication_year',
        'genre_id', 'cover_image', 'average_rating', 'ISBN', 'status'
    ];
    protected $appends = ['is_available'];

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === BookStatus::STATUS_AVAILABLE->value;
    }

    protected $casts = [
        'publication_year' => 'integer',
        'status' => BookStatus::class,
    ];

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
        ];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrow::class);
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

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && Storage::exists('public/' . $this->cover_image)) {
            return Storage::url($this->cover_image);
        }

        return asset('images/book-cover-placeholder.jpg');
    }

    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function state(): ReservedState|AvailableState|BorrowedState
    {
        return match ($this->status) {
            BookStatus::STATUS_AVAILABLE => new AvailableState($this),
            BookStatus::STATUS_RESERVED => new ReservedState($this),
            BookStatus::STATUS_BORROWED => new BorrowedState($this),
            default => throw new \InvalidArgumentException('Invalid book status'),
        };
    }
}
