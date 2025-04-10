<?php

use App\Http\Requests\BookStoreRequest;

it('validates book store request', function () {
    $request = new BookStoreRequest([
        'title' => 'Test Book',
        'author' => 'Test Author',
        'publication_year' => 2023,
        'genre_id' => 1,
    ]);

    expect($request->authorize())->toBeTrue()
        ->and($request->rules())->toBeArray()
        ->toMatchArray([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'required|digits:4|integer|min:1900|max:' . now()->format('Y'),
            'genre_id' => 'required|integer|exists:genres,id',
        ]);
});
