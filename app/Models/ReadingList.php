<?php

declare(strict_types=1);

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class ReadingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'is_default',
        'color',
        'icon',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'books_count',
        'formatted_created_date',
        'share_url',
    ];

    /**
     * Static methods
     */
    public static function createDefaultForUser(int $userId): self
    {
        return self::create([
            'user_id' => $userId,
            'name' => 'My Reading List',
            'description' => 'Default reading list',
            'is_default' => true,
            'is_public' => false,
            'color' => '#3B82F6',
            'icon' => 'heart',
        ]);
    }

    public static function getPopularBooks(int $limit = 10): array
    {
        return DB::table('reading_list_books')
            ->select('book_id', DB::raw('COUNT(*) as count'))
            ->groupBy('book_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->pluck('book_id')
            ->toArray();
    }

    /**
     * Get user's default reading list
     */
    public static function getUserDefaultList(int $userId): ?self
    {
        return self::forUser($userId)->default()->first();
    }

    /**
     * Get or create user's default reading list
     */
    public static function getOrCreateDefaultForUser(int $userId): self
    {
        $defaultList = self::getUserDefaultList($userId);

        if (! $defaultList) {
            $defaultList = self::createDefaultForUser($userId);
        }

        return $defaultList;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'reading_list_books')
            ->withPivot(['added_at', 'notes', 'priority'])
            ->withTimestamps()
            ->orderByPivot('added_at', 'desc');
    }

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Accessors
     */
    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    public function getFormattedCreatedDateAttribute(): string
    {
        return $this->created_at->format('M j, Y');
    }

    public function getShareUrlAttribute(): ?string
    {
        if (! $this->is_public) {
            return null;
        }

        return url("/reading-lists/public/{$this->id}");
    }

    /**
     * Methods
     */
    public function addBook(int $bookId, array $options = []): bool
    {
        if ($this->books()->where('book_id', $bookId)->exists()) {
            return false; // Book already in list
        }

        $this->books()->attach($bookId, array_merge([
            'added_at' => now(),
            'priority' => $options['priority'] ?? 1,
            'notes' => $options['notes'] ?? null,
        ], $options));

        return true;
    }

    public function removeBook(int $bookId): bool
    {
        return $this->books()->detach($bookId) > 0;
    }

    public function hasBook(int $bookId): bool
    {
        return $this->books()->where('book_id', $bookId)->exists();
    }

    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    public function setAsDefault(): bool
    {
        // First, unset any existing default for this user
        self::query()->where('user_id', $this->user_id)
            ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        self::creating(function ($readingList): void {
            // Ensure only one default list per user
            if ($readingList->is_default) {
                static::query()->where('user_id', $readingList->user_id)
                    ->update(['is_default' => false]);
            }

            // Set default color if not provided
            if (empty($readingList->color)) {
                $readingList->color = '#3B82F6'; // Blue
            }

            // Set default icon if not provided
            if (empty($readingList->icon)) {
                $readingList->icon = 'book-open';
            }
        });

        self::updating(function ($readingList): void {
            // Ensure only one default list per user
            if ($readingList->is_default && $readingList->isDirty('is_default')) {
                static::query()->where('user_id', $readingList->user_id)
                    ->where('id', '!=', $readingList->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
