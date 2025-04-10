<?php

namespace App\States;

use App\Enum\BookStatus;

abstract class BookStatusState
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Book $book) {}

    abstract public function reserve(): void;
    abstract public function borrow(): void;
    abstract public function return(): void;
    abstract public function cancelReservation(): void;

    protected function transitionTo(BookStatus $status): void
    {
        $this->book->status = $status;
        $this->book->save();
    }
}
