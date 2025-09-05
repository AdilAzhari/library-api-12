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
        'email' => 'mobile@library.com',
    ]);
    
    $this->genre = Genre::factory()->create(['name' => 'Fiction']);
    $this->book = Book::factory()->create([
        'title' => 'Mobile Test Book',
        'author' => 'Test Author',
        'genre_id' => $this->genre->id,
        'status' => 'available',
    ]);
});

// ========================
// MOBILE DEVICE TESTING
// ========================

it('works perfectly on iPhone SE (320px) - CRITICAL MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(320, 568) // iPhone SE - smallest common size
            ->loginAs($this->user)
            ->visit('/dashboard')
            ->assertSee('Welcome')
            ->pause(1000) // Allow for mobile animations
            
            // Test mobile navigation
            ->click('button[data-mobile-menu]')
            ->waitFor('.mobile-menu', 2)
            ->assertVisible('.mobile-menu')
            ->within('.mobile-menu', function ($menu): void {
                $menu->assertSee('Books')
                    ->assertSee('Borrows')
                    ->assertSee('Reservations');
            })
            
            // Navigate to books
            ->clickLink('Books')
            ->waitForLocation('/books')
            ->assertPathIs('/books')
            
            // Test mobile book card layout
            ->assertVisible('.book-card')
            ->tap('.book-card:first-child')
            ->waitFor('.book-details-mobile')
            ->assertVisible('.book-details-mobile')
            
            // Test mobile borrowing
            ->tap('[data-mobile-borrow]')
            ->waitForText('borrowed')
            ->assertSee('borrowed');
    });
});

it('adapts properly to iPhone 12/13 Pro (390px) - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(390, 844) // iPhone 12/13 Pro
            ->loginAs($this->user)
            ->visit('/books')
            ->assertVisible('.book-grid-mobile')
            ->assertElementsCount('.book-card', 2) // Should show 2 columns on this size
            
            // Test search on mobile
            ->tap('input[name="search"]')
            ->type('search', $this->book->title)
            ->keys('input[name="search"]', ['{enter}'])
            ->waitForText($this->book->title)
            ->assertSee($this->book->title)
            
            // Test mobile filters
            ->tap('[data-mobile-filters]')
            ->waitFor('.mobile-filters-panel')
            ->within('.mobile-filters-panel', function ($panel): void {
                $panel->select('genre', $this->genre->id)
                    ->tap('[data-apply-filters]');
            })
            ->waitForText($this->book->title)
            ->assertSee($this->book->title);
    });
});

it('works on Samsung Galaxy S20 (360px) - MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(360, 640) // Samsung Galaxy S20
            ->loginAs($this->user)
            ->visit('/dashboard')
            
            // Test mobile dashboard layout
            ->assertVisible('.dashboard-mobile')
            ->assertSee('Active Borrows')
            ->assertSee('Quick Actions')
            
            // Test mobile quick actions
            ->tap('[data-quick-action="browse"]')
            ->waitForLocation('/books')
            ->assertPathIs('/books')
            
            // Test mobile book interaction
            ->longPress('.book-card:first-child') // Long press for mobile context menu
            ->waitFor('.mobile-context-menu')
            ->within('.mobile-context-menu', function ($menu): void {
                $menu->assertSee('View Details')
                    ->assertSee('Add to Reading List')
                    ->tap('View Details');
            })
            ->waitFor('.book-details-mobile')
            ->assertVisible('.book-details-mobile');
    });
});

// ========================
// TABLET TESTING
// ========================

it('adapts properly to iPad (768px) - TABLET', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(768, 1024) // iPad
            ->loginAs($this->user)
            ->visit('/books')
            
            // Tablet should show more books per row
            ->assertVisible('.book-grid-tablet')
            ->assertElementsCount('.book-card', 3) // Should show 3 columns
            
            // Test tablet sidebar navigation
            ->assertVisible('.sidebar-tablet')
            ->within('.sidebar-tablet', function ($sidebar): void {
                $sidebar->assertSee('Dashboard')
                    ->assertSee('Books')
                    ->assertSee('Borrows');
            })
            
            // Test tablet book details view
            ->click('.book-card:first-child')
            ->waitFor('.book-details-tablet')
            ->assertVisible('.book-details-tablet')
            ->within('.book-details-tablet', function ($details): void {
                $details->assertSee($this->book->title)
                    ->assertSee($this->book->author)
                    ->assertVisible('[data-borrow-button]');
            });
    });
});

it('handles iPad Pro landscape (1024px) - LARGE TABLET', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(1024, 768) // iPad Pro landscape
            ->loginAs($this->user)
            ->visit('/books')
            
            // Should show desktop-like layout on large tablets
            ->assertVisible('.book-grid-desktop')
            ->assertElementsCount('.book-card', 4) // Should show 4 columns
            
            // Test advanced search on large tablet
            ->click('[data-advanced-search]')
            ->waitFor('.advanced-search-panel')
            ->within('.advanced-search-panel', function ($panel): void {
                $panel->type('title', $this->book->title)
                    ->select('genre', $this->genre->id)
                    ->click('[data-search-submit]');
            })
            ->waitForText($this->book->title)
            ->assertSee($this->book->title);
    });
});

// ========================
// TOUCH INTERACTIONS
// ========================

it('handles touch gestures properly - TOUCH MOBILE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667) // iPhone SE
            ->loginAs($this->user)
            ->visit('/books')
            
            // Test swipe gestures for book carousel
            ->assertVisible('.book-carousel-mobile')
            ->swipeLeft('.book-carousel-mobile')
            ->pause(500)
            ->assertVisible('.book-card:nth-child(3)') // Should show next books
            
            // Test pull-to-refresh
            ->swipeDown('body', 200) // Pull down gesture
            ->waitFor('.refresh-indicator')
            ->pause(2000) // Wait for refresh
            ->assertVisible('.book-card')
            
            // Test pinch-to-zoom on book cover
            ->click('.book-card:first-child')
            ->waitFor('.book-cover-mobile')
            ->pinch('.book-cover-mobile', 150, 150, 2.0) // Pinch to zoom
            ->assertVisible('.book-cover-zoomed');
    });
});

it('provides proper touch targets (44px minimum) - ACCESSIBILITY', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(320, 568)
            ->loginAs($this->user)
            ->visit('/dashboard')
            
            // All touch targets should be at least 44px
            ->script("
                const buttons = document.querySelectorAll('button, a, [role=\"button\"]');
                const smallButtons = Array.from(buttons).filter(btn => {
                    const rect = btn.getBoundingClientRect();
                    return rect.width < 44 || rect.height < 44;
                });
                return smallButtons.length;
            ")
            ->with(function ($result): void {
                expect($result)->toBe(0); // No buttons should be smaller than 44px
            });
    });
});

// ========================
// ORIENTATION CHANGES
// ========================

it('handles portrait to landscape orientation change - ORIENTATION', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667) // Portrait
            ->loginAs($this->user)
            ->visit('/books')
            ->assertVisible('.portrait-layout')
            
            // Simulate orientation change to landscape
            ->resize(667, 375) // Landscape
            ->pause(1000) // Allow for layout adjustment
            ->assertVisible('.landscape-layout')
            ->assertElementsCount('.book-card', 2) // Should show more books horizontally
            
            // Ensure functionality still works in landscape
            ->tap('.book-card:first-child')
            ->waitFor('.book-details-landscape')
            ->assertVisible('.book-details-landscape');
    });
});

// ========================
// PERFORMANCE ON MOBILE
// ========================

it('loads quickly on slow mobile connections - PERFORMANCE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            
            // Simulate slow 3G connection
            ->script("
                if (navigator.connection) {
                    navigator.connection.effectiveType = '3g';
                }
            ")
            
            ->visit('/books')
            ->waitFor('.book-card', 5) // Should load within 5 seconds even on slow connection
            ->assertVisible('.book-card')
            ->assertCount('.book-card', '>', 0);
    });
});

it('handles offline state gracefully - OFFLINE', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books')
            ->waitFor('.book-card')
            
            // Simulate going offline
            ->script("
                window.dispatchEvent(new Event('offline'));
                navigator.onLine = false;
            ")
            
            ->pause(1000)
            ->assertVisible('.offline-indicator')
            ->assertSee('No internet connection')
            
            // Simulate coming back online
            ->script("
                window.dispatchEvent(new Event('online'));
                navigator.onLine = true;
            ")
            
            ->pause(1000)
            ->assertNotVisible('.offline-indicator');
    });
});

// ========================
// MOBILE-SPECIFIC FEATURES
// ========================

it('supports mobile sharing functionality - MOBILE SHARING', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books/' . $this->book->id)
            ->waitFor('.book-details-mobile')
            
            // Test native mobile sharing
            ->tap('[data-mobile-share]')
            ->pause(1000) // Allow for native share dialog
            
            // Since we can't test native share dialog in browser,
            // we test that the share action was triggered
            ->script("return window.shareTriggered || false")
            ->with(function ($result): void {
                expect($result)->toBe(true);
            });
    });
});

it('provides mobile-optimized forms - MOBILE FORMS', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books/create')
            ->waitFor('.mobile-form')
            
            // Test mobile form layout
            ->assertVisible('.form-field-mobile')
            ->within('.mobile-form', function ($form): void {
                $form->assertSee('Title')
                    ->tap('input[name="title"]')
                    ->type('title', 'Mobile Test Book')
                    
                    // Test mobile-friendly input
                    ->assertAttribute('input[name="title"]', 'autocomplete', 'off')
                    ->assertAttribute('input[name="title"]', 'autocapitalize', 'words')
                    
                    ->tap('input[name="author"]')
                    ->type('author', 'Mobile Author')
                    
                    ->tap('select[name="genre_id"]')
                    ->select('genre_id', $this->genre->id)
                    
                    ->tap('[data-mobile-submit]');
            })
            ->waitForText('created successfully')
            ->assertSee('created successfully');
    });
});

// ========================
// CRITICAL ERROR SCENARIOS
// ========================

it('handles JavaScript errors gracefully on mobile - ERROR HANDLING', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books')
            
            // Inject a JavaScript error
            ->script("throw new Error('Test mobile error');")
            
            // App should still be functional
            ->pause(2000)
            ->assertVisible('.book-card')
            ->tap('.book-card:first-child')
            ->waitFor('.book-details-mobile')
            ->assertVisible('.book-details-mobile');
    });
});

it('maintains state during mobile app backgrounding - STATE MANAGEMENT', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667)
            ->loginAs($this->user)
            ->visit('/books')
            ->type('search', 'test search')
            
            // Simulate app going to background
            ->script("
                document.dispatchEvent(new Event('visibilitychange'));
                document.hidden = true;
            ")
            ->pause(1000)
            
            // Simulate app coming back to foreground
            ->script("
                document.dispatchEvent(new Event('visibilitychange'));
                document.hidden = false;
            ")
            ->pause(1000)
            
            // Search state should be maintained
            ->assertInputValue('search', 'test search');
    });
});