<?php

namespace App\Models;

use App\Observers\BookObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
#[ObservedBy([BookObserver::class])]
class Book extends Model
{
    use SoftDeletes, HasFactory, Searchable;
    protected $fillable = [
        'title',
        'author',
        'description',
        'publication_year',
        'genre_id',
        'cover_image',
        'is_borrowed',
        'average_rating',
    ];
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
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

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
