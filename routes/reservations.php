<?php

declare(strict_types=1);

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reservation Flow Routes
|--------------------------------------------------------------------------
| Routes related to the book reservation workflow including queue
| management, pickup process, and reservation lifecycle.
*/

Route::prefix('reservations')->name('reservations.')->group(function (): void {
    // Reservation management dashboard
    Route::get('/', [ReservationController::class, 'index'])->name('index');

    // Reservation creation (must check overdue status) - More specific routes FIRST
    Route::middleware('overdue.check')->group(function (): void {
        Route::get('/books/{book}/reserve', [ReservationController::class, 'create'])
            ->name('create');
        Route::post('/books/{book}/reserve', [ReservationController::class, 'store'])
            ->name('store');
    });

    // Individual reservation management (ownership required) - Generic routes LAST
    Route::middleware('owns:reservation')->group(function (): void {
        // Pickup process
        Route::post('/{reservation}/pickup', [ReservationController::class, 'confirmPickup'])
            ->name('pickup');

        // Reservation modifications
        Route::post('/{reservation}/extend', [ReservationController::class, 'extend'])
            ->name('extend');
        Route::delete('/{reservation}', [ReservationController::class, 'cancel'])
            ->name('cancel');

        // Update notification preferences
        Route::patch('/{reservation}/notifications', [ReservationController::class, 'updateNotifications'])
            ->name('notifications.update');

        // Show route - MUST be last because it's most generic
        Route::get('/{reservation}', [ReservationController::class, 'show'])
            ->name('show');
    });
});
