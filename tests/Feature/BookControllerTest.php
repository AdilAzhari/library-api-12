<?php

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->actingAs($this->user);
});

it('lists all books', function () {

    Cache::flush();

    Book::factory()->count(5)->create();

    $response = $this->getJson('/api/v1/books');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

it('creates a book', function () {
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
    $response = $this->postJson('/api/v1/books', $data);

    $response->assertStatus(201)
        ->assertJson(['message' => 'Book created successfully'])
        ->assertJson(['title' => 'Test Book']);
});

it('shows a book', function () {
    $book = Book::factory()->create();

    $response = $this->getJson("/api/v1/books/$book->id");

    $response->assertStatus(200)
        ->assertJson(['id' => $book->id]);
});

it('updates a book', function () {
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

    $response = $this->putJson("/api/v1/books/$book->id", $data);

    $response->assertStatus(200)
        ->assertJson(['title' => 'Book updated successfully']);
});

it('deletes a book', function () {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/v1/books/$book->id");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Book moved to trash']);
});
