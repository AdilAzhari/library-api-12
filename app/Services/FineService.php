<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\FineCreateDTO;
use App\Enum\FineReason;
use App\Enum\FineStatus;
use App\Models\Borrow;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class FineService
{
    public function create(FineCreateDTO $dto): Fine
    {
        $fine = App\Models\Fine::query()->create($dto->toModel());

        // Send notification to user
        $this->sendFineNotification($fine);

        return $fine->fresh();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Fine::query()->with(['user', 'book', 'borrow']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['reason'])) {
            $query->byReason($filters['reason']);
        }

        if (isset($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (isset($filters['overdue_only'])) {
            $query->overdue();
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function markAsPaid(Fine $fine, ?float $amount = null, string $paymentMethod = 'cash', ?string $notes = null): Fine
    {
        $fine->markAsPaid($amount, $paymentMethod, $notes);

        // Send payment confirmation notification
        $this->sendPaymentConfirmation($fine);

        return $fine->fresh();
    }

    public function waive(Fine $fine, int $waivedBy, ?string $reason = null): Fine
    {
        $fine->waive($waivedBy, $reason);

        // Log waiver action
        $this->logWaiverAction($fine, $waivedBy, $reason);

        return $fine->fresh();
    }

    public function cancel(Fine $fine, ?string $reason = null): Fine
    {
        $fine->cancel($reason);

        return $fine->fresh();
    }

    public function processOverdueFines(): int
    {
        $overdueBorrows = Borrow::active()
            ->where('due_date', '<', now())
            ->whereDoesntHave('fines', function ($query): void {
                $query->where('reason', FineReason::OVERDUE->value)
                    ->where('created_at', '>', now()->subDay());
            })
            ->with(['user', 'book'])
            ->get();

        $created = 0;
        foreach ($overdueBorrows as $borrow) {
            $overdueDays = now()->diffInDays($borrow->due_date);

            $dto = FineCreateDTO::forOverdueBorrow(
                $borrow->user_id,
                $borrow->book_id,
                $borrow->id,
                $overdueDays
            );

            $this->create($dto);
            $created++;
        }

        return $created;
    }

    public function getUserFines(int $userId, string $status = 'all', int $perPage = 10): LengthAwarePaginator
    {
        $query = App\Models\Fine::query()->with(['borrow.book'])
            ->where('user_id', $userId);

        if ($status === 'outstanding') {
            $query->whereIn('status', ['pending', 'partial']);
        } elseif ($status === 'paid') {
            $query->where('status', 'paid');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getUserTotalPaidFines(int $userId): float
    {
        return Fine::query()->where('user_id', $userId)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function processPayment(Fine $fine, float $amount, string $paymentMethod, ?string $reference = null): Fine
    {
        // Process the payment and update fine status
        $totalPaid = $fine->paid_amount + $amount;

        if ($totalPaid >= $fine->amount) {
            $fine->update([
                'status' => 'paid',
                'paid_amount' => $fine->amount,
                'paid_at' => now(),
            ]);
        } else {
            $fine->update([
                'status' => 'partial',
                'paid_amount' => $totalPaid,
            ]);
        }

        return $fine->fresh();
    }

    public function getOverdueFines(): Collection
    {
        return Fine::overdue()
            ->with(['user', 'book'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function sendOverdueReminders(): int
    {
        $overdueFines = $this->getOverdueFines();
        $sent = 0;

        foreach ($overdueFines as $fine) {
            if ($this->shouldSendReminder($fine)) {
                $this->sendOverdueReminder($fine);
                $sent++;
            }
        }

        return $sent;
    }

    public function generateReport(array $filters = []): array
    {
        $query = Fine::query();

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['reason'])) {
            $query->where('reason', $filters['reason']);
        }

        $fines = $query->get();

        return [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'paid_amount' => $fines->where('status', FineStatus::PAID->value)->sum('amount'),
            'outstanding_amount' => $fines->whereIn('status', [FineStatus::PENDING->value, FineStatus::PARTIAL->value])->sum('amount'),
            'waived_amount' => $fines->where('status', FineStatus::WAIVED->value)->sum('amount'),
            'by_reason' => $fines->groupBy('reason')->map(fn ($group, $reason) => [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'average_amount' => $group->avg('amount'),
            ]),
            'by_status' => $fines->groupBy('status')->map(fn ($group) => [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
            ]),
        ];
    }

    private function sendFineNotification(Fine $fine): void
    {
        // Implementation would send notification to user
        // This could be email, SMS, or in-app notification
    }

    private function sendPaymentConfirmation(Fine $fine): void
    {
        // Implementation would send payment confirmation
    }

    private function logWaiverAction(Fine $fine, int $waivedBy, ?string $reason): void
    {
        // Implementation would log the waiver action for audit trail
    }

    private function shouldSendReminder(Fine $fine): bool
    {
        // Logic to determine if reminder should be sent
        // Could check last reminder date, fine age, etc.
        return $fine->due_date->isPast() && $fine->due_date->diffInDays(now()) % 7 === 0;
    }

    private function sendOverdueReminder(Fine $fine): void
    {
        // Implementation would send overdue reminder
    }
}
