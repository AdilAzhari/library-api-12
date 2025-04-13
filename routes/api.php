<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.')
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->middleware('auth:sanctum');

        Route::resources([
            'books' => BookController::class,
        ]);
        // Borrowing Routes
        Route::controller(BorrowController::class)->group(function () {
            Route::post('/borrow', 'borrowBook')->middleware('auth:sanctum')->name('borrow.book');
            Route::post('/return', 'returnBook')->middleware('auth:sanctum')->name('return.book');
            Route::get('/borrowings', 'listBorrowedBooks')->middleware('auth:sanctum')->name('list.borrowings');
        });

        // Reservation Routes
        Route::controller(ReservationController::class)->group(function () {
            Route::post('/reserve', 'reserveBook')->middleware('auth:sanctum')->name('reserve.book');
            Route::get('/reservations', 'listReservations')->middleware('auth:sanctum')->name('list.reservations');
        });
        // Review Routes
        Route::post('/reviews', [ReviewController::class, 'reviewBook'])->middleware('auth:sanctum')->name('review.book');
        Route::get('/books/{book}/reviews', [ReviewController::class, 'listReviews'])->name('list.reviews');
        // Route::middleware(['auth:sanctum'])->group(function () {

        // Public routes (no authentication required)
        Route::controller(BookController::class)->group(function () {
            Route::get('/books/export', 'export');
            Route::get('/books/fetch-from-google', 'fetchFromGoogleBooks');
            Route::get('/books/{book}/recommend', 'recommend');
            Route::get('/health', 'health');
            Route::post('/books/{book}/restore', 'restore');
            Route::get('/books/{book}/favorite', 'favorite');
        });

    });
