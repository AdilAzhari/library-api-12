<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBorrowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'The book ID is required.',
            'book_id.exists' => 'The selected book does not exist.',
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }
}
