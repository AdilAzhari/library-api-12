<?php

declare(strict_types=1);

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use App\Services\BorrowingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->service = new BorrowingService;
    $this->user = User::factory()->create(['role' => \App\Enum\UserRoles::ADMIN]); // Make user admin to bypass reservation requirement
    $this->book = Book::factory()->create(['status' => 'available']);
});

it(/**
 * @throws Exception
 */ 'borrows a book', function (): void {
    $dto = new BorrowBookDTO($this->book->id, $this->user->id);

    $borrowing = $this->service->borrowBook($dto);

    expect($borrowing)->toBeInstanceOf(Borrow::class)
        ->and($borrowing->book_id)->toBe($this->book->id)
        ->and($borrowing->user_id)->toBe($this->user->id)
        ->and($borrowing->returned_at)->toBeNull();

    $this->assertEquals('borrowed', Book::query()->find($this->book->id)->status->value);
});

it(/**
 * @throws Exception
 */ 'returns a borrowed book', function (): void {
    $borrowing = Borrow::factory()->create([
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'borrowed_at' => now(),
        'returned_at' => null,
    ]);

    $dto = new ReturnBookDTO($borrowing->id, $this->book->id, $this->user->id);

    $returnedBorrowing = $this->service->returnBook($dto);

    expect($returnedBorrowing)->toBeInstanceOf(Borrow::class)
        ->and($returnedBorrowing->returned_at)->not->toBeNull();

    $this->assertEquals('available', Book::query()->find($this->book->id)->status->value);
});
