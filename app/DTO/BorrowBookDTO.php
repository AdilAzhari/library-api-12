<?php

namespace App\DTO;

use Illuminate\Http\Request;

class BorrowBookDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int    $user_id,
        public int    $book_id,
        public string $borrowed_at,
        public string $returned_at,
    )
    {
    }
    public function fromRequest(Request $request): self
    {
        return new self(
            $request->user_id,
            $request->book_id,
            $request->borrowed_at,
            $request->returned_at,
        );
    }
}
