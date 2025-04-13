<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publication_year' => 'required|digits:4|integer|min:1900|max:'.now()->format('Y'),
            'genre_id' => 'required|integer|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'ISBN' => 'nullable|string|max:17|unique:books,ISBN',
            'status' => 'sometimes|in:available,borrowed,reserved',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The book title is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'author.required' => 'The author name is required.',
            'author.max' => 'The author name may not be greater than 255 characters.',
            'publication_year.required' => 'The publication year is required.',
            'publication_year.digits' => 'The publication year must be 4 digits.',
            'publication_year.min' => 'The publication year must be at least 1900.',
            'publication_year.max' => 'The publication year cannot be in the future.',
            'genre_id.required' => 'Please select a genre.',
            'genre_id.exists' => 'The selected genre is invalid.',
            'cover_image.image' => 'The cover must be an image file.',
            'cover_image.mimes' => 'The cover must be a file of type: jpeg, png, jpg, gif, or webp.',
            'cover_image.max' => 'The cover image may not be larger than 2MB.',
            'ISBN.max' => 'The ISBN may not be longer than 17 characters.',
            'ISBN.unique' => 'This ISBN already exists in our system.',
            'status.in' => 'The selected status is invalid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('ISBN') && empty($this->ISBN)) {
            $this->merge(['ISBN' => null]);
        }
    }
}
