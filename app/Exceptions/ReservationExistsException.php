<?php

namespace App\Exceptions;

use Exception;

class ReservationExistsException extends Exception
{
    protected $message = 'You already have an active reservation for this book';

    public function __construct(?string $message = null, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}
