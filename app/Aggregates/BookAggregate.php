<?php

namespace App\Aggregates;

namespace App\Aggregates;

use App\Events\BookCreated;
use App\Events\BookDeletedPermanently;
use App\Events\BookUpdated;
use App\Models\Book;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class BookAggregate extends AggregateRoot
{
    public function createBook(book $data): static
    {
        $this->recordThat(new BookCreated($data));

        return $this;
    }

    public function updateBook(book $data): static
    {
        $this->recordThat(new BookUpdated($data));

        return $this;
    }

    public function deleteBook(book $book): static
    {
        $this->recordThat(new BookDeletedPermanently($book));

        return $this;
    }
}
