# Browser Tests (Laravel Dusk)

This directory contains comprehensive browser tests using Laravel Dusk for testing critical user flows, mobile responsiveness, and admin functionality.

## Test Coverage

### 🔄 CriticalUserFlowsTest.php
- **Authentication flows** (login, registration, validation)
- **Book browsing & search** (catalog, filters, details)
- **Borrowing workflows** (borrow, renew, return)
- **Reservation system** (reserve, cancel, queue management)
- **Dashboard functionality** (user dashboard, statistics)
- **Navigation & UX** (seamless navigation, pagination)
- **Admin functionality** (admin dashboard, book management)
- **Error handling** (network errors, loading states, session management)

### 📱 MobileResponsiveTest.php
- **Mobile device testing** (iPhone SE 320px, iPhone 12/13 Pro 390px, Samsung Galaxy S20 360px)
- **Tablet testing** (iPad 768px, iPad Pro landscape 1024px)
- **Touch interactions** (swipe gestures, pull-to-refresh, pinch-to-zoom)
- **Orientation changes** (portrait to landscape adaptation)
- **Performance** (slow connections, offline state)
- **Mobile-specific features** (sharing, optimized forms)
- **Critical error scenarios** (JavaScript errors, state management)

### 👨‍💼 AdminCriticalFlowsTest.php
- **Admin authentication & access control**
- **Book management workflow** (CRUD operations, bulk actions)
- **User management** (role changes, suspend/reactivate)
- **Borrowing management** (process returns, handle overdue)
- **Reports and analytics** (generate reports, real-time dashboard)
- **System configuration** (library settings)
- **Mobile admin interface**
- **Critical error handling** (error recovery, session management)

## Running Browser Tests

### Prerequisites

1. **Install Laravel Dusk:**
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

2. **Install ChromeDriver:**
```bash
php artisan dusk:chrome-driver
```

3. **Start the application:**
```bash
php artisan serve
```

### Running Tests

```bash
# Run all browser tests
php artisan dusk

# Run specific test file
php artisan dusk tests/Browser/CriticalUserFlowsTest.php

# Run tests with specific browser size
DUSK_HEADLESS_DISABLED=1 php artisan dusk

# Run tests in headless mode (default)
php artisan dusk

# Run tests with screenshots on failure
php artisan dusk --with-screenshots
```

### Environment Configuration

The tests use `.env.dusk.local` for configuration:

- **Database**: SQLite in-memory for fast testing
- **Queue**: Sync driver for immediate processing
- **Mail**: Array driver for testing emails
- **Cache**: Array driver for testing
- **Session**: Array driver for testing

## Test Structure

### Desktop Tests (1920x1080)
- Full feature testing with complete UI
- Complex workflows and multi-step processes
- Admin functionality and bulk operations

### Mobile Tests
- **iPhone SE (320x568)**: Smallest common screen size
- **iPhone 12/13 Pro (390x844)**: Modern iPhone size
- **Samsung Galaxy S20 (360x640)**: Popular Android size

### Tablet Tests
- **iPad (768x1024)**: Standard tablet size
- **iPad Pro Landscape (1024x768)**: Large tablet/desktop-like

## Browser Test Patterns

### Authentication Flow
```php
$browser->visit('/login')
    ->type('email', 'user@example.com')
    ->type('password', 'password')
    ->press('Sign In')
    ->waitForLocation('/dashboard')
    ->assertSee('Welcome');
```

### Mobile Navigation
```php
$browser->resize(375, 667) // Mobile size
    ->tap('button[data-mobile-menu]')
    ->waitFor('.mobile-menu')
    ->within('.mobile-menu', function ($menu) {
        $menu->clickLink('Books');
    });
```

### Form Interactions
```php
$browser->type('title', 'Book Title')
    ->select('genre_id', $this->genre->id)
    ->attach('cover_image', $imagePath)
    ->press('Create Book')
    ->waitForText('created successfully');
```

### Responsive Testing
```php
$browser->resize(320, 568) // Small mobile
    ->assertVisible('.mobile-layout')
    ->resize(768, 1024) // Tablet
    ->assertVisible('.tablet-layout');
```

## Critical Test Scenarios

### 🔒 Security Tests
- Unauthorized access prevention
- Role-based access control
- Session management
- CSRF protection

### 📱 Mobile UX Tests
- Touch target sizes (minimum 44px)
- Swipe gestures and interactions
- Orientation change handling
- Mobile form optimization

### ⚡ Performance Tests
- Page load times
- Loading states
- Slow network simulation
- Offline/online state handling

### 🛡️ Error Handling Tests
- Network errors
- JavaScript errors
- Session timeouts
- Form validation errors

## Debugging Browser Tests

### Screenshots
Failed tests automatically capture screenshots in `tests/Browser/screenshots/`

### Console Output
View browser console logs:
```php
$browser->script('console.log("Debug message")');
```

### Pause Execution
Debug interactively:
```php
$browser->pause(); // Pauses test execution
```

### Custom Assertions
```php
$browser->assertVisible('.loading-spinner')
    ->waitForText('Content loaded', 10)
    ->assertSee('Expected content');
```

## Maintenance

### Updating ChromeDriver
```bash
php artisan dusk:chrome-driver --detect
```

### Clearing Test Data
Tests use database migrations and factory data that's automatically cleaned up.

### Adding New Tests

1. Create test file in appropriate category
2. Follow existing patterns for setup and teardown
3. Test both desktop and mobile versions
4. Include error handling scenarios
5. Add meaningful assertions

## Performance Considerations

- Tests run in headless mode by default for speed
- Use in-memory SQLite database
- Parallel test execution where possible
- Screenshot capture only on failures
- Minimal wait times with smart waiting strategies