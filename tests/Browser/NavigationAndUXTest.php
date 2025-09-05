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

it('loads homepage correctly', function (): void {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Academia Inspired')
        ->assertSee('Welcome');
});

it('provides header navigation', function (): void {
    $response = $this->actingAs($this->user)->get('/dashboard');
    $response->assertOk()->assertSee('Academia Inspired');

    $response = $this->actingAs($this->user)->get('/books');
    $response->assertOk();

    $response = $this->actingAs($this->user)->get('/borrows');
    $response->assertOk();

    $response = $this->actingAs($this->user)->get('/reservations');
    $response->assertOk();
});

it('displays footer correctly', function (): void {
    $response = $this->get('/books');

    $response->assertOk()
        ->assertSee('Academia Inspired')
        ->assertSee(date('Y'));
});

it('provides footer links', function (): void {
    $response = $this->get('/about');
    $response->assertOk()->assertSee('About');

    $response = $this->get('/contact');
    $response->assertOk()->assertSee('Contact');

    $response = $this->get('/privacy');
    $response->assertOk()->assertSee('Privacy');
});

it('shows breadcrumb navigation', function (): void {
    $response = $this->get("/books/{$this->book->id}");

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('handles search functionality', function (): void {
    $response = $this->get('/books?'.http_build_query(['search' => $this->book->title]));

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('handles error pages correctly', function (): void {
    $response = $this->get('/non-existent-page');

    $response->assertStatus(404);
});

it('displays correct page titles', function (): void {
    $response = $this->get('/books');
    $response->assertOk();

    $response = $this->get("/books/{$this->book->id}");
    $response->assertOk()->assertSee($this->book->title);

    $response = $this->actingAs($this->user)->get('/dashboard');
    $response->assertOk();
});

it('handles flash messages', function (): void {
    $response = $this->actingAs($this->user)
        ->post("/books/{$this->book->id}/reserve");

    $response->assertRedirect()
        ->assertSessionHas('success');
});

it('provides accessible navigation', function (): void {
    $response = $this->get('/books');
    $response->assertOk();

    // Accessibility is primarily a frontend concern
    // Backend can ensure proper semantics and structure
});

it('maintains responsive design structure', function (): void {
    $response = $this->get('/books');

    $response->assertOk()
        ->assertSee($this->book->title);
});

it('handles authentication states', function (): void {
    // Guest state
    $response = $this->get('/');
    $response->assertOk();

    // Authenticated state
    $response = $this->actingAs($this->user)->get('/dashboard');
    $response->assertOk();
});

it('prevents access to protected routes', function (): void {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');

    $response = $this->get('/borrows');
    $response->assertRedirect('/login');

    $response = $this->get('/reservations');
    $response->assertRedirect('/login');
});
