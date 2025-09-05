<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GenreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Genre extends Model
{
    /** @use HasFactory<GenreFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
