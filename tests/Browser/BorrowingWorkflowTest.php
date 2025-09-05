<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Borrow;
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

it('completes the full borrowing workflow', function (): void {
    // View books listing
    $response = $this->get('/books');
    $response->assertOk()->assertSee($this->book->title);

    // View book details
    $response = $this->get("/books/{$this->book->id}");
    $response->assertOk()->assertSee('Borrow');

    // Borrow the book
    $response = $this->actingAs($this->user)
        ->post("/books/{$this->book->id}/borrow");

    $response->assertRedirect('/borrows');

    // Check borrowing history
    $response = $this->actingAs($this->user)->get('/borrows');
    $response->assertOk()
        ->assertSee('My Borrowed Books')
        ->assertSee($this->book->title);
});

it('displays borrowing history correctly', function (): void {
    // Create borrowing history
    $activeBorrow = Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
        'borrowed_at' => now(),
        'due_date' => now()->addWeeks(2),
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('My Borrowed Books')
        ->assertSee($this->book->title)
        ->assertSee($this->book->author)
        ->assertSee('Return');
});

it('shows overdue books correctly', function (): void {
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
        'due_date' => now()->subDays(3),
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('OVERDUE');
});

it('shows due soon book warnings', function (): void {
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
        'due_date' => now()->addDays(2),
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('Due in 2 days');
});

it('enables access to return book workflow', function (): void {
    $activeBorrow = Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');
    $response->assertOk()->assertSee('Return');

    $response = $this->actingAs($this->user)->get("/borrows/{$activeBorrow->id}/return");
    $response->assertOk()
        ->assertSee('Return Book')
        ->assertSee($this->book->title);
});

it('displays borrowing statistics', function (): void {
    // Create various types of borrows
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
    ]);

    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'active',
        'due_date' => now()->subDays(2),
    ]);

    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'returned',
        'returned_at' => now()->subDays(1),
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('Currently Borrowed')
        ->assertSee('Overdue Books');
});

it('handles empty borrowing state', function (): void {
    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('No active borrows')
        ->assertSee('Browse our collection');
});

it('calculates overdue days correctly', function (): void {
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
        'due_date' => now()->subDays(5),
    ]);

    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('5 days overdue');
});

it('processes book return request', function (): void {
    $activeBorrow = Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->user)
        ->patch("/borrows/{$activeBorrow->id}/return");

    $response->assertRedirect('/borrows');

    // Verify the borrow was marked as returned
    $this->assertDatabaseHas('borrows', [
        'id' => $activeBorrow->id,
        'status' => 'returned',
    ]);
});

it('prevents unauthenticated borrowing', function (): void {
    $response = $this->post("/books/{$this->book->id}/borrow");

    $response->assertRedirect('/login');
});

it('prevents accessing other users borrowing history', function (): void {
    $otherUser = User::factory()->create();
    $borrow = Borrow::factory()->create([
        'user_id' => $otherUser->id,
        'book_id' => $this->book->id,
    ]);

    $response = $this->actingAs($this->user)
        ->get("/borrows/{$borrow->id}/return");

    $response->assertStatus(403);
});
