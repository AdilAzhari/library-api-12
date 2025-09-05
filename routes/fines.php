<?php

declare(strict_types=1);

use App\Http\Controllers\FineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Overdue Management & Fines Flow Routes
|--------------------------------------------------------------------------
| Routes related to managing overdue books, fines, and payment processing.
*/

Route::prefix('fines')->name('fines.')->group(function (): void {
    // Fine management dashboard
    Route::get('/', [FineController::class, 'index'])->name('index');

    // Individual fine management (ownership required)
    Route::middleware(['owns:fine'])->group(function (): void {
        Route::get('/{fine}', [FineController::class, 'show'])->name('show');

        // Payment workflow
        Route::get('/{fine}/pay', [FineController::class, 'showPayment'])->name('payment.show');
        Route::post('/{fine}/pay', [FineController::class, 'processPayment'])->name('payment.process');

        // Payment history and receipts
        Route::get('/{fine}/receipt', [FineController::class, 'downloadReceipt'])->name('receipt');
        Route::get('/{fine}/payment-history', [FineController::class, 'paymentHistory'])->name('payment-history');
    });

    // Bulk fine actions (for users with multiple fines)
    Route::post('/pay-all', [FineController::class, 'payAllFines'])->name('pay-all');
    Route::get('/payment-summary', [FineController::class, 'paymentSummary'])->name('payment-summary');
});
