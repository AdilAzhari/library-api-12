<?php

namespace App\States;

use App\Enum\BookStatus;
use App\Exceptions\BookNotAvailableException;

class BorrowedState extends BookStatusState
{
    /**
     * @throws BookNotAvailableException
     */
    public function reserve(): void
    {
        throw new BookNotAvailableException('Borrowed book cannot be reserved');
    }

    /**
     * @throws BookNotAvailableException
     */
    public function borrow(): void
    {
        throw new BookNotAvailableException('Book is already borrowed');
    }

    public function return(): void
    {
        $this->transitionTo(BookStatus::STATUS_AVAILABLE);
    }

    /**
     * @throws BookNotAvailableException
     */
    public function cancelReservation(): void
    {
        throw new BookNotAvailableException('Borrowed book has no reservation to cancel');
    }
}
