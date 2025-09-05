<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\LibraryCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreLibraryCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->is_admin ||
            (int) $this->route('user') === auth()->id()
        );
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail): void {
                    if (! LibraryCard::canUserHaveNewCard($value)) {
                        $fail('This user already has an active library card.');
                    }
                },
            ],
            'issued_date' => ['sometimes', 'date', 'before_or_equal:today'],
            'expires_date' => ['sometimes', 'date', 'after:issued_date'],
            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    LibraryCard::STATUS_ACTIVE,
                    LibraryCard::STATUS_SUSPENDED,
                    LibraryCard::STATUS_EXPIRED,
                    LibraryCard::STATUS_LOST,
                    LibraryCard::STATUS_CANCELLED,
                ]),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'A user must be selected.',
            'user_id.exists' => 'The selected user does not exist.',
            'issued_date.before_or_equal' => 'The issue date cannot be in the future.',
            'expires_date.after' => 'The expiration date must be after the issue date.',
            'status.in' => 'The selected status is invalid.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'user',
            'issued_date' => 'issue date',
            'expires_date' => 'expiration date',
            'status' => 'status',
            'notes' => 'notes',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Set default dates if not provided
        if (! $this->has('issued_date')) {
            $this->merge(['issued_date' => now()->format('Y-m-d')]);
        }

        if (! $this->has('expires_date')) {
            $issuedDate = $this->get('issued_date')
                ? \Carbon\Carbon::parse($this->get('issued_date'))
                : now();

            $this->merge([
                'expires_date' => $issuedDate->addYears(2)->format('Y-m-d'),
            ]);
        }
    }
}
