<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ReadingListCreateDTO;
use App\Models\ReadingList;
use App\Models\User;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

final class ReadingListService
{
    public function create(ReadingListCreateDTO $dto): ReadingList
    {
        $readingList = ReadingList::query()->create($dto->toModel());

        // Add books if provided
        if (! empty($dto->book_ids)) {
            foreach ($dto->book_ids as $bookId) {
                $readingList->addBook($bookId);
            }
        }

        return $readingList->fresh();
    }

    public function createDefault(int $userId): ReadingList
    {
        // Check if user already has a default list
        if (ReadingList::getUserDefaultList($userId)) {
            throw new InvalidArgumentException('User already has a default reading list');
        }

        return ReadingList::createDefaultForUser($userId);
    }

    public function update(ReadingList $readingList, array $data): ReadingList
    {
        // Handle default list switching
        if (isset($data['is_default']) && $data['is_default']) {
            ReadingList::query()->where('user_id', $readingList->user_id)
                ->where('id', '!=', $readingList->id)
                ->update(['is_default' => false]);
        }

        $readingList->update($data);

        return $readingList->fresh();
    }

    public function delete(ReadingList $readingList): bool
    {
        // Cannot delete default list if it's the only list
        if ($readingList->is_default) {
            $userListsCount = ReadingList::forUser($readingList->user_id)->count();
            if ($userListsCount === 1) {
                throw new InvalidArgumentException('Cannot delete the only reading list');
            }

            // Set another list as default
            $newDefault = ReadingList::forUser($readingList->user_id)
                ->where('id', '!=', $readingList->id)
                ->first();

            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }

        return $readingList->delete();
    }

    public function addBook(ReadingList $readingList, int $bookId, array $options = []): bool
    {
        return $readingList->addBook($bookId, $options);
    }

    public function removeBook(ReadingList $readingList, int $bookId): bool
    {
        return $readingList->removeBook($bookId);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ReadingList::query()->with(['user', 'books']);

        if (isset($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (isset($filters['public_only'])) {
            $query->public();
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getPublicLists(int $perPage = 15): LengthAwarePaginator
    {
        return ReadingList::public()
            ->with(['user', 'books'])
            ->withCount('books')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getFeaturedLists(int $perPage = 15): LengthAwarePaginator
    {
        return ReadingList::public()
            ->where('is_featured', true)
            ->with(['user', 'books'])
            ->withCount('books')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUserLists(User $user): Collection
    {
        return ReadingList::forUser($user->id)
            ->with('books')
            ->withCount('books')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function getOrCreateDefault(User $user): ReadingList
    {
        return ReadingList::getOrCreateDefaultForUser($user->id);
    }

    public function duplicate(ReadingList $readingList, string $newName, bool $copyBooks = true): ReadingList
    {
        $newList = ReadingList::query()->create([
            'user_id' => $readingList->user_id,
            'name' => $newName,
            'description' => $readingList->description,
            'is_public' => false, // Always create as private
            'is_default' => false,
            'color' => $readingList->color,
            'icon' => $readingList->icon,
        ]);

        if ($copyBooks) {
            foreach ($readingList->books as $book) {
                $newList->addBook($book->id, [
                    'notes' => $book->pivot->notes,
                    'priority' => $book->pivot->priority,
                ]);
            }
        }

        return $newList;
    }

    public function makePublic(ReadingList $readingList): ReadingList
    {
        $readingList->makePublic();

        return $readingList->fresh();
    }

    public function makePrivate(ReadingList $readingList): ReadingList
    {
        $readingList->makePrivate();

        return $readingList->fresh();
    }

    public function updateBookPriority(ReadingList $readingList, int $bookId, int $priority): bool
    {
        if (! $readingList->hasBook($bookId)) {
            return false;
        }

        $readingList->books()->updateExistingPivot($bookId, ['priority' => $priority]);

        return true;
    }

    public function updateBookNotes(ReadingList $readingList, int $bookId, string $notes): bool
    {
        if (! $readingList->hasBook($bookId)) {
            return false;
        }

        $readingList->books()->updateExistingPivot($bookId, ['notes' => $notes]);

        return true;
    }

    public function getPopularBooks(int $limit = 10): array
    {
        return ReadingList::getPopularBooks($limit);
    }

    public function getStats(): array
    {
        return [
            'total_lists' => ReadingList::count(),
            'public_lists' => ReadingList::public()->count(),
            'private_lists' => ReadingList::private()->count(),
            'lists_with_books' => ReadingList::query()->has('books')->count(),
            'average_books_per_list' => DB::table('reading_list_books')->count() / max(ReadingList::count(), 1),
            'most_popular_books' => $this->getPopularBooks(5),
            'recent_lists' => ReadingList::query()->where('created_at', '>=', now()->subWeek())->count(),
        ];
    }

    public function searchBooks(ReadingList $readingList, string $query): Collection
    {
        return $readingList->books()
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('author', 'like', "%{$query}%")
                    ->orWhere('isbn', 'like', "%{$query}%");
            })
            ->get();
    }

    public function exportList(ReadingList $readingList, string $format = 'json'): array
    {
        $data = [
            'list' => [
                'name' => $readingList->name,
                'description' => $readingList->description,
                'created_at' => $readingList->created_at->toISOString(),
                'books_count' => $readingList->books_count,
            ],
            'books' => $readingList->books->map(fn ($book) => [
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'added_at' => $book->pivot->added_at,
                'notes' => $book->pivot->notes,
                'priority' => $book->pivot->priority,
            ]),
        ];

        return $data;
    }
}
