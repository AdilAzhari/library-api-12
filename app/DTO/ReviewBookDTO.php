<?php

namespace App\DTO;

class ReviewBookDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int     $bookId,
        public int     $userId,
        public int     $rating,
        public ?string $comment = null,
    )
    {
    }
}
