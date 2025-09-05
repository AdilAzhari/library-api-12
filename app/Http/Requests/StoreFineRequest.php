<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Fine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'borrow_id' => ['nullable', 'integer', 'exists:borrows,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'reason' => [
                'required',
                'string',
                Rule::in([
                    Fine::REASON_OVERDUE,
                    Fine::REASON_LOST_BOOK,
                    Fine::REASON_DAMAGED_BOOK,
                    Fine::REASON_LATE_RETURN,
                    Fine::REASON_PROCESSING_FEE,
                    Fine::REASON_OTHER,
                ]),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'A user must be selected.',
            'user_id.exists' => 'The selected user does not exist.',
            'book_id.required' => 'A book must be selected.',
            'book_id.exists' => 'The selected book does not exist.',
            'amount.required' => 'The fine amount is required.',
            'amount.min' => 'The fine amount must be at least $0.01.',
            'amount.max' => 'The fine amount cannot exceed $9,999.99.',
            'reason.required' => 'A reason for the fine must be provided.',
            'reason.in' => 'The selected reason is invalid.',
            'due_date.after_or_equal' => 'The due date must be today or later.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'borrow_id' => 'borrow record',
            'book_id' => 'book',
            'amount' => 'fine amount',
            'reason' => 'fine reason',
            'description' => 'description',
            'due_date' => 'payment due date',
            'notes' => 'notes',
        ];
    }
}
