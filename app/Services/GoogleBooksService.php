<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    public function searchBooks($query): array
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
        ]);

        return $response->json();
    }
}
