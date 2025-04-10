<?php

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use App\Services\BorrowingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new BorrowingService();
    $this->user = User::factory()->create();
    $this->book = Book::factory()->create(['is_borrowed' => false]);
});

it(/**
 * @throws Exception
 */ 'borrows a book', function () {
    $dto = new BorrowBookDTO($this->book->id, $this->user->id);

    $borrowing = $this->service->borrowBook($dto);

    expect($borrowing)->toBeInstanceOf(Borrow::class)
        ->and($borrowing->book_id)->toBe($this->book->id)
        ->and($borrowing->user_id)->toBe($this->user->id)
        ->and($borrowing->returned_at)->toBeNull();

    $this->assertTrue(Book::query()->find($this->book->id)->is_borrowed);
});

it(/**
 * @throws Exception
 */ 'returns a borrowed book', function () {
    $borrowing = Borrow::factory()->create([
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'borrowed_at' => now(),
        'returned_at' => null,
    ]);

    $dto = new ReturnBookDTO($this->book->id, $this->user->id);

    $returnedBorrowing = $this->service->returnBook($dto);

    expect($returnedBorrowing)->toBeInstanceOf(Borrow::class)
        ->and($returnedBorrowing->returned_at)->not->toBeNull();

    $this->assertFalse(Book::find($this->book->id)->is_borrowed);
});
