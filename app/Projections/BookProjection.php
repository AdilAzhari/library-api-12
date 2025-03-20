<?php

namespace App\Projections;

use App\Events\BookCreated;
use App\Events\BookDeletedPermanently;
use App\Events\BookUpdated;
use App\Models\Book;

class BookProjection
{
    public function onBookCreated(BookCreated $event): void
    {
        Book::query()->create($event->data);
    }

    public function onBookUpdated(BookUpdated $event): void
    {
        $book = Book::query()->find($event->data['id']);
        $book->update($event->data);
    }

    public function onBookDeleted(BookDeletedPermanently $event): void
    {
        $book = Book::query()->find($event->aggregateRootUuid());
        $book->delete();
    }
}
