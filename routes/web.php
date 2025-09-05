<?php

declare(strict_types=1);

use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\NewReleasesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Landing Page
Route::get('/', fn () => Inertia::render('Welcome', [
    'canLogin' => Route::has('login'),
    'canRegister' => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion' => PHP_VERSION,
]))->name('home');

// Public book catalog
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Public genre browsing
Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

// Public new releases
Route::get('/new-releases', [NewReleasesController::class, 'index'])->name('new-releases.index');

// Public recommendations
Route::get('/recommendations', [RecommendationsController::class, 'index'])->name('recommendations.index');

// Authenticated book actions
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('/books/{book}/borrow', [BookController::class, 'borrow'])->name('books.borrow');
    Route::post('/books/{book}/reserve', [BookController::class, 'reserve'])->name('books.reserve');
    Route::post('/books/{book}/reviews', [BookController::class, 'review'])->name('books.reviews.store');
});

require __DIR__.'/auth.php';

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    // Dashboard & Profile (Central hub with overdue checking)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['overdue.check'])
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Flow Based Route Organization
    require __DIR__.'/borrowing.php';      // Borrowing workflow routes
    require __DIR__.'/reservations.php';   // Reservation workflow routes
    require __DIR__.'/fines.php';          // Overdue/fines workflow routes

    // Static Pages
    Route::get('/about', fn () => Inertia::render('AboutPage'))->name('about');
    Route::get('/terms', fn () => Inertia::render('TermsPage'))->name('terms');
    Route::get('/privacy', fn () => Inertia::render('PrivacyPolicy'))->name('privacy');
    Route::get('/contact', fn () => Inertia::render('ContactPage'))->name('contact');
});

require __DIR__.'/Admin.php';
Route::get('hello', function () {
    return Auth::check() ? Auth::user()->role->value : false;
});
