<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

final class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'rating',
        'comment',
        'is_approved',
        'approved_at',
        'approved_by',
        'is_featured',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'helpful_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'is_pending',
        'formatted_date',
        'can_be_edited',
    ];

    /**
     * Check if user can review this book
     */
    public static function canUserReviewBook(int $userId, int $bookId): bool
    {
        // User must have borrowed the book to review it
        $hasBorrowed = Borrow::query()->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();

        if (! $hasBorrowed) {
            return false;
        }

        // User can only have one review per book
        $hasExistingReview = self::query()->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();

        return ! $hasExistingReview;
    }

    /**
     * Get review statistics for a book
     */
    public static function getBookReviewStats(int $bookId): array
    {
        $reviews = self::forBook($bookId)->approved();

        return [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('rating') ?? 0, 2),
            'rating_breakdown' => [
                5 => $reviews->clone()->where('rating', 5)->count(),
                4 => $reviews->clone()->where('rating', 4)->count(),
                3 => $reviews->clone()->where('rating', 3)->count(),
                2 => $reviews->clone()->where('rating', 2)->count(),
                1 => $reviews->clone()->where('rating', 1)->count(),
            ],
        ];
    }

    /**
     * Relationships
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->approved();
    }

    public function scopeForBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessors
     */
    public function getIsPendingAttribute(): bool
    {
        return ! $this->is_approved;
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M j, Y');
    }

    public function getCanBeEditedAttribute(): bool
    {
        // Users can edit their own reviews within 24 hours
        return auth()->id() === $this->user_id &&
               $this->created_at->gt(now()->subDay());
    }

    /**
     * Methods
     */
    public function approve(?int $approvedById = null): bool
    {
        return $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approvedById ?? auth()->id(),
        ]);
    }

    public function reject(): bool
    {
        return $this->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    public function markAsFeatured(): bool
    {
        if (! $this->is_approved) {
            return false;
        }

        return $this->update(['is_featured' => true]);
    }

    public function unmarkAsFeatured(): bool
    {
        return $this->update(['is_featured' => false]);
    }

    public function incrementHelpfulCount(): bool
    {
        return $this->increment('helpful_count');
    }

    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Update the average rating for the associated book
     */
    public function updateBookAverageRating(): void
    {
        $averageRating = $this->book
            ->reviews()
            ->approved()
            ->avg('rating');

        $this->book->update([
            'average_rating' => $averageRating ? round((float) $averageRating, 2) : 0,
        ]);
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function booted(): void
    {
        self::saving(function ($review): void {
            // Validate rating is between 1-5
            if ($review->rating < 1 || $review->rating > 5) {
                throw new InvalidArgumentException('Rating must be between 1 and 5');
            }

            // Auto-approve reviews if feature is disabled in config
            if (! config('library.features.review_approval', false)) {
                $review->is_approved = true;
                $review->approved_at = now();
            }
        });

        self::created(function ($review): void {
            // Update book average rating
            $review->updateBookAverageRating();
        });

        self::updated(function ($review): void {
            // Update book average rating when review is updated
            $review->updateBookAverageRating();
        });

        self::deleted(function ($review): void {
            // Update book average rating when review is deleted
            $review->updateBookAverageRating();
        });
    }
}
