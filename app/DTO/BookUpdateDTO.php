<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final readonly class BookUpdateDTO
{
    public function __construct(
        public string $title,
        public string $author,
        public int $publication_year,
        public ?string $description,
        public int $genre_id,
        public ?UploadedFile $cover_image = null,
        public ?string $isbn = null,
        public string $status = 'available',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            author: $request->input('author'),
            publication_year: $request->input('publication_year'),
            description: $request->input('description'),
            genre_id: $request->integer('genre_id'),
            cover_image: $request->file('cover_image'),
            isbn: $request->input('ISBN'),
            status: $request->input('status', 'available')
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            author: $data['author'],
            publication_year: $data['publication_year'],
            description: $data['description'] ?? null,
            genre_id: $data['genre_id'],
            cover_image: $data['cover_image'] ?? null,
            isbn: $data['isbn'] ?? null,
            status: $data['status'] ?? 'available'
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'publication_year' => $this->publication_year,
            'genre_id' => $this->genre_id,
            'ISBN' => $this->isbn,
            'status' => $this->status,
        ];
    }

    public function sanitize(): self
    {
        return new self(
            title: mb_trim($this->title),
            author: mb_trim($this->author),
            publication_year: $this->publication_year,
            description: $this->description ? mb_trim($this->description) : null,
            genre_id: $this->genre_id,
            cover_image: $this->cover_image,
            isbn: $this->isbn ? preg_replace('/[^0-9X]/', '', $this->isbn) : null,
            status: $this->status
        );
    }

    public function hasCoverImage(): bool
    {
        return $this->cover_image !== null;
    }

    public function generateUniqueIdentifier(): string
    {
        return Str::slug($this->title.'-'.$this->author.'-'.$this->publication_year);
    }

    public function getCoverImagePath(?string $basePath = null): ?string
    {
        if (! $this->hasCoverImage()) {
            return null;
        }

        $filename = $this->generateUniqueIdentifier().'.'.
            $this->cover_image->getClientOriginalExtension();

        return $basePath
            ? mb_rtrim($basePath, '/').'/'.$filename
            : $filename;
    }
}
