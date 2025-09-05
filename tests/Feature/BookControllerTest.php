<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    $this->user = App\Models\User::factory()->create(['role' => 'User']);
    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

it('lists all books', function (): void {
    Cache::flush();

    // Add 5 new books
    Book::factory()->count(5)->create();

    $response = $this->getJson('/api/public/v1/books', [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['data', 'success', 'message'])
        ->assertJson(['success' => true]);

    // Verify we have at least some books in the response
    expect($response->json('data'))->not()->toBeEmpty();
    expect($response->json('data'))->toBeArray();
});

it('creates a book', function (): void {
    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'publication_year' => 2023,
        'description' => 'A test book description.',
        'genre_id' => Genre::factory()->create([
            'status' => true,
        ])->id,
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
    ];
    $response = $this->postJson('/api/public/v1/books', $data, [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Book created successfully'])
        ->assertJsonPath('data.title', 'Test Book');
});

it('shows a book', function (): void {
    $book = Book::factory()->create();

    $response = $this->getJson("/api/public/v1/books/$book->id", [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $book->id);
});

it('updates a book', function (): void {
    $book = Book::factory()->create();

    $data = [
        'title' => 'Updated Book',
        'author' => 'Updated Author',
        'publication_year' => 2023,
        'description' => 'An updated book description.',
        'genre_id' => Genre::factory()->create([
            'status' => true,
        ])->id,
        'cover_image' => UploadedFile::fake()->image('updated_cover.jpg'),
    ];

    $response = $this->putJson("/api/public/v1/books/$book->id", $data, [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Book updated successfully']);
});

it('deletes a book', function (): void {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/public/v1/books/$book->id", [], [
        'Authorization' => 'Bearer '.$this->token,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Book moved to trash']);
});
