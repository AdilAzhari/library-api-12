<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminBorrowingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Middleware\AdminMiddleware;

Route::prefix('admin')->as('admin.')
    ->middleware(['auth', 'verified', AdminMiddleware::class])->group(function (): void {

        Route::controller(ProfileController::class)
            ->prefix('profile')
            ->as('profile.')
            ->group(function (): void {
                Route::get('/edit', 'edit')->name('.edit');
                Route::put('/', 'update')->name('.update');
                Route::delete('/', 'destroy')->name('.destroy');
            });

        Route::resource('/borrowings', AdminBorrowingController::class)->except(['edit', 'update']);
        Route::controller(AdminBorrowingController::class)
            ->prefix('borrowings/{borrowing}')
            ->as('borrowings.')
            ->group(function (): void {
                Route::put('/return', 'markReturned')->name('return');
                Route::put('/renew', 'renew')->name('renew');
            });

        Route::controller(AdminReservationController::class)
            ->as('reservations.')
            ->prefix('reservations')
            ->group(function (): void {
                Route::get('/', 'index')->name('index');
                Route::post('/{reservation}/fulfill', 'fulfill')->name('fulfill');
                Route::post('/{reservation}/cancel', 'cancel')->name('cancel');
            });
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Books
        Route::resource('/books', AdminBookController::class)->except(['show']);

        // Borrowings

        // Users
        Route::resource('/users', AdminUserController::class);

        // Reports
        Route::controller(AdminReportController::class)
            ->as('reports.')
            ->prefix('reports')
            ->group(function (): void {
                Route::get('/borrowings', 'borrowings')->name('borrowings');
                Route::get('/overdue', 'overdue')->name('overdue');
                Route::get('/borrowings/export/csv', 'exportBorrowingsCSV')->name('borrowings.export.csv');
                Route::get('/borrowings/export/pdf', 'exportBorrowingsPDF')->name('borrowings.export.pdf');
            });
    });
