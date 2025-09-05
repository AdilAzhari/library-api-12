<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->genre = Genre::factory()->create();
    $this->book = Book::factory()->create([
        'genre_id' => $this->genre->id,
        'status' => 'available',
    ]);
});

it('allows users to browse books', function (): void {
    $response = $this->get('/books');

    $response->assertOk()
        ->assertSee('Discover Your Next Read')
        ->assertSee($this->book->title)
        ->assertSee($this->book->author);
});

it('displays book details correctly', function (): void {
    $response = $this->get("/books/{$this->book->id}");

    $response->assertOk()
        ->assertSee($this->book->title)
        ->assertSee($this->book->author)
        ->assertSee($this->book->description)
        ->assertSee((string) $this->book->publication_year);
});

it('allows authenticated users to borrow available books', function (): void {
    $response = $this->actingAs($this->user)
        ->post("/books/{$this->book->id}/borrow");

    $response->assertRedirect('/borrows');

    // Verify borrow was created
    $this->assertDatabaseHas('borrows', [
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
    ]);
});

it('allows authenticated users to reserve books', function (): void {
    // First make the book borrowed so it can be reserved
    $this->book->update(['status' => 'borrowed']);

    $response = $this->actingAs($this->user)
        ->post("/books/{$this->book->id}/reserve");

    $response->assertRedirect()
        ->assertSessionHas('success');

    // Verify reservation was created
    $this->assertDatabaseHas('reservations', [
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
    ]);
});

it('shows correct book status indicators', function (): void {
    $borrowedBook = Book::factory()->create([
        'genre_id' => $this->genre->id,
        'status' => 'borrowed',
    ]);

    $response = $this->get('/books');

    $response->assertOk()
        ->assertSee($borrowedBook->title);
});

it('filters books by genre', function (): void {
    $response = $this->get('/books?'.http_build_query(['genre' => $this->genre->id]));

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('filters books by availability status', function (): void {
    $response = $this->get('/books?'.http_build_query(['status' => 'available']));

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('handles book search functionality', function (): void {
    $response = $this->get('/books?'.http_build_query(['search' => $this->book->title]));

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('handles pagination correctly', function (): void {
    // Create more books to test pagination
    Book::factory(15)->create(['genre_id' => $this->genre->id]);

    $response = $this->get('/books');

    $response->assertOk();

    // Check if pagination data is passed to the view
    $response->assertViewHas('books');
});

it('prevents unauthenticated users from borrowing', function (): void {
    $response = $this->post("/books/{$this->book->id}/borrow");

    $response->assertRedirect('/login');
});

it('prevents unauthenticated users from reserving', function (): void {
    $response = $this->post("/books/{$this->book->id}/reserve");

    $response->assertRedirect('/login');
});
