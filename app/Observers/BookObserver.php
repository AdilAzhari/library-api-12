<?php

namespace App\Observers;

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
}
