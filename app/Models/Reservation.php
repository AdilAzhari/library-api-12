<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id', 'user_id', 'reserved_at',
        'expires_at', 'fulfilled_by_borrow_id', 'canceled_at'
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrow::class, 'fulfilled_by_borrow_id');
    }

    public function fulfill(Borrow $borrowing): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return $this->update([
            'fulfilled_by_borrow_id' => $borrowing->id
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at');
    }

    public function scopeFulfilled($query)
    {
        return $query->whereNotNull('fulfilled_by_borrow_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isFulfilled(): bool
    {
        return $this->fulfilled_by_borrow_id !== null;
    }

    public function isCanceled(): bool
    {
        return $this->canceled_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now()
            && !$this->isFulfilled()
            && !$this->isCanceled();
    }
    public function activeForUser($query, int $userId, int $bookId = null)
    {
        $query = $query->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->whereNull('fulfilled_by_borrow_id')
            ->whereNull('canceled_at');

        if ($bookId) {
            $query->where('book_id', $bookId);
        }

        return $query;
    }

}
