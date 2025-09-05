<?php

declare(strict_types=1);

namespace App\DTO;

final class ReviewCreateDTO extends BaseDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $book_id,
        public readonly int $rating,
        public readonly ?string $comment = null,
        public readonly bool $is_approved = false,
        public readonly bool $is_featured = false,
    ) {}

    public function toModel(): array
    {
        return [
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => $this->is_approved,
            'is_featured' => $this->is_featured,
            'approved_at' => $this->is_approved ? now() : null,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->rating < 1 || $this->rating > 5) {
            $errors['rating'] = 'Rating must be between 1 and 5';
        }

        if ($this->comment && mb_strlen($this->comment) > 1000) {
            $errors['comment'] = 'Comment must not exceed 1000 characters';
        }

        return $errors;
    }
}
