<?php

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->book = Book::factory()->create(['is_borrowed' => false]);
    $this->actingAs($this->user);
});

it('borrows a book', function () {
    $response = $this->postJson('/api/v1/borrow', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ]);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Book borrowed successfully']);

    $this->assertDatabaseHas('borrows', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'returned_at' => null,
    ]);

    $this->assertTrue(Book::query()->find($this->book->id)->is_borrowed);
});

it('returns a borrowed book', function () {
    $borrowing = Borrow::factory()->create([
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'borrowed_at' => now(),
        'returned_at' => null,
    ]);

    $response = $this->postJson('/api/v1/return', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Book returned successfully']);

    $this->assertDatabaseHas('borrows', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'returned_at' => now(),
    ]);

    $this->assertFalse(Book::query()->find($this->book->id)->is_borrowed);
});
