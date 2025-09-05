<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreReadingListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('reading_lists')
                    ->where('user_id', auth()->id()),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => [
                'nullable',
                'string',
                'max:50',
                Rule::in([
                    'book-open', 'heart', 'star', 'bookmark', 'library',
                    'academic-cap', 'sparkles', 'fire', 'lightning-bolt',
                ]),
            ],
            'books' => ['sometimes', 'array'],
            'books.*' => ['integer', 'exists:books,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The reading list name is required.',
            'name.unique' => 'You already have a reading list with this name.',
            'name.max' => 'The reading list name cannot exceed 100 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',
            'icon.in' => 'The selected icon is not available.',
            'books.*.exists' => 'One or more selected books do not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'reading list name',
            'description' => 'description',
            'is_public' => 'public visibility',
            'is_default' => 'default list',
            'color' => 'color',
            'icon' => 'icon',
            'books' => 'books',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('color') && ! str_starts_with($this->color, '#')) {
            $this->merge([
                'color' => '#'.mb_ltrim($this->color, '#'),
            ]);
        }
    }
}
