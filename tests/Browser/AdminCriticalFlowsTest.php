<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'name' => 'Admin User',
        'email' => 'admin@library.com',
        'password' => bcrypt('admin123'),
    ]);
    
    $this->librarian = User::factory()->create([
        'role' => 'librarian',
        'name' => 'Librarian User',
        'email' => 'librarian@library.com',
    ]);
    
    $this->member = User::factory()->create([
        'role' => 'member',
        'name' => 'Regular Member',
        'email' => 'member@library.com',
    ]);
    
    $this->genre = Genre::factory()->create(['name' => 'Admin Test Genre']);
    
    $this->book = Book::factory()->create([
        'title' => 'Admin Test Book',
        'author' => 'Admin Test Author',
        'isbn' => '9781111111111',
        'genre_id' => $this->genre->id,
        'status' => 'available',
        'total_copies' => 5,
        'copies_available' => 5,
    ]);
});

// ========================
// ADMIN AUTHENTICATION & ACCESS
// ========================

it('allows admin login and redirects to admin dashboard - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->visit('/login')
            ->type('email', 'admin@library.com')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/admin/dashboard')
            ->assertPathIs('/admin/dashboard')
            ->assertSee('Admin Dashboard')
            ->assertSee('Welcome, Admin User')
            ->assertSee('Total Books')
            ->assertSee('Active Borrows')
            ->assertSee('Total Users');
    });
});

it('prevents unauthorized access to admin areas - SECURITY', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->member) // Regular member
            ->visit('/admin/dashboard')
            ->assertPathIs('/') // Redirected to home
            ->assertSee('Access denied') // Or appropriate error message
            
            ->visit('/admin/books')
            ->assertPathIs('/') // Redirected to home
            
            ->visit('/admin/users')
            ->assertPathIs('/'); // Redirected to home
    });
});

it('allows librarian access to appropriate admin sections - LIBRARIAN ACCESS', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->librarian)
            ->visit('/admin/books')
            ->assertSee('Manage Books') // Librarian can manage books
            ->assertSee($this->book->title)
            
            ->visit('/admin/borrows')
            ->assertSee('Manage Borrows') // Librarian can manage borrows
            
            // But cannot access user management
            ->visit('/admin/users')
            ->assertPathIs('/admin/books') // Redirected back
            ->assertSee('Insufficient permissions');
    });
});

// ========================
// BOOK MANAGEMENT WORKFLOW
// ========================

it('completes full book creation workflow - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/books')
            ->assertSee('Manage Books')
            ->clickLink('Add New Book')
            ->waitForLocation('/admin/books/create')
            ->assertSee('Create New Book')
            
            // Fill in book details
            ->type('title', 'New Admin Book')
            ->type('author', 'New Admin Author')
            ->type('isbn', '9782222222222')
            ->select('genre_id', $this->genre->id)
            ->type('description', 'This is a test book created by admin')
            ->type('publication_year', '2024')
            ->type('total_copies', '3')
            ->type('copies_available', '3')
            ->select('status', 'available')
            
            // Upload cover image (if file upload is implemented)
            ->attach('cover_image', __DIR__ . '/../fixtures/test-book-cover.jpg')
            
            ->press('Create Book')
            ->waitForText('Book created successfully')
            ->assertSee('Book created successfully')
            ->assertPathIs('/admin/books')
            ->assertSee('New Admin Book')
            ->assertSee('New Admin Author');
    });
});

it('handles book editing workflow - DESKTOP', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/books')
            ->assertSee($this->book->title)
            
            // Find and edit the book
            ->within("tr:contains('{$this->book->title}')", function ($row): void {
                $row->clickLink('Edit');
            })
            ->waitForLocation('/admin/books/' . $this->book->id . '/edit')
            ->assertSee('Edit Book')
            ->assertInputValue('title', $this->book->title)
            
            // Update book details
            ->clear('title')
            ->type('title', 'Updated Admin Book Title')
            ->clear('description')
            ->type('description', 'Updated description for admin book')
            ->type('total_copies', '10')
            ->type('copies_available', '8')
            
            ->press('Update Book')
            ->waitForText('Book updated successfully')
            ->assertSee('Book updated successfully')
            ->assertSee('Updated Admin Book Title');
    });
});

it('handles bulk book operations - BULK OPERATIONS', function (): void {
    // Create multiple books for bulk operations
    $books = Book::factory()->count(5)->create(['genre_id' => $this->genre->id]);
    
    $this->browse(function (Browser $browser) use ($books): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/books')
            ->assertSee('Bulk Actions')
            
            // Select multiple books
            ->check('book_ids[]', $books[0]->id)
            ->check('book_ids[]', $books[1]->id)
            ->check('book_ids[]', $books[2]->id)
            
            ->select('bulk_action', 'update_status')
            ->select('new_status', 'maintenance')
            ->press('Apply Bulk Action')
            ->waitForText('3 books updated successfully')
            ->assertSee('3 books updated successfully');
    });
});

// ========================
// USER MANAGEMENT WORKFLOW
// ========================

it('manages users effectively - USER MANAGEMENT', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/users')
            ->assertSee('Manage Users')
            ->assertSee($this->member->name)
            ->assertSee($this->librarian->name)
            
            // Create new user
            ->clickLink('Add New User')
            ->waitForLocation('/admin/users/create')
            ->type('name', 'New Library User')
            ->type('email', 'newuser@library.com')
            ->type('password', 'newuser123')
            ->type('password_confirmation', 'newuser123')
            ->select('role', 'member')
            ->press('Create User')
            ->waitForText('User created successfully')
            ->assertSee('User created successfully')
            ->assertSee('New Library User');
    });
});

it('handles user role changes - ROLE MANAGEMENT', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/users')
            ->assertSee($this->member->name)
            
            // Change member to librarian
            ->within("tr:contains('{$this->member->name}')", function ($row): void {
                $row->select('role', 'librarian')
                    ->press('Update Role');
            })
            ->waitForText('User role updated')
            ->assertSee('User role updated')
            
            // Verify the role was changed
            ->within("tr:contains('{$this->member->name}')", function ($row): void {
                $row->assertSelected('role', 'librarian');
            });
    });
});

it('suspends and reactivates users - USER STATUS', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/users')
            ->within("tr:contains('{$this->member->name}')", function ($row): void {
                $row->press('Suspend');
            })
            ->waitForText('User suspended')
            ->assertSee('User suspended')
            ->assertSee('Suspended')
            
            // Reactivate user
            ->within("tr:contains('{$this->member->name}')", function ($row): void {
                $row->press('Reactivate');
            })
            ->waitForText('User reactivated')
            ->assertSee('User reactivated')
            ->assertSee('Active');
    });
});

// ========================
// BORROWING MANAGEMENT
// ========================

it('manages borrowing operations - BORROW MANAGEMENT', function (): void {
    // Create some active borrows
    $borrow = Borrow::factory()->create([
        'user_id' => $this->member->id,
        'book_id' => $this->book->id,
        'borrowed_at' => now()->subDays(10),
        'due_date' => now()->addDays(4),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser) use ($borrow): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/borrows')
            ->assertSee('Manage Borrowings')
            ->assertSee($this->book->title)
            ->assertSee($this->member->name)
            
            // Process return
            ->within("tr:contains('{$this->book->title}')", function ($row): void {
                $row->press('Process Return');
            })
            ->waitForText('Book returned successfully')
            ->assertSee('Book returned successfully')
            ->assertSee('Returned');
    });
});

it('handles overdue books and fines - OVERDUE MANAGEMENT', function (): void {
    // Create overdue borrow
    $overdueBorrow = Borrow::factory()->create([
        'user_id' => $this->member->id,
        'book_id' => $this->book->id,
        'borrowed_at' => now()->subDays(20),
        'due_date' => now()->subDays(5),
        'status' => 'active',
    ]);
    
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/borrows')
            ->assertSee('Overdue')
            ->assertSee('5 days overdue')
            
            // Send overdue reminder
            ->within("tr:contains('Overdue')", function ($row): void {
                $row->press('Send Reminder');
            })
            ->waitForText('Reminder sent')
            ->assertSee('Reminder sent')
            
            // Calculate and apply fine
            ->within("tr:contains('Overdue')", function ($row): void {
                $row->press('Apply Fine');
            })
            ->waitForText('Fine applied')
            ->assertSee('Fine applied');
    });
});

// ========================
// REPORTS AND ANALYTICS
// ========================

it('generates and views reports - REPORTING', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/reports')
            ->assertSee('Library Reports')
            ->assertSee('Borrowing Statistics')
            ->assertSee('User Activity')
            ->assertSee('Popular Books')
            
            // Generate borrowing report
            ->select('report_type', 'borrowing_summary')
            ->select('date_range', 'last_month')
            ->press('Generate Report')
            ->waitForText('Report generated')
            ->assertSee('Report generated')
            ->assertSee('Total Borrows')
            ->assertSee('Active Borrows')
            ->assertSee('Overdue Items')
            
            // Export report
            ->press('Export to PDF')
            ->pause(2000) // Allow for PDF generation
            ->assertSee('Report exported');
    });
});

it('views real-time dashboard analytics - ANALYTICS', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/dashboard')
            ->assertSee('Library Statistics')
            ->assertVisible('.stats-card')
            ->assertSee('Total Books')
            ->assertSee('Active Borrows')
            ->assertSee('Total Users')
            ->assertSee('Overdue Items')
            
            // Check for charts
            ->assertVisible('.chart-container')
            ->assertVisible('[data-chart="borrowing-trends"]')
            ->assertVisible('[data-chart="popular-genres"]')
            
            // Test real-time updates
            ->pause(5000) // Wait for real-time update
            ->script("return document.querySelector('[data-realtime-counter]').textContent")
            ->with(function ($count): void {
                expect($count)->toBeNumeric();
            });
    });
});

// ========================
// SYSTEM CONFIGURATION
// ========================

it('manages library settings - SYSTEM CONFIG', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/settings')
            ->assertSee('Library Settings')
            ->assertSee('Borrowing Policies')
            ->assertSee('Fine Structure')
            
            // Update borrowing policies
            ->clear('max_loans_per_user')
            ->type('max_loans_per_user', '10')
            ->clear('loan_period_days')
            ->type('loan_period_days', '21')
            ->clear('max_renewals')
            ->type('max_renewals', '3')
            
            // Update fine structure
            ->clear('daily_late_fee')
            ->type('daily_late_fee', '1.00')
            ->clear('grace_period_days')
            ->type('grace_period_days', '2')
            
            ->press('Save Settings')
            ->waitForText('Settings updated successfully')
            ->assertSee('Settings updated successfully');
    });
});

// ========================
// MOBILE ADMIN INTERFACE
// ========================

it('provides functional mobile admin interface - MOBILE ADMIN', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->resize(375, 667) // Mobile size
            ->loginAs($this->admin)
            ->visit('/admin/dashboard')
            ->assertSee('Admin')
            ->assertVisible('.admin-mobile-nav')
            
            // Test mobile admin navigation
            ->tap('.admin-mobile-menu-button')
            ->waitFor('.admin-mobile-menu')
            ->within('.admin-mobile-menu', function ($menu): void {
                $menu->assertSee('Books')
                    ->assertSee('Users')
                    ->assertSee('Borrows')
                    ->assertSee('Reports')
                    ->tap('Books');
            })
            ->waitForLocation('/admin/books')
            ->assertPathIs('/admin/books')
            
            // Test mobile book management
            ->assertVisible('.mobile-book-card')
            ->tap('.mobile-book-card:first-child')
            ->waitFor('.mobile-book-details')
            ->assertVisible('.mobile-book-details')
            ->tap('[data-mobile-edit]')
            ->waitFor('.mobile-edit-form')
            ->within('.mobile-edit-form', function ($form): void {
                $form->clear('title')
                    ->type('title', 'Mobile Edited Title')
                    ->tap('[data-mobile-save]');
            })
            ->waitForText('updated successfully')
            ->assertSee('updated successfully');
    });
});

// ========================
// CRITICAL ERROR HANDLING
// ========================

it('handles critical admin errors gracefully - ERROR RECOVERY', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/books')
            
            // Simulate server error during bulk operation
            ->script("
                window.fetch = () => Promise.reject(new Error('Server error'));
            ")
            
            ->check('book_ids[]', $this->book->id)
            ->select('bulk_action', 'delete')
            ->press('Apply Bulk Action')
            ->waitForText('Operation failed')
            ->assertSee('Operation failed')
            ->assertSee('Please try again')
            
            // Verify data integrity
            ->refresh()
            ->assertSee($this->book->title); // Book should still exist
    });
});

it('maintains admin session during long operations - SESSION MANAGEMENT', function (): void {
    $this->browse(function (Browser $browser): void {
        $browser->loginAs($this->admin)
            ->visit('/admin/reports')
            
            // Start long-running report
            ->select('report_type', 'comprehensive_audit')
            ->press('Generate Report')
            ->waitFor('.long-operation-indicator')
            
            // Wait for operation to complete (simulate long wait)
            ->pause(30000) // 30 seconds
            
            // Session should still be active
            ->assertDontSee('Please log in')
            ->assertSee('Admin Dashboard')
            ->waitForText('Report completed')
            ->assertSee('Report completed');
    });
});