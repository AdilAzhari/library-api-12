<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function borrowBook(Request $request, Book $book)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        if ($book->borrows()->whereNull('returned_at')->exists()) {
            return response()->json(['message' => 'Book is already borrowed'], 400);
        }

        $book->borrows()->create(['user_id' => $request->user_id, 'borrowed_at' => now()]);

        return response()->json(['message' => 'Book borrowed successfully'], 201);
    }

}
