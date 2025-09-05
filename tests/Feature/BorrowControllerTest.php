<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create(['role' => \App\Enum\UserRoles::ADMIN]); // Make user admin to bypass reservation requirement
    $this->book = Book::factory()->create(['status' => 'available']);
    $this->token = $this->user->createToken('test-token')->plainTextToken;
    $this->actingAs($this->user);
});

it('borrows a book', function (): void {
    $response = $this->postJson('/api/public/v1/borrow', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Book borrowed successfully']);

    $this->assertDatabaseHas('borrows', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'returned_at' => null,
    ]);

    $this->assertEquals('borrowed', Book::query()->find($this->book->id)->status->value);
});

it('returns a borrowed book', function (): void {
    $borrowing = Borrow::factory()->create([
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'borrowed_at' => now(),
        'returned_at' => null,
    ]);

    $response = $this->postJson('/api/public/v1/return', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Book returned successfully']);

    $this->assertDatabaseHas('borrows', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseMissing('borrows', [
        'book_id' => $this->book->id,
        'user_id' => $this->user->id,
        'returned_at' => null,
    ]);

    $this->assertEquals('available', Book::query()->find($this->book->id)->status->value);
});
