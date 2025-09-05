<?php

declare(strict_types=1);

namespace App\Enum;

enum ReviewStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FEATURED = 'featured';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FEATURED => 'Featured',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FEATURED => 'info',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Review is awaiting moderation',
            self::APPROVED => 'Review has been approved and is visible',
            self::REJECTED => 'Review was rejected by moderation',
            self::FEATURED => 'Review is featured and highlighted',
        };
    }

    public function canApprove(): bool
    {
        return $this === self::PENDING;
    }

    public function canReject(): bool
    {
        return in_array($this, [self::PENDING, self::APPROVED]);
    }

    public function canFeature(): bool
    {
        return $this === self::APPROVED;
    }

    public function isVisible(): bool
    {
        return in_array($this, [self::APPROVED, self::FEATURED]);
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'clock',
            self::APPROVED => 'check',
            self::REJECTED => 'x',
            self::FEATURED => 'star',
        };
    }
}
