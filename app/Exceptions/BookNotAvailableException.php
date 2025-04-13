<?php

namespace App\Exceptions;

use Exception;

class BookNotAvailableException extends Exception
{
    protected $message = 'This book is not available for the requested action';

    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}
