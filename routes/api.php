<?php

use App\Http\Controllers\Api\BookController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RateLimiter::for('api', function ($request) {
////    return $request->user()->isPremium()
////        ? Limit::perMinute(120)->by($request->user()->id)
////        : Limit::perMinute(60)->by($request->user()->id);
//});


Route::middleware('auth:api')->prefix('v1')->group(function () {
    // Protected routes (require authentication)
    Route::apiResource('books', BookController::class)->except(['index', 'show']);

    // Public routes (no authentication required)
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::get('/books/export', [BookController::class, 'export']);
    Route::get('/books/fetch-from-google', [BookController::class, 'fetchFromGoogleBooks']);
    Route::get('/books/{book}/recommend', [BookController::class, 'recommend']);
    Route::get('/health', [BookController::class, 'health']);
});
