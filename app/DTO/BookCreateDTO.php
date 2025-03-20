<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

readonly class BookCreateDTO
{
    public function __construct(
        public string        $title,
        public string        $author,
        public int           $publication_year,
        public ?string       $description = null,
        public ?int          $genre_id = null,
        public ?UploadedFile $cover_image = null
    )
    {
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
