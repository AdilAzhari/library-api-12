<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\ReviewBookDTO;
use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class ReviewController extends Controller
{
    use ApiMessages;

    public function __construct(protected ReviewService $reviewService) {}

    public function reviewBook(Request $request)
    {
        Log::info('ReviewController::reviewBook - Starting review submission', [
            'book_id' => $request->input('book_id'),
            'user_id' => $request->input('user_id'),
            'rating' => $request->input('rating'),
            'has_comment' => ! empty($request->input('comment')),
            'request_data' => $request->all(),
        ]);

        try {
            $dto = new ReviewBookDTO(
                bookId: $request->input('book_id'),
                userId: $request->input('user_id'),
                rating: $request->input('rating'),
                comment: $request->input('comment') ?? null,
            );

            $review = $this->reviewService->reviewBook($dto);

            Log::info('ReviewController::reviewBook - Review submitted successfully', [
                'review_id' => $review->id ?? null,
                'book_id' => $dto->bookId,
                'user_id' => $dto->userId,
                'rating' => $dto->rating,
            ]);

            return $this->successResponse('Review submitted successfully', $review, 201);
        } catch (Exception $e) {
            Log::error('ReviewController::reviewBook - Error submitting review', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'book_id' => $request->input('book_id'),
                'user_id' => $request->input('user_id'),
                'rating' => $request->input('rating'),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->serverErrorResponse('An error occurred while submitting the review.');
        }
    }
}
