<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingList;
use App\Services\ReadingListService;
use App\Traits\ApiMessages;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class ReadingListController extends Controller
{
    use ApiMessages;

    public function __construct(
        protected ReadingListService $readingListService
    ) {}

    /**
     * Get user's reading lists
     */
    public function userLists(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $includePublic = $request->boolean('include_public', false);

            $query = $user->readingLists();

            if ($includePublic) {
                $query->orWhere('is_public', true)
                    ->where('user_id', '!=', $user->id);
            }

            $readingLists = $query->withCount('books')
                ->orderBy('name')
                ->get();

            return $this->successResponse('User reading lists retrieved successfully', $readingLists);

        } catch (Exception $e) {
            Log::error('Failed to get user reading lists: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get user reading lists');
        }
    }

    /**
     * Get public reading lists
     */
    public function publicLists(Request $request): JsonResponse
    {
        try {
            $limit = min($request->integer('limit', 20), 100);

            $publicLists = ReadingList::query()->where('is_public', true)
                ->with(['user:id,name'])
                ->withCount('books')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return $this->successResponse('Public reading lists retrieved successfully', $publicLists);

        } catch (Exception $e) {
            Log::error('Failed to get public reading lists: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get public reading lists');
        }
    }

    /**
     * Get featured reading lists (curated by library staff)
     */
    public function featuredLists(Request $request): JsonResponse
    {
        try {
            $limit = min($request->integer('limit', 10), 50);

            // Get featured lists based on popularity and curation
            $featuredLists = ReadingList::query()->where('is_public', true)
                ->where('is_featured', true) // Assuming this column exists
                ->orWhere(function ($query): void {
                    // Or lists with high engagement if no explicit featuring
                    $query->where('is_public', true)
                        ->whereHas('books', function ($bookQuery): void {
                            $bookQuery->havingRaw('count(*) >= ?', [5]);
                        });
                })
                ->with(['user:id,name'])
                ->withCount('books')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();

            return $this->successResponse('Featured reading lists retrieved successfully', $featuredLists);

        } catch (Exception $e) {
            Log::error('Failed to get featured reading lists: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to get featured reading lists');
        }
    }

    /**
     * Add a book to a reading list
     */
    public function addBook(Request $request, ReadingList $readingList, Book $book): JsonResponse
    {
        try {
            // Check ownership (middleware should handle this)
            if ($readingList->user_id !== Auth::id()) {
                return $this->errorResponse('Unauthorized', 403);
            }

            // Check if book is already in the list
            if ($readingList->books()->where('book_id', $book->id)->exists()) {
                return $this->errorResponse('Book is already in this reading list', 400);
            }

            $result = $this->readingListService->addBookToList($readingList, $book);

            return $this->successResponse('Book added to reading list successfully', [
                'reading_list' => $readingList->load('books'),
                'added_book' => $book,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to add book to reading list: '.$e->getMessage());

            return $this->serverErrorResponse('Unable to add book to reading list');
        }
    }
}
