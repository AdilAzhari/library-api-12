<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

#[ObservedBy('App\Observers\BorrowObserver')]
class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id', 'user_id', 'borrowed_at',
        'due_date', 'returned_at', 'renewal_count', 'late_fee', 'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fulfilledReservation(): HasOne
    {
        return $this->hasOne(Reservation::class, 'fulfilled_by_borrow_id');
    }

    public function isOverdue(): bool
    {
        return !$this->returned_at && $this->due_date->isPast();
    }

    public function renew(int $days = 14): bool
    {
        if ($this->returned_at || $this->renewal_count >= config('library.max_renewals', 3)) {
            return false;
        }

        $this->update([
            'due_date' => $this->due_date->addDays($days),
            'renewal_count' => $this->renewal_count + 1
        ]);

        return true;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    public function scopeOverdue($query)
    {
        return $query->active()
            ->where('due_date', '<', now());
    }
}
