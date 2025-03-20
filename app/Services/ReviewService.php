<?php

namespace App\Services;

use App\DTO\ReviewBookDTO;
use App\Models\Review;
use App\Models\Book;

class ReviewService
{
    public function reviewBook(ReviewBookDTO $dto)
    {
        $review = Review::query()->create([
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
            'rating' => $dto->rating,
            'comment' => $dto->comment,
        ]);

        $this->updateBookRating($dto->bookId);

        return $review;
    }

    protected function updateBookRating(int $bookId): void
    {
        $book = Book::query()->findOrFail($bookId);
        $averageRating = Review::query()->where('book_id', $bookId)->avg('rating');
        $book->update(['average_rating' => $averageRating]);
    }
}
