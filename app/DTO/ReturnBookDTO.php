<?php

namespace App\DTO;

use Illuminate\Http\Request;

readonly class ReturnBookDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $bookId,
        public int $userId,
    )
    {
    }
    public function fromRequest(Request $request): self
    {
        return new self(
            bookId: $request->bookId,
            userId: $request->userId,
        );
    }
}
