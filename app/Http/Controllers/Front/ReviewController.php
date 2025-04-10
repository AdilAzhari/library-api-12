<?php

namespace App\Http\Controllers\Front;

use App\DTO\ReviewBookDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Services\ReviewService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService)
    {
    }

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
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        try {
            $dto = new ReviewBookDTO(
                bookId: $request->validated('book_id'),
                rating: $request->validated('rating'),
                comment: $request->validated('comment'), // Optional
            );

            $this->reviewService->reviewBook($dto);

            return redirect()
                ->back()
                ->with('success', 'Review submitted successfully.');
        } catch (Exception $e) {
            Log::error('Error submitting review: ' . $e->getMessage());
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
            Log::error('Error deleting review: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the review.');
        }
    }
}
