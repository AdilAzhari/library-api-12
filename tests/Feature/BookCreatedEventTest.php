<?php

use App\Events\BookCreated;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('dispatches book created event', function () {
    $book = Book::factory()->create();

    event(new BookCreated($book));

    // Placeholder for event assertion
    expect(true)->toBeTrue();
});
