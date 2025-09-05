<?php

declare(strict_types=1);

namespace App\Enum;

enum FineReason: string
{
    case OVERDUE = 'overdue';
    case LOST_BOOK = 'lost_book';
    case DAMAGED_BOOK = 'damaged_book';
    case LATE_RETURN = 'late_return';
    case PROCESSING_FEE = 'processing_fee';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::OVERDUE => 'Overdue Book',
            self::LOST_BOOK => 'Lost Book',
            self::DAMAGED_BOOK => 'Damaged Book',
            self::LATE_RETURN => 'Late Return',
            self::PROCESSING_FEE => 'Processing Fee',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OVERDUE => 'Fine for keeping a book past its due date',
            self::LOST_BOOK => 'Replacement cost for a lost book',
            self::DAMAGED_BOOK => 'Repair or replacement cost for damaged book',
            self::LATE_RETURN => 'Fee for returning book after library hours',
            self::PROCESSING_FEE => 'Administrative processing fee',
            self::OTHER => 'Other library-related fine',
        };
    }

    public function defaultAmount(): float
    {
        return match ($this) {
            self::OVERDUE => 0.50, // per day
            self::LOST_BOOK => 25.00,
            self::DAMAGED_BOOK => 10.00,
            self::LATE_RETURN => 2.00,
            self::PROCESSING_FEE => 5.00,
            self::OTHER => 0.00,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::OVERDUE => 'clock',
            self::LOST_BOOK => 'exclamation-triangle',
            self::DAMAGED_BOOK => 'tools',
            self::LATE_RETURN => 'calendar-x',
            self::PROCESSING_FEE => 'file-text',
            self::OTHER => 'info-circle',
        };
    }
}
