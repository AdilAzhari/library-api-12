<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

readonly class BookCreateDTO
{
    public function __construct(
        public string        $title,
        public string        $author,
        public int           $publication_year,
        public ?string       $description = null,
        public int           $genre_id,
        public ?UploadedFile $cover_image = null,
        public ?string       $isbn = null,
        public string        $status = 'available',
    )
    {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: $request->input('title'),
            author: $request->input('author'),
            publication_year: $request->input('publication_year'),
            description: $request->input('description'),
            genre_id: $request->input('genre_id'),
            cover_image: $request->file('cover_image'),
            isbn: $request->input('isbn'),
            status: $request->input('status', 'available')
        );
    }

    public static function validate(Request $request): array
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
            'genre_id' => 'required|integer|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'isbn' => 'nullable|string|max:17|unique:books,isbn',
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
            'isbn.max' => 'The ISBN may not be longer than 17 characters.',
            'isbn.unique' => 'This ISBN already exists in our system.',
            'status.in' => 'The selected status is invalid.'
        ])->validate();
    }

    public function generateUniqueIdentifier(): string
    {
        return Str::slug($this->title . '-' . $this->author . '-' . $this->publication_year);
    }

    public function sanitize(): self
    {
        return new self(
            title: trim($this->title),
            author: trim($this->author),
            publication_year: $this->publication_year,
            description: $this->description ? trim($this->description) : null,
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

    public function getCoverImagePath(?string $basePath = null): ?string
    {
        if (!$this->hasCoverImage()) {
            return null;
        }

        $filename = $this->generateUniqueIdentifier() . '.' .
            $this->cover_image->getClientOriginalExtension();

        return $basePath
            ? rtrim($basePath, '/') . '/' . $filename
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
            'isbn' => $this->isbn,
            'status' => $this->status,
        ];
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
}
