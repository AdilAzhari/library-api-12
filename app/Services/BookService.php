<?php

namespace App\Services;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BookService
{
    public function getAllBooks($filters, $perPage): LengthAwarePaginator
    {
        $query = Book::query();

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('title', 'like', "%$search%")
                ->orWhere('author', 'like', "%$search%");
        })->when($filters['year'] ?? null, function ($q, $year) {
            $q->where('publication_year', $year);
        })->when($filters['sort_by'] ?? null, function ($q, $sortBy) use ($filters) {
            $sortField = in_array($sortBy, ['title', 'author', 'publication_year']) ? $sortBy : 'title';
            $q->orderBy($sortField, $filters['sort_direction'] ?? 'asc');
        });

        return $query->paginate($perPage);
    }

    public function createBook(BookCreateDTO $dto): Book
    {
        $book = Book::query()->create([
            'title' => $dto->title,
            'author' => $dto->author,
            'publication_year' => $dto->publication_year,
            'description' => $dto->description,
            'genre_id' => $dto->genre_id,
            'cover_image' => $dto->cover_image ? basename($dto->cover_image->store('public/covers')) : null,
        ]);

        Log::info('New book created: ' . $book->id);

        return $book;
    }

    public function updateBook(Book $book, BookUpdateDTO $dto): Book
    {
        $book->update([
            'title' => $dto->title,
            'author' => $dto->author,
            'publication_year' => $dto->publication_year,
            'description' => $dto->description,
            'genre_id' => $dto->genre_id,
            'cover_image' => $dto->cover_image ? basename($dto->cover_image->store('public/covers')) : $book->cover_image,
        ]);

        Log::info('Book ' . $book->id . ' updated.');

        return $book;
    }

    public function deleteBook(Book $book): void
    {
        $book->delete();
        Log::info('Book moved to trash.');
    }

    public function restoreBook($id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();
        return $book;
    }

    public function recommendBooks(Book $book): Collection
    {
        return Book::query()->where('genre_id', $book->genre_id)
            ->where('id', '!=', $book->id)
            ->limit(5)
            ->get();
    }
}
