<?php

namespace App\Services;

use App\DTO\BorrowBookDTO;
use App\DTO\ReturnBookDTO;
use App\Events\BookOverdue;
use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use App\Events\BookBorrowed;
use App\Events\BookReturned;
use Exception;

class BorrowingService
{
    /**
     * @throws Exception
     */
    public function borrowBook(BorrowBookDTO $dto): bool
    {
        $book = Book::query()->findOrFail($dto->bookId);
        $user = User::query()->findOrFail($dto->userId);

        if ($book->is_borrowed) {
            throw new Exception('Book is already borrowed.');
        }

        $borrowing = Borrow::query()->create([
            'book_id' => $dto->bookId,
            'user_id' => $dto->userId,
            'borrowed_at' => now(),
            'returned_at' => null,
        ]);

        $book->update(['is_borrowed' => true]);

        event(new BookBorrowed($borrowing));

        return $borrowing;
    }

    public function returnBook(ReturnBookDTO $dto)
    {
        $borrowing = Borrow::query()->where('book_id', $dto->bookId)
            ->where('user_id', $dto->userId)
            ->whereNull('returned_at')
            ->firstOrFail();

        $borrowing->update(['returned_at' => now()]);

        $book = Book::query()->findOrFail($dto->bookId);
        $book->update(['is_borrowed' => false]);

        event(new BookReturned($borrowing));

        return $borrowing;
    }

    public function checkOverdueBooks(): void
    {
        $overdueBooks = Borrow::query()->where('due_date', '<', now())
            ->whereNull('returned_at')
            ->get();

        foreach ($overdueBooks as $borrowing) {
            event(new BookOverdue($borrowing));
        }
    }
}
