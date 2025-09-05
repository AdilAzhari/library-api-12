<?php

declare(strict_types=1);

namespace App\States;

use App\Enum\BookStatus;
use App\Exceptions\BookNotAvailableException;

final class ReservedState extends BookStatusState
{
    /**
     * @throws BookNotAvailableException
     */
    public function reserve(): void
    {
        throw new BookNotAvailableException('Book is already reserved');
    }

    public function borrow(): void
    {
        $this->transitionTo(BookStatus::STATUS_BORROWED);
    }

    /**
     * @throws BookNotAvailableException
     */
    public function return(): void
    {
        throw new BookNotAvailableException('Reserved book cannot be returned');
    }

    public function cancelReservation(): void
    {
        $this->transitionTo(BookStatus::STATUS_AVAILABLE);
    }
}
