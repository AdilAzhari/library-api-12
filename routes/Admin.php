<?php

use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminBorrowingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReservationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ProfileController;

Route::prefix('admin')->as('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->as('profile.')
        ->group(function () {
            Route::get('/edit', 'edit')->name('.edit');
            Route::put('/', 'update')->name('.update');
            Route::delete('/', 'destroy')->name('.destroy');
        });

    Route::resource('/borrowings', AdminBorrowingController::class)->except(['edit', 'update']);
    Route::controller(AdminBorrowingController::class)->as('admin.borrowings')
        ->prefix('borrowings/{borrowing}')
        ->group(function () {
            Route::put('/return', 'return')->name('.return');
            Route::put('/renew', 'renew')->name('.renew');
            Route::put('/return', 'markReturned')->name('.return');
            Route::put('/renew', 'renew')->name('.renew');
        });

    Route::controller(AdminReservationController::class)
        ->as('admin.reservations')
        ->prefix('reservations')
        ->group(function () {
            Route::put('/{reservation}/fulfill', 'fulfill')->name('.fulfill');
            Route::put('/{reservation}/cancel', 'cancel')->name('.cancel');
            Route::get('/', 'index')->name('.index');
        });
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Books
    Route::resource('/books', AdminBookController::class)->except(['show']);


    // Borrowings

    // Users
    Route::resource('/users', AdminUserController::class);

    // Reports
    Route::controller(AdminReportController::class)
        ->as('admin.reports')
        ->prefix('reports')
        ->group(function () {
            Route::get('/borrowings', 'borrowings')->name('.borrowings');
            Route::get('/overdue', 'overdue')->name('.overdue');
            Route::get('/borrowings/export/csv', 'exportBorrowingsCSV')->name('.borrowings.export.csv');
            Route::get('/borrowings/export/pdf', 'exportBorrowingsPDF')->name('.borrowings.export.pdf');
        });
});
