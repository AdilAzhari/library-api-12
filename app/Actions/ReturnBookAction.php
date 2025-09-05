<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enum\BookStatus;
use App\Enum\BorrowStatus;
use App\Models\Borrow;
use App\Services\FineService;
use App\Services\ReservationService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class ReturnBookAction
{
    public function __construct(
        private FineService $fineService,
        private ReservationService $reservationService,
    ) {}

    public function execute(Borrow $borrow, array $options = []): Borrow
    {
        return DB::transaction(function () use ($borrow, $options) {
            // Validate the return
            $this->validate($borrow);

            // Mark as returned
            $borrow->update([
                'returned_at' => now(),
                'status' => BorrowStatus::COMPLETED->value,
                'return_notes' => $options['notes'] ?? null,
            ]);

            // Update book status
            $book = $borrow->book;

            // Check if book has active reservations
            if ($book->activeReservation) {
                $book->update(['status' => BookStatus::RESERVED->value]);
                $this->notifyNextReserver($book);
            } else {
                $book->update(['status' => BookStatus::AVAILABLE->value]);
            }

            // Process any overdue fines
            $this->processOverdueFines($borrow);

            // Process late return fees if applicable
            if (isset($options['condition']) && $options['condition'] === 'damaged') {
                $this->processDamageFine($borrow, $options['damage_assessment'] ?? []);
            }

            // Send return confirmation
            $this->sendReturnConfirmation($borrow);

            return $borrow->fresh();
        });
    }

    private function validate(Borrow $borrow): void
    {
        if (! $borrow->canReturn()) {
            throw new InvalidArgumentException('This borrow cannot be returned');
        }

        if ($borrow->returned_at) {
            throw new InvalidArgumentException('Book has already been returned');
        }
    }

    private function processOverdueFines(Borrow $borrow): void
    {
        if ($borrow->due_date->isPast()) {
            $overdueDays = now()->diffInDays($borrow->due_date);

            // Check if fine already exists for this borrow
            if (! $borrow->fines()->where('reason', 'overdue')->exists()) {
                $dto = \App\DTO\FineCreateDTO::forOverdueBorrow(
                    $borrow->user_id,
                    $borrow->book_id,
                    $borrow->id,
                    $overdueDays
                );

                $this->fineService->create($dto);
            }
        }
    }

    private function processDamageFine(Borrow $borrow, array $damageAssessment): void
    {
        if (empty($damageAssessment['repair_cost'])) {
            return;
        }

        $dto = new \App\DTO\FineCreateDTO(
            user_id: $borrow->user_id,
            book_id: $borrow->book_id,
            amount: (float) $damageAssessment['repair_cost'],
            reason: \App\Enum\FineReason::DAMAGED_BOOK,
            borrow_id: $borrow->id,
            description: 'Book damage repair cost: '.($damageAssessment['description'] ?? 'No description provided'),
        );

        $this->fineService->create($dto);
    }

    private function notifyNextReserver($book): void
    {
        $nextReservation = $book->activeReservation;
        if ($nextReservation) {
            // Send notification to the user who has the reservation
            // Implementation would send email/SMS notification
        }
    }

    private function sendReturnConfirmation(Borrow $borrow): void
    {
        // Implementation would send return confirmation
    }
}
