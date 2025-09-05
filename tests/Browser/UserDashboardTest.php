<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Fine;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->genre = Genre::factory()->create();
    $this->book = Book::factory()->create([
        'genre_id' => $this->genre->id,
        'status' => 'available',
    ]);
});

it('allows users to access dashboard', function (): void {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Welcome')
        ->assertSee($this->user->name);
});

it('displays borrowing statistics on dashboard', function (): void {
    // Create some borrows for the user
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Active Borrows');
});

it('shows overdue warnings on dashboard', function (): void {
    // Create an overdue borrow
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'due_date' => now()->subDays(5),
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Overdue');
});

it('displays active reservations on dashboard', function (): void {
    Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Reservations');
});

it('shows outstanding fines on dashboard', function (): void {
    Fine::factory()->create([
        'user_id' => $this->user->id,
        'amount' => 5.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Fine')
        ->assertSee('5.00');
});

it('provides access to borrowing history', function (): void {
    $response = $this->actingAs($this->user)->get('/borrows');

    $response->assertOk()
        ->assertSee('My Borrowed Books');
});

it('provides access to reservations', function (): void {
    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('My Reservations');
});

it('provides access to reading lists', function (): void {
    $response = $this->actingAs($this->user)->get('/reading-lists');

    $response->assertOk()
        ->assertSee('Reading Lists');
});

it('provides quick action navigation', function (): void {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Browse Books')
        ->assertSee('New Releases')
        ->assertSee('Recommendations');
});

it('displays recent activity', function (): void {
    // Create some recent activity
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'borrowed_at' => now()->subHours(2),
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Recent Activity')
        ->assertSee($this->book->title);
});

it('loads recommendations section', function (): void {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Recommended');
});

it('shows due soon notifications', function (): void {
    // Create a book due soon
    Borrow::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->book->id,
        'due_date' => now()->addDays(2),
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Due Soon');
});

it('handles empty dashboard states gracefully', function (): void {
    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Welcome');
});

it('prevents unauthenticated access to dashboard', function (): void {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});
