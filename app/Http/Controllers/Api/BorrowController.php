<?php

namespace App\Http\Controllers\Api;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Http\Controllers\Controller;
use App\Services\BorrowingService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BorrowController extends Controller
{
    use ApiMessages;

    public function __construct(protected BorrowingService $borrowingService)
    {
    }

    public function borrowBook(Request $request)
    {
        try {
            $dto = new BorrowBookDTO(
                $request->input('book_id'),
                $request->input('user_id'),
            );

            $borrowing = $this->borrowingService->borrowBook($dto);

            return $this->successResponse('Book borrowed successfully', $borrowing, 201);
        } catch (Exception $e) {
            Log::error('Error borrowing book: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred while borrowing the book.');
        }
    }

    public function returnBook(Request $request)
    {
        try {
            $dto = new ReturnBookDTO(
                bookId: $request->input('book_id'),
                userId: $request->input('user_id'),
            );

            $borrowing = $this->borrowingService->returnBook($dto);

            return $this->successResponse('Book returned successfully', $borrowing);
        } catch (Exception $e) {
            Log::error('Error returning book: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred while returning the book.');
        }
    }

}
