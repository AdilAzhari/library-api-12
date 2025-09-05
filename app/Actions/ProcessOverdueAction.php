<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Borrow;
use App\Models\User;
use App\Services\FineService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final readonly class ProcessOverdueAction
{
    public function __construct(
        private FineService $fineService,
        private UserService $userService,
    ) {}

    public function execute(): array
    {
        Log::info('Starting overdue processing');

        $results = [
            'fines_created' => 0,
            'notifications_sent' => 0,
            'cards_suspended' => 0,
            'errors' => [],
        ];

        try {
            // Process overdue fines
            $results['fines_created'] = $this->fineService->processOverdueFines();

            // Send overdue notifications
            $results['notifications_sent'] = $this->sendOverdueNotifications();

            // Suspend cards if necessary
            $results['cards_suspended'] = $this->suspendOverdueUsers();

            Log::info('Overdue processing completed', $results);

        } catch (Exception $e) {
            Log::error('Error during overdue processing: '.$e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    public function processIndividualUser(User $user): array
    {
        $results = [
            'fines_created' => 0,
            'notifications_sent' => 0,
            'suspended' => false,
        ];

        $overdueBorrows = $user->activeBorrows()
            ->where('due_date', '<', now())
            ->get();

        if ($overdueBorrows->isEmpty()) {
            return $results;
        }

        // Create fines for each overdue borrow
        foreach ($overdueBorrows as $borrow) {
            if (! $borrow->fines()->where('reason', 'overdue')->exists()) {
                $overdueDays = now()->diffInDays($borrow->due_date);

                $dto = \App\DTO\FineCreateDTO::forOverdueBorrow(
                    $borrow->user_id,
                    $borrow->book_id,
                    $borrow->id,
                    $overdueDays
                );

                $this->fineService->create($dto);
                $results['fines_created']++;
            }
        }

        // Send notification
        try {
            $this->sendOverdueNotification($user);
            $results['notifications_sent'] = 1;
        } catch (Exception $e) {
            Log::error("Failed to send notification to user {$user->id}: ".$e->getMessage());
        }

        // Check if user should be suspended
        $longestOverdue = $overdueBorrows->max(fn ($borrow) => now()->diffInDays($borrow->due_date));

        $suspensionThreshold = config('library.settings.overdue_suspension_days', 30);
        if ($longestOverdue >= $suspensionThreshold) {
            try {
                $this->userService->suspend(
                    $user,
                    "Books overdue for {$longestOverdue} days"
                );
                $results['suspended'] = true;
            } catch (Exception $e) {
                Log::error("Failed to suspend user {$user->id}: ".$e->getMessage());
            }
        }

        return $results;
    }

    private function sendOverdueNotifications(): int
    {
        $overdueUsers = $this->getOverdueUsers();
        $notificationsSent = 0;

        foreach ($overdueUsers as $user) {
            try {
                $this->sendOverdueNotification($user);
                $notificationsSent++;
            } catch (Exception $e) {
                Log::error("Failed to send overdue notification to user {$user->id}: ".$e->getMessage());
            }
        }

        return $notificationsSent;
    }

    private function suspendOverdueUsers(): int
    {
        $suspensionThreshold = config('library.settings.overdue_suspension_days', 30);
        $longOverdueUsers = $this->getLongOverdueUsers($suspensionThreshold);
        $suspended = 0;

        foreach ($longOverdueUsers as $user) {
            try {
                $this->userService->suspend(
                    $user,
                    "Automatic suspension - books overdue for more than {$suspensionThreshold} days"
                );
                $suspended++;
            } catch (Exception $e) {
                Log::error("Failed to suspend user {$user->id}: ".$e->getMessage());
            }
        }

        return $suspended;
    }

    private function getOverdueUsers(): Collection
    {
        return App\Models\User::query()->whereHas('activeBorrows', function ($query): void {
            $query->where('due_date', '<', now());
        })->with(['activeBorrows' => function ($query): void {
            $query->where('due_date', '<', now());
        }])->get();
    }

    private function getLongOverdueUsers(int $days): Collection
    {
        $cutoffDate = now()->subDays($days);

        return App\Models\User::query()->whereHas('activeBorrows', function ($query) use ($cutoffDate): void {
            $query->where('due_date', '<', $cutoffDate);
        })->whereDoesntHave('activeLibraryCard', function ($query): void {
            $query->where('status', 'suspended');
        })->get();
    }

    private function sendOverdueNotification(User $user): void
    {
        $overdueBorrows = $user->activeBorrows()
            ->where('due_date', '<', now())
            ->with('book')
            ->get();

        foreach ($overdueBorrows as $borrow) {
            $daysPastDue = now()->diffInDays($borrow->due_date);

            // Send different types of notifications based on how overdue
            $notificationType = $this->getNotificationType($daysPastDue);

            // Implementation would send actual notification
            // $this->sendNotification($user, $borrow, $notificationType);
        }
    }

    private function getNotificationType(int $daysPastDue): string
    {
        return match (true) {
            $daysPastDue <= 3 => 'gentle_reminder',
            $daysPastDue <= 7 => 'reminder',
            $daysPastDue <= 14 => 'urgent_reminder',
            $daysPastDue <= 30 => 'final_notice',
            default => 'collection_notice',
        };
    }
}
