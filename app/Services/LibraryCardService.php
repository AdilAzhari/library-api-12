<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\LibraryCardStatus;
use App\Models\LibraryCard;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final class LibraryCardService
{
    public function issue(int $userId): LibraryCard
    {
        if (! LibraryCard::canUserHaveNewCard($userId)) {
            throw new InvalidArgumentException('User already has an active library card');
        }

        $card = LibraryCard::create([
            'user_id' => $userId,
        ]);

        // Send welcome notification
        $this->sendWelcomeNotification($card);

        return $card;
    }

    public function renew(LibraryCard $card, int $years = 2): LibraryCard
    {
        if (! $card->canRenew()) {
            throw new InvalidArgumentException('Card cannot be renewed in its current status');
        }

        $card->renew($years);

        // Send renewal confirmation
        $this->sendRenewalNotification($card);

        return $card->fresh();
    }

    public function suspend(LibraryCard $card, ?string $reason = null): LibraryCard
    {
        $card->suspend($reason);

        // Send suspension notification
        $this->sendSuspensionNotification($card, $reason);

        return $card->fresh();
    }

    public function activate(LibraryCard $card): LibraryCard
    {
        $card->activate();

        // Send activation notification
        $this->sendActivationNotification($card);

        return $card->fresh();
    }

    public function reportLost(LibraryCard $card): LibraryCard
    {
        $card->reportLost();

        // Send lost card notification
        $this->sendLostCardNotification($card);

        return $card->fresh();
    }

    public function replace(LibraryCard $card): LibraryCard
    {
        $newCard = $card->replaceCard();

        // Send replacement notification
        $this->sendReplacementNotification($newCard, $card);

        return $newCard;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LibraryCard::query()->with('user');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['expiring_soon'])) {
            $query->expiringSoon($filters['expiring_soon']);
        }

        if (isset($filters['user_search'])) {
            $search = $filters['user_search'];
            $query->whereHas('user', function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['card_number'])) {
            $query->where('card_number', 'like', "%{$filters['card_number']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findByCardNumber(string $cardNumber): ?LibraryCard
    {
        return LibraryCard::findByCardNumber($cardNumber);
    }

    public function getExpiringSoon(int $days = 30): Collection
    {
        return LibraryCard::expiringSoon($days)
            ->with('user')
            ->get();
    }

    public function sendExpiryReminders(int $days = 30): int
    {
        $expiring = $this->getExpiringSoon($days);
        $sent = 0;

        foreach ($expiring as $card) {
            $this->sendExpiryReminder($card);
            $sent++;
        }

        return $sent;
    }

    public function bulkRenew(array $cardIds, int $years = 2): int
    {
        $cards = LibraryCard::whereIn('id', $cardIds)
            ->where('status', LibraryCardStatus::ACTIVE->value)
            ->get();

        $renewed = 0;
        foreach ($cards as $card) {
            try {
                $this->renew($card, $years);
                $renewed++;
            } catch (Exception $e) {
                // Log error but continue with other cards
                logger()->error("Failed to renew card {$card->id}: ".$e->getMessage());
            }
        }

        return $renewed;
    }

    public function getStats(): array
    {
        return LibraryCard::getStatistics();
    }

    public function validateCard(string $cardNumber): array
    {
        $card = $this->findByCardNumber($cardNumber);

        if (! $card) {
            return [
                'valid' => false,
                'message' => 'Card not found',
                'card' => null,
            ];
        }

        if (! $card->is_active) {
            return [
                'valid' => false,
                'message' => 'Card is not active: '.$card->status,
                'card' => $card,
            ];
        }

        if ($card->is_expired) {
            return [
                'valid' => false,
                'message' => 'Card has expired',
                'card' => $card,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Card is valid',
            'card' => $card,
        ];
    }

    public function generateReport(array $filters = []): array
    {
        $query = LibraryCard::query();

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $cards = $query->get();

        return [
            'total_cards' => $cards->count(),
            'active_cards' => $cards->where('status', LibraryCardStatus::ACTIVE->value)->count(),
            'expired_cards' => $cards->where('status', LibraryCardStatus::EXPIRED->value)->count(),
            'suspended_cards' => $cards->where('status', LibraryCardStatus::SUSPENDED->value)->count(),
            'lost_cards' => $cards->where('status', LibraryCardStatus::LOST->value)->count(),
            'cancelled_cards' => $cards->where('status', LibraryCardStatus::CANCELLED->value)->count(),
            'expiring_soon' => LibraryCard::expiringSoon()->count(),
            'issued_this_month' => $cards->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    private function sendWelcomeNotification(LibraryCard $card): void
    {
        // Implementation would send welcome notification with card details
    }

    private function sendRenewalNotification(LibraryCard $card): void
    {
        // Implementation would send renewal confirmation
    }

    private function sendSuspensionNotification(LibraryCard $card, ?string $reason): void
    {
        // Implementation would send suspension notification
    }

    private function sendActivationNotification(LibraryCard $card): void
    {
        // Implementation would send activation notification
    }

    private function sendLostCardNotification(LibraryCard $card): void
    {
        // Implementation would send lost card notification
    }

    private function sendReplacementNotification(LibraryCard $newCard, LibraryCard $oldCard): void
    {
        // Implementation would send replacement card notification
    }

    private function sendExpiryReminder(LibraryCard $card): void
    {
        // Implementation would send expiry reminder
    }
}
