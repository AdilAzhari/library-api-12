<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

final readonly class BookCreateDTO
{
    public function __construct(
        public string $title,
        public string $author,
        public int $publication_year,
        public ?string $description,
        public int $genre_id,
        public ?UploadedFile $cover_image = null,
        public ?string $ISBN = null,
        public ?string $cover_image_path = null,
        public float $average_rating = 0,
        public string $status = 'available',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->string('title')->toString(),
            author: $request->string('author')->toString(),
            publication_year: $request->input('publication_year'),
            description: $request->input('description'),
            genre_id: $request->integer('genre_id'),
            cover_image: $request->file('cover_image'),
            ISBN: $request->input('ISBN'),
            status: $request->input('status', 'available')
        );
    }

    public static function validate(Request $request): array
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|digits:4|integer|min:1900|max:'.date('Y'),
            'description' => 'nullable|string',
            'genre_id' => 'required|integer|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ISBN' => 'nullable|string|max:17|unique:books,ISBN',
            'status' => 'sometimes|in:available,borrowed,reserved',
        ], [
            'title.required' => 'The book title is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'author.required' => 'The author name is required.',
            'author.max' => 'The author name may not be greater than 255 characters.',
            'publication_year.required' => 'The publication year is required.',
            'publication_year.digits' => 'The publication year must be 4 digits.',
            'publication_year.min' => 'The publication year must be at least 1900.',
            'publication_year.max' => 'The publication year cannot be in the future.',
            'genre_id.required' => 'Please select a genre.',
            'genre_id.exists' => 'The selected genre is invalid.',
            'cover_image.image' => 'The cover must be an image file.',
            'cover_image.mimes' => 'The cover must be a file of type: jpeg, png, jpg, gif, or webp.',
            'cover_image.max' => 'The cover image may not be larger than 2MB.',
            'ISBN.max' => 'The ISBN may not be longer than 17 characters.',
            'ISBN.unique' => 'This ISBN already exists in our system.',
            'status.in' => 'The selected status is invalid.',
        ])->validate();
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
            ISBN: $data['ISBN'] ?? null,
            status: $data['status'] ?? 'available'
        );
    }

    public function generateUniqueIdentifier(): string
    {
        return Str::slug($this->title.'-'.$this->author.'-'.$this->publication_year);
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
            ISBN: $this->ISBN ? preg_replace('/[^0-9X]/', '', $this->ISBN) : null,
            status: $this->status
        );
    }

    public function hasCoverImage(): bool
    {
        return $this->cover_image !== null;
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

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'publication_year' => $this->publication_year,
            'genre_id' => $this->genre_id,
            'ISBN' => $this->ISBN,
            'status' => $this->status,
        ];
    }
}
