<?php

use App\Http\Controllers\Front\BookController;
use App\Http\Controllers\Front\BorrowController;
use App\Http\Controllers\Front\ReservationController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['throttle:60,1', 'auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('about', function () {
        return Inertia::render('AboutPage');
    });

    Route::get('terms', function () {
        return Inertia::render('TermsPage');
    });

    Route::get('privacyPolicy', function () {
        return Inertia::render('PrivacyPolicy');
    });

    Route::get('contact', function () {
        return Inertia::render('ContactPage');
    });

    Route::get('careers', function () {
        return Inertia::render('Careers');
    });

    Route::get('blog', function () {
        return Inertia::render('Blog');
    });

    Route::get('sitemap', function () {
        return Inertia::render('Sitemap');
    });

    Route::get('cookies', function () {
        return Inertia::render('CookiePolicy');
    });

    // Books

    Route::resources([
        'books' => BookController::class,
        'reservations' => ReservationController::class,
        'borrows' => BorrowController::class,
    ]);

    Route::resource('reviews', ReviewController::class)->except(['index', 'show']);

    route::controller(BookController::class)
        ->prefix('books')
        ->group(function () {
            Route::post('/{book}/borrow', 'borrow');
            Route::post('/{book}/reviews', 'review');
            Route::post('/books/drafts', 'draft');
            Route::post('/{book}/reserve', 'reserve');
        });

    // Reservations

    Route::controller(ReservationController::class)
        ->prefix('reservations')
        ->group(function () {
            Route::post('/{reservation}/fulfill', 'fulfill')->name('reservations.fulfill');
            Route::post('/{reservation}/cancel', 'cancel')->name('reservations.cancel');
        });

    // Borrows
    Route::controller(BorrowController::class)
        ->prefix('borrows')
        ->group(function () {
            Route::post('/{borrow}/return', 'return')->name('borrows.return');
        });
});
require __DIR__.'/auth.php';
require __DIR__.'/Admin.php';
