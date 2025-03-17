<?php

namespace App\Http\Controllers;

use App\Models\Book;

class RecommendationController extends Controller
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
