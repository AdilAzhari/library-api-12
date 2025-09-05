<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ReviewBookDTO;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Services\ReviewService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

final class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    /**
     * Show the review book form.
     */
    public function create(): Response|ResponseFactory
    {
        return Inertia::render('Review/Create');
    }

    /**
     * Handle the review book form submission.
     */
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        try {
            $dto = new ReviewBookDTO(
                bookId: $book->id,
                rating: $request->validated('rating'),
                comment: $request->validated('comment'), // Optional
            );

            $this->reviewService->reviewBook($dto);

            return redirect()
                ->back()
                ->with('success', 'Review submitted successfully.');
        } catch (Exception $e) {
            Log::error('Error submitting review: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', $e->getMessage()); // Return the actual error message
        }
    }

    /**
     * Delete a review.
     */
    public function destroy(int $reviewId): RedirectResponse
    {
        try {
            $this->reviewService->deleteReview($reviewId);

            return redirect()
                ->route('review.index')
                ->with('success', 'Review deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting review: '.$e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the review.');
        }
    }

    /**
     * Display user's reviews
     */
    public function index(): Response
    {
        $user = Auth::user();
        $reviews = $user->reviews()
            ->with('book')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_reviews' => $user->reviews()->count(),
            'average_rating' => round($user->reviews()->avg('rating'), 1),
            'helpful_votes' => 0, // Would need to implement helpful votes system
        ];

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    /**
     * Show edit review form
     */
    public function edit(int $reviewId): Response|RedirectResponse
    {
        try {
            $review = Auth::user()->reviews()
                ->with('book')
                ->findOrFail($reviewId);

            return Inertia::render('Reviews/Edit', [
                'review' => $review,
            ]);

        } catch (Exception) {
            return redirect()
                ->route('reviews.index')
                ->with('error', 'Review not found.');
        }
    }

    /**
     * Update review
     */
    public function update(StoreReviewRequest $request, int $reviewId): RedirectResponse
    {
        try {
            $review = Auth::user()->reviews()->findOrFail($reviewId);

            $review->update([
                'rating' => $request->validated('rating'),
                'comment' => $request->validated('comment'),
            ]);

            return redirect()
                ->route('reviews.index')
                ->with('success', 'Review updated successfully.');

        } catch (Exception $e) {
            Log::error('Error updating review: '.$e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update review. Please try again.');
        }
    }
}
