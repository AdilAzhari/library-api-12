<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Borrow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OverdueBookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Borrow $borrowing) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Overdue Book Notification')
            ->line('The book "'.$this->borrowing->book->title.'" is overdue.')
            ->line('Due date: '.$this->borrowing->due_date->format('F j, Y'))
            ->action('Return Book', url('/borrowings'))
            ->line('Thank you for using our library!');
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'The book "'.$this->borrowing->book->title.'" is overdue.',
            'due_date' => $this->borrowing->due_date->format('F j, Y'),
            'book_id' => $this->borrowing->book_id,
            'borrowing_id' => $this->borrowing->id,
        ];
    }
}
