<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FineController;
use App\Http\Controllers\Api\ReadingListController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for User Flows
|--------------------------------------------------------------------------
| API endpoints that support the main user flreows with real-time data,
| search functionality, and AJAX interactions.
*/

// Authentication routes (no middleware required) - direct routes for testing
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

// Authentication routes (no middleware required)
Route::prefix('auth')->name('api.auth.')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {

    // User info endpoint
    Route::get('/user', fn (Request $request) => $request->user());

    // Dashboard data aggregation
    Route::prefix('dashboard')->name('api.dashboard.')->group(function (): void {
        Route::get('/data', [DashboardController::class, 'dashboardData'])->name('data');
        Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
        Route::get('/quick-stats', [DashboardController::class, 'quickStats'])->name('quick-stats');
    });

    // Book discovery and search
    Route::prefix('books')->name('api.books.')->group(function (): void {
        Route::get('/search', [BookController::class, 'search'])->name('search');
        Route::get('/autocomplete', [BookController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/recommendations', [BookController::class, 'recommendations'])->name('recommendations');
        Route::get('/{book}/availability', [BookController::class, 'availability'])->name('availability');
        Route::get('/{book}/queue-info', [BookController::class, 'queueInfo'])->name('queue-info');

        // Related books for book detail pages
        Route::get('/{book}/related', [BookController::class, 'related'])->name('related');
        Route::get('/trending', [BookController::class, 'trending'])->name('trending');
        Route::get('/new-arrivals', [BookController::class, 'newArrivals'])->name('new-arrivals');
    });

    // Borrowing flow API endpoints
    Route::prefix('borrows')->name('api.borrows.')->group(function (): void {
        Route::get('/active', [BorrowController::class, 'active'])->name('active');
        Route::get('/overdue', [BorrowController::class, 'overdue'])->name('overdue');
        Route::get('/due-soon', [BorrowController::class, 'dueSoon'])->name('due-soon');
        Route::get('/history', [BorrowController::class, 'history'])->name('history');

        // Quick actions
        Route::post('/{borrow}/renew', [BorrowController::class, 'renew'])
            ->middleware(['owns:borrow'])
            ->name('renew');

        // Renewal eligibility check
        Route::get('/{borrow}/can-renew', [BorrowController::class, 'canRenew'])
            ->middleware(['owns:borrow'])
            ->name('can-renew');
    });

    // Reservation flow API endpoints
    Route::prefix('reservations')->name('api.reservations.')->group(function (): void {
        Route::get('/active', [ReservationController::class, 'active'])->name('active');
        Route::get('/ready', [ReservationController::class, 'ready'])->name('ready');
        Route::get('/history', [ReservationController::class, 'history'])->name('history');

        // Queue information
        Route::get('/queue/{book}', [ReservationController::class, 'queueInfo'])->name('queue-info');
        Route::get('/{reservation}/position', [ReservationController::class, 'queuePosition'])
            ->middleware(['owns:reservation'])
            ->name('queue-position');

        // Quick actions
        Route::post('/{reservation}/extend', [ReservationController::class, 'extend'])
            ->middleware(['owns:reservation'])
            ->name('extend');
        Route::post('/{reservation}/pickup', [ReservationController::class, 'confirmPickup'])
            ->middleware(['owns:reservation'])
            ->name('pickup');
    });

    // Reading Lists API (for modals and quick access)
    Route::prefix('reading-lists')->name('api.reading-lists.')->group(function (): void {
        Route::get('/user', [ReadingListController::class, 'userLists'])->name('user');
        Route::get('/public', [ReadingListController::class, 'publicLists'])->name('public');
        Route::get('/featured', [ReadingListController::class, 'featuredLists'])->name('featured');

        // Quick book addition (used in modals)
        Route::post('/{readingList}/books/{book}', [ReadingListController::class, 'addBook'])
            ->middleware(['owns:readingList'])
            ->name('add-book');
    });

    // Fines and payment API endpoints
    Route::prefix('fines')->name('api.fines.')->group(function (): void {
        Route::get('/summary', [FineController::class, 'summary'])->name('summary');
        Route::get('/outstanding', [FineController::class, 'outstanding'])->name('outstanding');
        Route::get('/payment-methods', [FineController::class, 'paymentMethods'])->name('payment-methods');

        // Payment processing status
        Route::get('/{fine}/payment-status', [FineController::class, 'paymentStatus'])
            ->middleware(['owns:fine'])
            ->name('payment-status');
    });

    // Global search across all content types
    Route::prefix('search')->name('api.search.')->group(function (): void {
        Route::get('/global', [SearchController::class, 'global'])->name('global');
        Route::get('/books', [SearchController::class, 'books'])->name('books');
        Route::get('/reading-lists', [SearchController::class, 'readingLists'])->name('reading-lists');
        Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('suggestions');
    });
});

// Public API endpoints (no authentication required)
Route::prefix('public')->name('api.public.')->group(function (): void {
    Route::get('/library-hours', fn () => response()->json([
        'hours' => [
            'monday' => '8:00 AM - 8:00 PM',
            'tuesday' => '8:00 AM - 8:00 PM',
            'wednesday' => '8:00 AM - 8:00 PM',
            'thursday' => '8:00 AM - 8:00 PM',
            'friday' => '8:00 AM - 6:00 PM',
            'saturday' => '9:00 AM - 5:00 PM',
            'sunday' => '1:00 PM - 5:00 PM',
        ],
    ]))->name('library-hours');

    Route::get('/locations', fn () => response()->json([
        'locations' => [
            'Main Library',
            'Science Library',
            'Engineering Library',
            'Medical Library',
            'Student Center Desk',
        ],
    ]))->name('locations');

    // Legacy routes for backward compatibility
    Route::prefix('v1')->name('v1.')->group(function (): void {
        // Books CRUD routes
        Route::apiResource('books', BookController::class)->middleware('auth:sanctum');

        Route::post('/borrow', [BorrowController::class, 'borrowBook'])->middleware('auth:sanctum')->name('borrow.book');
        Route::post('/return', [BorrowController::class, 'returnBook'])->middleware('auth:sanctum')->name('return.book');
        Route::get('/borrowings', [BorrowController::class, 'listBorrowedBooks'])->middleware('auth:sanctum')->name('list.borrowings');
        Route::post('/reserve', [ReservationController::class, 'reserveBook'])->middleware('auth:sanctum')->name('reserve.book');
        Route::get('/reservations', [ReservationController::class, 'listReservations'])->middleware('auth:sanctum')->name('list.reservations');
        Route::post('/reviews', [ReviewController::class, 'reviewBook'])->middleware('auth:sanctum')->name('review.book');
        Route::get('/books/{book}/reviews', [ReviewController::class, 'listReviews'])->name('list.reviews');
    });
});
