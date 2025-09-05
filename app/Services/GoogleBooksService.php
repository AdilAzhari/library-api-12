<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class GoogleBooksService
{
    public function searchBooks($query): array
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
        ]);

        return $response->json();
    }
}
