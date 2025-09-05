<?php

declare(strict_types=1);

namespace App\DTO;

final class ReviewBookDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $bookId,
        public int $rating,
        public ?string $comment = null,
    ) {}
}
