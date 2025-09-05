<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;

final class RecommendationController extends Controller
{
    public function recommend(Book $book)
    {
        $relatedBooks = Book::query()->where('genre_id', $book->genre_id)
            ->where('id', '!=', $book->id)
            ->limit(5)
            ->get();

        return response()->json($relatedBooks);
    }
}
