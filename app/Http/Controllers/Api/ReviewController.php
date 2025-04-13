<?php

namespace App\Http\Controllers\Api;

use App\DTO\ReviewBookDTO;
use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    use ApiMessages;

    public function __construct(protected ReviewService $reviewService) {}

    public function reviewBook(Request $request)
    {
        try {
            $dto = new ReviewBookDTO(
                bookId: $request->input('book_id'),
                userId: $request->input('user_id'),
                rating: $request->input('rating'),
                comment: $request->input('comment') ?? null,
            );

            $review = $this->reviewService->reviewBook($dto);

            return $this->successResponse('Review submitted successfully', $review, 201);
        } catch (Exception $e) {
            Log::error('Error submitting review: '.$e->getMessage());

            return $this->serverErrorResponse('An error occurred while submitting the review.');
        }
    }
}
