<?php

declare(strict_types=1);

namespace App\DTO;

final class ReadingListCreateDTO extends BaseDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly bool $is_public = false,
        public readonly bool $is_default = false,
        public readonly string $color = '#3B82F6',
        public readonly string $icon = 'book-open',
        public readonly array $book_ids = [],
    ) {}

    public function toModel(): array
    {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'is_default' => $this->is_default,
            'color' => $this->color,
            'icon' => $this->icon,
        ];
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->name) || mb_strlen($this->name) > 100) {
            $errors['name'] = 'Name is required and must not exceed 100 characters';
        }

        if ($this->description && mb_strlen($this->description) > 1000) {
            $errors['description'] = 'Description must not exceed 1000 characters';
        }

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $this->color)) {
            $errors['color'] = 'Color must be a valid hex color code';
        }

        $allowedIcons = [
            'book-open', 'heart', 'star', 'bookmark', 'library',
            'academic-cap', 'sparkles', 'fire', 'lightning-bolt',
        ];

        if (! in_array($this->icon, $allowedIcons)) {
            $errors['icon'] = 'Invalid icon selected';
        }

        return $errors;
    }
}
