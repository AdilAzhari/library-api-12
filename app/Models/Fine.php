<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Fine extends Model
{
    use HasFactory;

    // Legacy constants for backwards compatibility - use Enums instead
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_WAIVED = 'waived';

    public const STATUS_CANCELLED = 'cancelled';

    public const REASON_OVERDUE = 'overdue';

    public const REASON_LOST_BOOK = 'lost_book';

    public const REASON_DAMAGED_BOOK = 'damaged_book';

    public const REASON_LATE_RETURN = 'late_return';

    public const REASON_PROCESSING_FEE = 'processing_fee';

    public const REASON_OTHER = 'other';

    protected $fillable = [
        'user_id',
        'borrow_id',
        'book_id',
        'amount',
        'reason',
        'status',
        'description',
        'due_date',
        'paid_at',
        'paid_amount',
        'payment_method',
        'waived_by',
        'waived_at',
        'waiver_reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'waived_at' => 'datetime',
        'reason' => \App\Enum\FineReason::class,
        'status' => \App\Enum\FineStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'is_paid',
        'is_overdue',
        'is_waived',
        'balance_due',
        'formatted_amount',
        'status_label',
    ];

    /**
     * Static methods
     */
    public static function createOverdueFine(Borrow $borrow): self
    {
        $overdueDays = now()->diffInDays($borrow->due_date);
        $dailyRate = config('library.settings.overdue_fee_per_day', 0.50);
        $amount = $overdueDays * $dailyRate;

        return self::create([
            'user_id' => $borrow->user_id,
            'borrow_id' => $borrow->id,
            'book_id' => $borrow->book_id,
            'amount' => $amount,
            'reason' => self::REASON_OVERDUE,
            'description' => "Overdue fine for {$overdueDays} days",
        ]);
    }

    public static function createLostBookFine(Borrow $borrow, float $replacementCost): self
    {
        return self::create([
            'user_id' => $borrow->user_id,
            'borrow_id' => $borrow->id,
            'book_id' => $borrow->book_id,
            'amount' => $replacementCost,
            'reason' => self::REASON_LOST_BOOK,
            'description' => 'Replacement cost for lost book',
        ]);
    }

    public static function createDamageFine(Borrow $borrow, float $repairCost): self
    {
        return self::create([
            'user_id' => $borrow->user_id,
            'borrow_id' => $borrow->id,
            'book_id' => $borrow->book_id,
            'amount' => $repairCost,
            'reason' => self::REASON_DAMAGED_BOOK,
            'description' => 'Repair cost for damaged book',
        ]);
    }

    /**
     * Get fine statistics
     */
    public static function getStatistics(): array
    {
        $totalFines = self::sum('amount');
        $paidFines = self::paid()->sum('amount');
        $outstandingFines = self::unpaid()->sum('amount');

        return [
            'total_fines' => $totalFines,
            'paid_fines' => $paidFines,
            'outstanding_fines' => $outstandingFines,
            'total_count' => self::count(),
            'pending_count' => self::pending()->count(),
            'overdue_count' => self::overdue()->count(),
            'average_fine' => self::avg('amount'),
            'largest_fine' => self::max('amount'),
        ];
    }

    /**
     * Get user fine summary
     */
    public static function getUserSummary(int $userId): array
    {
        $userFines = self::forUser($userId);

        return [
            'total_fines' => $userFines->sum('amount'),
            'outstanding_balance' => $userFines->unpaid()->sum('amount'),
            'paid_fines' => $userFines->paid()->sum('amount'),
            'pending_count' => $userFines->pending()->count(),
            'overdue_count' => $userFines->overdue()->count(),
            'has_outstanding_fines' => $userFines->unpaid()->exists(),
        ];
    }

    /**
     * Process overdue fines for all overdue borrows
     */
    public static function processOverdueFines(): int
    {
        $overdueBorrows = Borrow::active()
            ->where('due_date', '<', now())
            ->whereDoesntHave('fines', function ($query): void {
                $query->where('reason', self::REASON_OVERDUE)
                    ->where('created_at', '>', now()->subDay());
            })
            ->get();

        $created = 0;
        foreach ($overdueBorrows as $borrow) {
            self::createOverdueFine($borrow);
            $created++;
        }

        return $created;
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function borrow(): BelongsTo
    {
        return $this->belongsTo(Borrow::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function waivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    /**
     * Accessors
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->lt(now()) &&
               in_array($this->status, [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    public function getIsWaivedAttribute(): bool
    {
        return $this->status === self::STATUS_WAIVED;
    }

    public function getBalanceDueAttribute(): float
    {
        if ($this->is_paid || $this->is_waived) {
            return 0.0;
        }

        return $this->amount - ($this->paid_amount ?? 0);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$'.number_format($this->amount, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PAID => 'Paid',
            self::STATUS_PARTIAL => 'Partial Payment',
            self::STATUS_WAIVED => 'Waived',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Methods
     */
    public function markAsPaid(
        ?float $amount = null,
        string $paymentMethod = 'cash',
        ?string $notes = null
    ): bool {
        $paymentAmount = $amount ?? $this->balance_due;

        if ($paymentAmount >= $this->balance_due) {
            // Full payment
            return $this->update([
                'status' => self::STATUS_PAID,
                'paid_amount' => $this->amount,
                'paid_at' => now(),
                'payment_method' => $paymentMethod,
                'notes' => $notes,
            ]);
        }

        // Partial payment
        return $this->update([
            'status' => self::STATUS_PARTIAL,
            'paid_amount' => ($this->paid_amount ?? 0) + $paymentAmount,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);

    }

    public function waive(int $waivedBy, ?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_WAIVED,
            'waived_by' => $waivedBy,
            'waived_at' => now(),
            'waiver_reason' => $reason ?? 'Administrative waiver',
        ]);
    }

    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ? "Cancelled: {$reason}" : 'Cancelled',
        ]);
    }

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        self::creating(function ($fine): void {
            if (empty($fine->status)) {
                $fine->status = self::STATUS_PENDING;
            }

            if (empty($fine->due_date)) {
                $fine->due_date = now()->addDays(config('library.settings.fine_payment_days', 30));
            }
        });

        self::updated(function ($fine): void {
            // Update user's fine status when fine is paid/waived
            if ($fine->wasChanged(['status', 'paid_amount', 'waived_at'])) {
                $fine->user->refresh();
            }
        });
    }
}
