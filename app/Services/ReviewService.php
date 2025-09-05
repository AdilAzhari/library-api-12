<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ReviewBookDTO;
use App\Models\Book;
use App\Models\Review;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final class ReviewService
{
    /**
     * Submit a review for a book.
     *
     * @throws Exception
     */
    public function reviewBook(ReviewBookDTO $dto): Review
    {
        try {
            // Ensure the book exists
            $book = Book::findOrFail($dto->bookId);

            // Create the review
            $review = Review::create([
                'book_id' => $dto->bookId,
                'user_id' => auth()->user()->id,
                'rating' => $dto->rating,
                'comment' => $dto->comment,
            ]);

            // Update the book's average rating
            $this->updateBookRating($dto->bookId);

            return $review;
        } catch (Exception $e) {
            Log::error('Error submitting review: '.$e->getMessage());
            throw new Exception('Failed to submit review: '.$e->getMessage()); // Include the actual error message
        }
    }

    /**
     * List all reviews for a specific book.
     */
    public function listReviews(?int $bookId = null): Collection
    {
        $query = Review::query()->with(['book', 'user']);

        if ($bookId) {
            $query->where('book_id', $bookId);
        }

        return $query->get();
    }

    /**
     * Get a specific review by ID.
     *
     * @throws Exception
     */
    public function getReview(int $reviewId): Review
    {
        try {
            return Review::with(['book', 'user'])->findOrFail($reviewId);
        } catch (Exception $e) {
            Log::error('Error fetching review: '.$e->getMessage());
            throw new Exception('Failed to fetch review. Please try again.');
        }
    }

    /**
     * Delete a review.
     *
     * @throws Exception
     */
    public function deleteReview(int $reviewId): void
    {
        try {
            $review = Review::query()->findOrFail($reviewId);
            $bookId = $review->book_id;
            $review->delete();

            // Update the book's average rating after deletion
            $this->updateBookRating($bookId);
        } catch (Exception $e) {
            Log::error('Error deleting review: '.$e->getMessage());
            throw new Exception('Failed to delete review. Please try again.');
        }
    }

    /**
     * Update the average rating of a book.
     */
    private function updateBookRating(int $bookId): void
    {
        $book = Book::query()->findOrFail($bookId);
        $averageRating = Review::query()->where('book_id', $bookId)->avg('rating');
        $book->update(['average_rating' => $averageRating]);
    }
}
