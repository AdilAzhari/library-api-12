<?php

declare(strict_types=1);

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\ReadingListController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Borrowing Flow Routes
|--------------------------------------------------------------------------
| Routes related to the book borrowing workflow including discovery,
| borrowing process, and borrow management.
*/

// Book Discovery Flow
Route::prefix('books')->name('books.')->group(function (): void {
    Route::get('/', [BookController::class, 'index'])->name('index');

    Route::get('/{book}', [BookController::class, 'show'])
        ->middleware(['overdue.check'])
        ->name('show');

    // Book reviews (integrated with borrowing flow)
    Route::post('/{book}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');
});

// Borrowing Workflow
Route::prefix('borrows')->name('borrows.')->group(function (): void {
    // View all borrows with filtering
    Route::get('/', [BorrowController::class, 'index'])->name('index');

    // Borrow initiation (protected from overdue users)
    Route::middleware(['overdue.block'])->group(function (): void {
        Route::get('/books/{book}/borrow', [BorrowController::class, 'create'])
            ->name('create');
        Route::post('/books/{book}/borrow', [BorrowController::class, 'store'])
            ->name('store');
    });

    // Borrow management (owner verification required)
    Route::middleware(['owns:borrow'])->group(function (): void {
        Route::get('/{borrow}', [BorrowController::class, 'show'])
            ->name('show');
        Route::get('/{borrow}/return', [BorrowController::class, 'showReturn'])
            ->name('return.show');
        Route::post('/{borrow}/return', [BorrowController::class, 'return'])
            ->name('return.process');
        Route::post('/{borrow}/renew', [BorrowController::class, 'renew'])
            ->name('renew');
    });
});

// Reading Lists Integration (Cross-flow feature)
Route::prefix('reading-lists')->name('reading-lists.')->group(function (): void {
    Route::get('/', [ReadingListController::class, 'index'])->name('index');
    Route::get('/create', [ReadingListController::class, 'create'])->name('create');
    Route::post('/', [ReadingListController::class, 'store'])->name('store');

    // Public reading list viewing (no ownership required for public lists)
    Route::get('/{readingList}', [ReadingListController::class, 'show'])->name('show');

    // Individual reading list management (requires ownership)
    Route::middleware(['owns:readingList'])->group(function (): void {
        Route::get('/{readingList}/edit', [ReadingListController::class, 'edit'])->name('edit');
        Route::put('/{readingList}', [ReadingListController::class, 'update'])->name('update');
        Route::delete('/{readingList}', [ReadingListController::class, 'destroy'])->name('destroy');

        // Book management within lists
        Route::post('/{readingList}/books/{book}', [ReadingListController::class, 'addBook'])
            ->name('add-book');
        Route::delete('/{readingList}/books/{bookItem}', [ReadingListController::class, 'removeBook'])
            ->name('remove-book');
    });

    // Public actions (no ownership required)
    Route::post('/{readingList}/duplicate', [ReadingListController::class, 'duplicate'])
        ->name('duplicate');
});
