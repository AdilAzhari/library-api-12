<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->genre = Genre::factory()->create();

    $this->availableBook = Book::factory()->create([
        'genre_id' => $this->genre->id,
        'status' => 'available',
    ]);

    $this->borrowedBook = Book::factory()->create([
        'genre_id' => $this->genre->id,
        'status' => 'borrowed',
    ]);
});

it('allows users to reserve available books', function (): void {
    $response = $this->actingAs($this->user)
        ->post("/books/{$this->availableBook->id}/reserve");

    $response->assertRedirect()
        ->assertSessionHas('success');

    // Verify reservation was created
    $this->assertDatabaseHas('reservations', [
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
    ]);
});

it('allows users to reserve borrowed books', function (): void {
    $response = $this->actingAs($this->user)
        ->post("/books/{$this->borrowedBook->id}/reserve");

    $response->assertRedirect()
        ->assertSessionHas('success');

    // Verify reservation was created
    $this->assertDatabaseHas('reservations', [
        'user_id' => $this->user->id,
        'book_id' => $this->borrowedBook->id,
    ]);
});

it('displays reservations correctly', function (): void {
    $reservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('My Reservations')
        ->assertSee($this->availableBook->title)
        ->assertSee($this->availableBook->author);
});

it('shows ready for pickup notifications', function (): void {
    $readyReservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('Ready for Pickup');
});

it('shows expiring reservation warnings', function (): void {
    $expiringReservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addHours(12),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('Expires in');
});

it('allows users to cancel reservations', function (): void {
    $reservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)
        ->delete("/reservations/{$reservation->id}");

    $response->assertRedirect('/reservations')
        ->assertSessionHas('success');

    // Verify reservation was cancelled
    $this->assertDatabaseMissing('reservations', [
        'id' => $reservation->id,
        'cancelled_at' => null,
    ]);
});

it('displays reservation statistics', function (): void {
    // Create various reservation states
    Reservation::factory()->create([
        'user_id' => $this->user->id,
        'expires_at' => now()->addDays(5),
    ]);

    Reservation::factory()->create([
        'user_id' => $this->user->id,
        'expires_at' => now()->addHours(12),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('Active Reservations')
        ->assertSee('Ready for Pickup')
        ->assertSee('Expiring Soon');
});

it('shows priority queue position', function (): void {
    $reservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->borrowedBook->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee($this->borrowedBook->title);
});

it('handles empty reservations state', function (): void {
    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('No active reservations')
        ->assertSee('Browse our collection');
});

it('displays reservation expiration time', function (): void {
    $reservation = Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addDays(2),
    ]);

    $response = $this->actingAs($this->user)->get('/reservations');

    $response->assertOk()
        ->assertSee('Expires')
        ->assertSee('2 days');
});

it('prevents unauthenticated reservation access', function (): void {
    $response = $this->get('/reservations');

    $response->assertRedirect('/login');
});

it('prevents reserving by unauthenticated users', function (): void {
    $response = $this->post("/books/{$this->availableBook->id}/reserve");

    $response->assertRedirect('/login');
});

it('prevents accessing other users reservations', function (): void {
    $otherUser = User::factory()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $otherUser->id,
        'book_id' => $this->availableBook->id,
    ]);

    $response = $this->actingAs($this->user)
        ->delete("/reservations/{$reservation->id}");

    $response->assertStatus(403);
});

it('prevents duplicate reservations for same book', function (): void {
    // Create existing reservation
    Reservation::factory()->create([
        'user_id' => $this->user->id,
        'book_id' => $this->availableBook->id,
        'expires_at' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->user)
        ->post("/books/{$this->availableBook->id}/reserve");

    $response->assertRedirect()
        ->assertSessionHasErrors();
});
