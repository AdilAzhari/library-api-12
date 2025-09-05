<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'role' => 'member',
        'email' => 'member@library.com',
        'password' => bcrypt('password'),
    ]);
    
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email' => 'admin@library.com',
        'password' => bcrypt('password'),
    ]);
    
    $this->genre = Genre::factory()->create(['name' => 'Fiction']);
    
    $this->book = Book::factory()->create([
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'isbn' => '9780743273565',
        'genre_id' => $this->genre->id,
        'status' => 'available',
        'copies_available' => 3,
        'total_copies' => 3,
    ]);
});

// ========================
// AUTHENTICATION FLOWS
// ========================

it('allows user login and redirects to dashboard - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->visit('/login')
            ->assertSee('Sign in to your account')
            ->type('email', 'member@library.com')
            ->type('password', 'password')
            ->press('Sign In')
            ->waitForLocation('/dashboard')
            ->assertPathIs('/dashboard')
            ->assertSee('Welcome')
            ->assertSee($this->user->name);
    });
});

it('allows user login and redirects to dashboard - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667) // iPhone SE size
            ->visit('/login')
            ->assertSee('Sign in')
            ->type('email', 'member@library.com')
            ->type('password', 'password')
            ->press('Sign In')
            ->waitForLocation('/dashboard')
            ->assertPathIs('/dashboard')
            ->assertSee('Welcome');
    });
});

it('shows validation errors for invalid login - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->visit('/login')
            ->type('email', 'invalid@example.com')
            ->type('password', 'wrongpassword')
            ->press('Sign In')
            ->waitForText('These credentials do not match')
            ->assertSee('These credentials do not match');
    });
});

it('allows new user registration - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->visit('/register')
            ->assertSee('Create your account')
            ->type('name', 'John Doe')
            ->type('email', 'john@example.com')
            ->type('password', 'password123')
            ->type('password_confirmation', 'password123')
            ->press('Register')
            ->waitForLocation('/dashboard')
            ->assertPathIs('/dashboard')
            ->assertSee('Welcome');
    });
});

// ========================
// BOOK BROWSING & SEARCH
// ========================

it('displays book catalog and allows search - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->assertSee('Book Catalog')
            ->assertSee($this->book->title)
            ->assertSee($this->book->author)
            ->type('search', 'Gatsby')
            ->keys('input[name="search"]', ['{enter}'])
            ->waitForText($this->book->title)
            ->assertSee($this->book->title)
            ->assertSee('Available');
    });
});

it('displays book catalog and allows search - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books')
            ->assertSee('Books')
            ->assertSee($this->book->title)
            ->tap('input[name="search"]')
            ->type('search', 'Gatsby')
            ->keys('input[name="search"]', ['{enter}'])
            ->waitForText($this->book->title)
            ->assertSee($this->book->title);
    });
});

it('shows book details when clicked - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->clickLink($this->book->title)
            ->waitForLocation('/books/' . $this->book->id)
            ->assertSee($this->book->title)
            ->assertSee($this->book->author)
            ->assertSee($this->book->isbn)
            ->assertSee('Available')
            ->assertSee('Borrow Book');
    });
});

it('allows filtering books by genre - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->select('genre', $this->genre->id)
            ->waitForText($this->book->title)
            ->assertSee($this->book->title)
            ->assertSee($this->genre->name);
    });
});

// ========================
// BORROWING WORKFLOW
// ========================

it('completes full borrowing workflow - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books/' . $this->book->id)
            ->assertSee('Borrow Book')
            ->press('Borrow Book')
            ->waitForText('Book borrowed successfully')
            ->assertSee('Book borrowed successfully')
            ->visit('/borrows')
            ->assertSee('My Borrowed Books')
            ->assertSee($this->book->title)
            ->assertSee('Active');
    });
});

it('completes borrowing workflow on mobile - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books/' . $this->book->id)
            ->assertSee('Borrow')
            ->tap('button:contains("Borrow")')
            ->waitForText('borrowed successfully')
            ->assertSee('borrowed successfully');
    });
});

it('shows due dates and renewal options in borrows - DESKTOP', function (): void {
    // First borrow the book
    $this->user->borrows()->create([
        'book_id' => $this->book->id,
        'borrowed_at' => now(),
        'due_date' => now()->addDays(14),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/borrows')
            ->assertSee($this->book->title)
            ->assertSee('Due:')
            ->assertSee('Renew')
            ->press('Renew')
            ->waitForText('renewed successfully')
            ->assertSee('renewed successfully');
    });
});

// ========================
// RESERVATION SYSTEM
// ========================

it('allows making and managing reservations - DESKTOP', function (): void {
    // Make book unavailable
    $this->book->update(['status' => 'borrowed', 'copies_available' => 0]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books/' . $this->book->id)
            ->assertSee('Reserve Book')
            ->press('Reserve Book')
            ->waitForText('Book reserved successfully')
            ->assertSee('Book reserved successfully')
            ->visit('/reservations')
            ->assertSee('My Reservations')
            ->assertSee($this->book->title)
            ->assertSee('Active');
    });
});

it('allows cancelling reservations - DESKTOP', function (): void {
    // Create a reservation
    $this->user->reservations()->create([
        'book_id' => $this->book->id,
        'reserved_at' => now(),
        'expires_at' => now()->addDays(7),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/reservations')
            ->assertSee($this->book->title)
            ->press('Cancel')
            ->waitForText('Reservation cancelled')
            ->assertSee('Reservation cancelled');
    });
});

// ========================
// DASHBOARD FUNCTIONALITY
// ========================

it('displays comprehensive dashboard information - DESKTOP', function (): void {
    // Create some user activity
    $this->user->borrows()->create([
        'book_id' => $this->book->id,
        'borrowed_at' => now(),
        'due_date' => now()->addDays(14),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Welcome')
            ->assertSee('Active Borrows')
            ->assertSee($this->book->title)
            ->assertSee('Browse Books')
            ->assertSee('New Releases')
            ->assertSee('My Reservations')
            ->assertSee('Reading Lists');
    });
});

it('displays dashboard on mobile with proper layout - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Welcome')
            ->assertSee('Browse')
            ->assertSee('Reservations');
    });
});

it('shows overdue warnings on dashboard - DESKTOP', function (): void {
    // Create overdue borrow
    $this->user->borrows()->create([
        'book_id' => $this->book->id,
        'borrowed_at' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Overdue')
            ->assertSee($this->book->title)
            ->assertSee('5 days overdue');
    });
});

// ========================
// NAVIGATION & UX
// ========================

it('provides seamless navigation between sections - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/dashboard')
            ->clickLink('Browse Books')
            ->waitForLocation('/books')
            ->assertPathIs('/books')
            ->clickLink('Dashboard')
            ->waitForLocation('/dashboard')
            ->assertPathIs('/dashboard')
            ->clickLink('My Borrowed Books')
            ->waitForLocation('/borrows')
            ->assertPathIs('/borrows');
    });
});

it('handles mobile navigation menu properly - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/dashboard')
            ->click('button[aria-label="Toggle menu"]') // Mobile menu button
            ->waitFor('.mobile-menu')
            ->assertVisible('.mobile-menu')
            ->clickLink('Books')
            ->waitForLocation('/books')
            ->assertPathIs('/books');
    });
});

it('displays search results with pagination - DESKTOP', function (): void {
    // Create multiple books for pagination
    Book::factory()->count(20)->create(['genre_id' => $this->genre->id]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->assertSee('Page 1')
            ->assertSee('Next')
            ->clickLink('Next')
            ->waitForText('Page 2')
            ->assertSee('Page 2')
            ->assertSee('Previous');
    });
});

// ========================
// ADMIN FUNCTIONALITY
// ========================

it('allows admin to access admin dashboard - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/dashboard')
            ->assertSee('Admin Dashboard')
            ->assertSee('Total Books')
            ->assertSee('Active Borrows')
            ->assertSee('Total Users')
            ->assertSee('Recent Activity');
    });
});

it('allows admin to manage books - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/books')
            ->assertSee('Manage Books')
            ->assertSee($this->book->title)
            ->clickLink('Add New Book')
            ->waitForLocation('/admin/books/create')
            ->assertSee('Add New Book')
            ->type('title', 'New Test Book')
            ->type('author', 'Test Author')
            ->type('isbn', '9781234567890')
            ->select('genre_id', $this->genre->id)
            ->type('description', 'Test description')
            ->press('Create Book')
            ->waitForText('Book created successfully')
            ->assertSee('Book created successfully');
    });
});

it('prevents non-admin users from accessing admin areas - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user) // Regular user
            ->visit('/admin/dashboard')
            ->assertPathIs('/')  // Should be redirected
            ->assertSee('Access denied'); // Or appropriate error message
    });
});

// ========================
// ERROR HANDLING & EDGE CASES
// ========================

it('handles network errors gracefully - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->script("
                // Simulate network error
                window.fetch = () => Promise.reject(new Error('Network error'));
            ")
            ->refresh()
            ->assertSee('Unable to load'); // Or appropriate error message
    });
});

it('shows appropriate loading states - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/books')
            ->type('search', 'loading test')
            ->keys('input[name="search"]', ['{enter}'])
            ->assertVisible('.loading') // Loading indicator should be visible
            ->waitForText('No books found')
            ->assertSee('No books found');
    });
});

it('maintains session across page reloads - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Welcome')
            ->refresh()
            ->assertSee('Welcome') // Still logged in
            ->assertDontSee('Sign In'); // Not redirected to login
    });
});

// ========================
// RESPONSIVE DESIGN TESTS
// ========================

it('adapts layout properly on tablet size - TABLET', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(768, 1024) // iPad size
            ->loginAs($this->user)
            ->visit('/books')
            ->assertSee($this->book->title)
            ->assertVisible('.tablet-layout') // Assuming tablet-specific classes
            ->click('.book-card')
            ->waitFor('.book-details')
            ->assertVisible('.book-details');
    });
});

it('handles very small screens gracefully - SMALL MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(320, 568) // iPhone 5 size
            ->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Welcome')
            ->assertVisible('.mobile-optimized') // Mobile-specific layout
            ->click('button[aria-label="Menu"]')
            ->waitFor('.mobile-menu')
            ->assertVisible('.mobile-menu');
    });
});