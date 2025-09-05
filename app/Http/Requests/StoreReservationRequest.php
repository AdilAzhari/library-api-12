<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bookId' => ['required', 'integer', 'exists:books,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'The book ID is required.',
        ];
    }
}
