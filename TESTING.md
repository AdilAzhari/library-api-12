# Testing Documentation

## 🧪 Testing Strategy

This project uses **Pest v4** with comprehensive testing coverage including unit tests, feature tests, and browser tests.

## 📁 Test Structure

```
tests/
├── Browser/                 # Browser tests (Pest v4 + Playwright)
│   ├── BookManagementTest.php
│   ├── BorrowingWorkflowTest.php
│   ├── UserAuthenticationTest.php
│   ├── UserDashboardTest.php
│   └── ReservationWorkflowTest.php
├── Feature/                 # Feature tests
├── Unit/                   # Unit tests
├── Pest.php               # Pest configuration
└── TestCase.php          # Base test case
```

## 🚀 Setup Browser Testing

### 1. Install Dependencies

```bash
# Install Pest browser testing plugin
composer require pestphp/pest-plugin-browser --dev

# Install Playwright
npm install playwright@latest

# Install browser binaries
npx playwright install
```

### 2. Environment Configuration

Add to your `.env.testing`:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## 🏃 Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Types
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only  
php artisan test --testsuite=Feature

# Browser tests only
php artisan test --testsuite=Browser
```

### Run Individual Test Files
```bash
# Specific test file
php artisan test tests/Browser/BookManagementTest.php

# Specific test method
php artisan test --filter "allows users to browse books"
```

## 🔍 Browser Test Coverage

### 📚 Book Management (`BookManagementTest.php`)
- ✅ Browse books catalog
- ✅ Search functionality
- ✅ View book details
- ✅ Filter by genre/status
- ✅ Pagination
- ✅ Mobile responsiveness
- ✅ Dark mode support
- ✅ Reserve/borrow books

### 🏠 User Dashboard (`UserDashboardTest.php`)
- ✅ Dashboard access and display
- ✅ Statistics cards
- ✅ Overdue warnings
- ✅ Active reservations
- ✅ Outstanding fines
- ✅ Navigation to other sections
- ✅ Recent activity feed
- ✅ Recommendations

### 📖 Borrowing Workflow (`BorrowingWorkflowTest.php`)
- ✅ Complete borrow process
- ✅ Borrowing history display
- ✅ Status filtering (active, overdue, history)
- ✅ Book renewal
- ✅ Return workflow
- ✅ Overdue notifications
- ✅ Due soon warnings
- ✅ Statistics display

### 🔖 Reservation System (`ReservationWorkflowTest.php`)
- ✅ Reserve available books
- ✅ Reserve borrowed books
- ✅ View reservations list
- ✅ Filter reservations
- ✅ Expiration warnings
- ✅ Ready for pickup notifications
- ✅ Cancel reservations
- ✅ Queue management

### 🔐 User Authentication (`UserAuthenticationTest.php`)
- ✅ User registration
- ✅ User login/logout
- ✅ Form validation
- ✅ Password reset
- ✅ Email verification
- ✅ Session management
- ✅ Route protection
- ✅ Remember me functionality

## 🛠️ Advanced Testing Features

### Device Testing
```php
it('works on mobile devices', function () {
    visit('/books')
        ->on()->mobile()
        ->assertSee('Book Title');
});
```

### Dark Mode Testing
```php
it('supports dark mode', function () {
    visit('/books')
        ->inDarkMode()
        ->assertNoJavascriptErrors();
});
```

### Cross-browser Testing
Playwright supports multiple browsers automatically:
- Chromium
- Firefox  
- WebKit (Safari)

### Screenshots and Debugging
```php
it('can take screenshots', function () {
    visit('/books')
        ->screenshot('books-page')
        ->assertSee('Books');
});
```

## 📊 Test Data Management

### Database Transactions
All tests use database transactions for cleanup:

```php
beforeEach(function () {
    $this->user = User::factory()->create();
    // Test data setup
});
```

### Factory Usage
```php
$book = Book::factory()->create([
    'status' => 'available',
    'genre_id' => $genre->id
]);
```

## 🐛 Debugging Tests

### Verbose Output
```bash
php artisan test --verbose
```

### Debug Specific Test
```bash
php artisan test --filter "test name" --stop-on-failure
```

### Browser Debugging
```php
it('debugs browser interaction', function () {
    visit('/books')
        ->pause() // Pauses execution for manual inspection
        ->assertSee('Books');
});
```

## 🚨 Common Issues & Solutions

### Playwright Installation Issues
```bash
# Force reinstall browsers
npx playwright install --force

# Install system dependencies (Linux)
npx playwright install-deps
```

### Database Issues
```bash
# Refresh test database
php artisan migrate:fresh --env=testing
```

### Port Conflicts
Update `APP_URL` in `.env.testing` if port 8000 is busy.

## 📈 Coverage Reports

Generate coverage reports:
```bash
php artisan test --coverage --min=80
```

## 🎯 Testing Best Practices

1. **Test User Flows** - Focus on complete user journeys
2. **Test Edge Cases** - Empty states, validation errors
3. **Mobile First** - Test responsive design
4. **Accessibility** - Ensure keyboard navigation works
5. **Performance** - Test loading states and timeouts
6. **Cross-browser** - Verify compatibility
7. **Data Cleanup** - Use transactions for isolation

## 📚 Pest v4 Browser Testing Resources

- [Pest Browser Testing Docs](https://pestphp.com/docs/pest-v4-is-here-now-with-browser-testing)
- [Playwright Documentation](https://playwright.dev/)
- [Laravel Testing Guide](https://laravel.com/docs/testing)

---

With this comprehensive testing suite, the Library Management System is thoroughly tested and ready for production deployment! 🚀