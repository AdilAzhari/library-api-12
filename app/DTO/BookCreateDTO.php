<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
class BookCreateDTO
{
    public string $title;
    public string $author;
    public int $publication_year;
    public ?string $description;
    public ?int $genre_id;
    public ?UploadedFile $cover_image;

    public function __construct(
        string $title,
        string $author,
        int $publication_year,
        ?string $description = null,
        ?int $genre_id = null,
        ?UploadedFile $cover_image = null
    ) {
        $this->title = $title;
        $this->author = $author;
        $this->publication_year = $publication_year;
        $this->description = $description;
        $this->genre_id = $genre_id;
        $this->cover_image = $cover_image;
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
}
