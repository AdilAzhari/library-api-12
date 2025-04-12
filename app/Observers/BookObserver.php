<?php

namespace App\Observers;

use App\Enum\BookStatus;
use App\Events\BookCreated;
use App\Events\BookDeletedPermanently;
use App\Events\BookUpdated;
use App\Models\Book;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        BookCreated::dispatch($book);
    }

    public function creating(Book $book): void
    {
        $book->status = $book->status ?? BookStatus::STATUS_AVAILABLE->value;

        // Ensure new books can't be created as borrowed without a borrow record
        if ($book->status === BookStatus::STATUS_BORROWED->value && !$book->borrowings()->exists()) {
            $book->status = BookStatus::STATUS_AVAILABLE->value;
        }
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        BookUpdated::dispatch($book);
    }


    /**
     * Handle the Book "force deleted" event.
     */
    public function forceDeleted(Book $book): void
    {
        BookDeletedPermanently::dispatch($book);
    }

    public function saved(Book $book): void
    {
        $book->searchable();
    }

    public function deleted(Book $book): void
    {
        $book->unsearchable();
    }
}
