<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class BookStoreRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|digits:4|integer|min:1900|max:'.now()->format('Y'),
            'genre_id' => 'required|integer|exists:genres,id',
        ];
    }

    //    public function after(): array
    //    {
    //        return [
    //            function (Validator $validator) {
    //                if ($validator->errors()->any()) {
    //                    return;
    //                }
    //            }
    //        ];
    //    }
}
