<?php

namespace App\Http\Requests\Admin;

class StoreBorrowingRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date',
        ];
    }
}
