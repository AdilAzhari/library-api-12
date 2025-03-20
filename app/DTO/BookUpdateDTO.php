<?php

namespace App\DTO;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

readonly class BookUpdateDTO
{
    public function __construct(
       public string $title,
       public string $author,
       public int $publication_year,
       public ?string $description = null,
       public ?int $genre_id = null,
       public ?UploadedFile $cover_image = null
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->title,
            $request->author,
            $request->publication_year,
            $request->description,
            $request->genre_id,
            $request->file('cover_image')
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'publication_year' => $this->publication_year,
            'description' => $this->description,
            'genre_id' => $this->genre_id,
            'cover_image' => $this->cover_image,
        ];
    }
}
