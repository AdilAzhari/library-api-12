<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LibraryCard extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_LOST = 'lost';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'card_number',
        'issued_date',
        'expires_date',
        'status',
        'qr_code',
        'barcode',
        'notes',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expires_date' => 'date',
        'status' => \App\Enum\LibraryCardStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'is_active',
        'is_expired',
        'days_until_expiry',
        'formatted_card_number',
    ];

    /**
     * Static methods
     */
    public static function generateCardNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'BTCH'.$year; // BiblioTech Hub + Year

        // Get the next sequential number
        $lastCard = self::query()->where('card_number', 'like', $prefix.'%')
            ->orderBy('card_number', 'desc')
            ->first();

        if ($lastCard) {
            $lastNumber = (int) mb_substr((string) $lastCard->card_number, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.mb_str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function generateQRCode(string $cardNumber): string
    {
        // In a real implementation, you'd generate an actual QR code
        // For now, we'll just return the data that would be encoded
        return base64_encode(json_encode([
            'library' => 'BiblioTech Hub',
            'card_number' => $cardNumber,
            'type' => 'library_card',
            'generated' => now()->toISOString(),
        ]));
    }

    public static function generateBarcode(string $cardNumber): string
    {
        // Generate a simple barcode representation
        // In production, you'd use a proper barcode library
        return 'BC'.str_replace(['BTCH', '-'], '', $cardNumber);
    }

    public static function findByCardNumber(string $cardNumber): ?self
    {
        // Remove formatting if present
        $cleanCardNumber = str_replace('-', '', $cardNumber);

        return self::query()->where('card_number', $cleanCardNumber)
            ->orWhere('card_number', $cardNumber)
            ->first();
    }

    /**
     * Get card statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total_cards' => self::count(),
            'active_cards' => self::active()->count(),
            'expired_cards' => self::expired()->count(),
            'expiring_soon' => self::expiringSoon()->count(),
            'suspended_cards' => self::query()->where('status', self::STATUS_SUSPENDED)->count(),
            'lost_cards' => self::query()->where('status', self::STATUS_LOST)->count(),
        ];
    }

    /**
     * Check if user can have a new card
     */
    public static function canUserHaveNewCard(int $userId): bool
    {
        // User can't have more than one active card
        return ! self::query()->where('user_id', $userId)
            ->active()
            ->exists();
    }

    /**
     * Issue a new card to a user
     */
    public static function issueToUser(int $userId): self
    {
        if (! self::canUserHaveNewCard($userId)) {
            throw new Exception('User already has an active library card');
        }

        return self::create(['user_id' => $userId]);
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expires_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_date', '<=', now())
            ->orWhere('status', self::STATUS_EXPIRED);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('expires_date', [
                now(),
                now()->addDays($days),
            ]);
    }

    /**
     * Accessors
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->expires_date->gt(now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_date->lt(now()) ||
               $this->status === self::STATUS_EXPIRED;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if ($this->is_expired) {
            return 0;
        }

        return now()->diffInDays($this->expires_date, false);
    }

    public function getFormattedCardNumberAttribute(): string
    {
        // Format like: BTCH-2025-000001
        return mb_substr($this->card_number, 0, 4).'-'.
               mb_substr($this->card_number, 4, 4).'-'.
               mb_substr($this->card_number, 8);
    }

    /**
     * Methods
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function suspend(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_SUSPENDED,
            'notes' => $reason ? "Suspended: {$reason}" : 'Suspended',
        ]);
    }

    public function reportLost(): bool
    {
        return $this->update([
            'status' => self::STATUS_LOST,
            'notes' => 'Reported lost on '.now()->format('Y-m-d'),
        ]);
    }

    public function cancel(?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ? "Cancelled: {$reason}" : 'Cancelled',
        ]);
    }

    public function renew(int $years = 2): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        return $this->update([
            'expires_date' => $this->expires_date->addYears($years),
        ]);
    }

    public function replaceCard(): self
    {
        // Mark current card as lost
        $this->reportLost();

        // Create new card for same user
        return self::create([
            'user_id' => $this->user_id,
            'notes' => 'Replacement for card: '.$this->formatted_card_number,
        ]);
    }

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        self::creating(function ($card): void {
            if (empty($card->card_number)) {
                $card->card_number = self::generateCardNumber();
            }

            if (empty($card->issued_date)) {
                $card->issued_date = now();
            }

            if (empty($card->expires_date)) {
                $card->expires_date = now()->addYears(2);
            }

            if (empty($card->status)) {
                $card->status = self::STATUS_ACTIVE;
            }

            // Generate QR code and barcode
            $card->qr_code = self::generateQRCode($card->card_number);
            $card->barcode = self::generateBarcode($card->card_number);
        });
    }
}
