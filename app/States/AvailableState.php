<?php declare(strict_types=1);

namespace App\States;

use App\Enum\BookStatus;
use App\Exceptions\BookNotAvailableException;

class AvailableState extends BookStatusState
{
    public function reserve(): void
    {
        $this->transitionTo(BookStatus::STATUS_RESERVED);
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
        throw new BookNotAvailableException('Available book cannot be returned');
    }

    /**
     * @throws BookNotAvailableException
     */
    public function cancelReservation(): void
    {
        throw new BookNotAvailableException('Available book has no reservation to cancel');
    }
}
