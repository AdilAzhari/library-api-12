<?php

declare(strict_types=1);

use App\Models\User;

it('allows new users to register', function (): void {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('allows existing users to login', function (): void {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('shows validation errors on registration with empty form', function (): void {
    $response = $this->post('/register', []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

it('shows validation errors on login with empty form', function (): void {
    $response = $this->post('/login', []);

    $response->assertSessionHasErrors(['email', 'password']);
});

it('shows error for invalid login credentials', function (): void {
    $response = $this->post('/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('validates password confirmation on registration', function (): void {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ]);

    $response->assertSessionHasErrors('password');
});

it('provides forgot password functionality', function (): void {
    $response = $this->get('/forgot-password');

    $response->assertOk()
        ->assertSee('forgot', false); // Case-insensitive match for forgot
});

it('processes password reset requests', function (): void {
    $user = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'john@example.com',
    ]);

    $response->assertSessionHas('status');
});

it('redirects guests to login for protected routes', function (): void {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

it('prevents authenticated users from accessing guest routes', function (): void {
    $user = User::factory()->create();

    $loginResponse = $this->actingAs($user)->get('/login');
    $registerResponse = $this->actingAs($user)->get('/register');

    $loginResponse->assertRedirect('/dashboard');
    $registerResponse->assertRedirect('/dashboard');
});

it('validates email format on registration', function (): void {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('prevents duplicate email registration', function (): void {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('enforces minimum password length', function (): void {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});

it('allows users to logout', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/logout');

    $response->assertRedirect('/');
    $this->assertGuest();
});
