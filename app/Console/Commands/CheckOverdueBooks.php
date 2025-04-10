<?php
namespace App\Console\Commands;

use App\Models\Borrow;
use App\Notifications\OverdueBookNotification;
use Illuminate\Console\Command;

class CheckOverdueBooks extends Command
{
    protected $signature = 'books:check-overdue';
    protected $description = 'Check for overdue books and send notifications';

    public function handle(): void
    {
        $overdueBorrowings = Borrow::overdue()
            ->whereDoesntHave('notifications', function($query) {
                $query->where('type', OverdueBookNotification::class);
            })
            ->with(['user', 'book'])
            ->get();

        foreach ($overdueBorrowings as $borrowing) {
            $borrowing->user->notify(new OverdueBookNotification($borrowing));
        }

        $this->info('Sent notifications for '.$overdueBorrowings->count().' overdue books.');
    }
}
