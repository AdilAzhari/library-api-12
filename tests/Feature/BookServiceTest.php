<?php

namespace Tests\Unit\Services;

use App\DTO\BookCreateDTO;
use App\DTO\BookUpdateDTO;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bookService = new BookService();
});

it('creates a book', function () {
    $dto = new BookCreateDTO(
        'Test Book',
        'Test Author',
        2023,
        'A test book description.',
        1,
        UploadedFile::fake()->image('cover.jpg')
    );

    $book = $this->bookService->createBook($dto);

    expect($book->title)->toBe('Test Book')
        ->and($book->author)->toBe('Test Author')
        ->and($book->publication_year)->toBe(2023)
        ->and($book->description)->toBe('A test book description.')
        ->and($book->genre_id)->toBe(1)
        ->and($book->cover_image)->not->toBeNull();
});

it('updates a book', function () {
    $book = Book::factory()->create();

    $dto = new BookUpdateDTO(
        'Updated Book',
        'Updated Author',
        2023,
        'An updated book description.',
        1,
        UploadedFile::fake()->image('updated_cover.jpg')
    );

    $updatedBook = $this->bookService->updateBook($book, $dto);

    expect($updatedBook->title)->toBe('Updated Book')
        ->and($updatedBook->author)->toBe('Updated Author')
        ->and($updatedBook->publication_year)->toBe(2023)
        ->and($updatedBook->description)->toBe('An updated book description.')
        ->and($updatedBook->genre_id)->toBe(1)
        ->and($updatedBook->cover_image)->not->toBeNull();
});

it('deletes a book', function () {
    $book = Book::factory()->create();

    $this->bookService->deleteBook($book);

    expect($book->fresh()->trashed())->toBeTrue();
});

it('restores a book', function () {
    $book = Book::factory()->create();
    $book->delete();

    $this->bookService->restoreBook($book->id);

    expect($book->fresh()->trashed())->toBeFalse();
});
