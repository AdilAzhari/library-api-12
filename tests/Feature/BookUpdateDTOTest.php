<?php

namespace Tests\Unit\DTO;

use App\DTO\BookUpdateDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

it('initializes BookUpdateDTO correctly', function () {
    $data = [
        'title' => 'Updated Book',
        'author' => 'Updated Author',
        'publication_year' => 2023,
        'description' => 'An updated book description.',
        'genre_id' => 1,
        'cover_image' => UploadedFile::fake()->image('updated_cover.jpg'),
    ];

    $dto = new BookUpdateDTO(
        $data['title'],
        $data['author'],
        $data['publication_year'],
        $data['description'],
        $data['genre_id'],
        $data['cover_image']
    );

    expect($dto->title)->toBe($data['title'])
        ->and($dto->author)->toBe($data['author'])
        ->and($dto->publication_year)->toBe($data['publication_year'])
        ->and($dto->description)->toBe($data['description'])
        ->and($dto->genre_id)->toBe($data['genre_id'])
        ->and($dto->cover_image)->toBe($data['cover_image']);
});

it('creates BookUpdateDTO from request', function () {
    $request = new Request([
        'title' => 'Updated Book',
        'author' => 'Updated Author',
        'publication_year' => 2023,
        'description' => 'An updated book description.',
        'genre_id' => 1,
    ]);

    $request->files->set('cover_image', UploadedFile::fake()->image('updated_cover.jpg'));

    $dto = BookUpdateDTO::fromRequest($request);

    expect($dto->title)->toBe($request->title)
        ->and($dto->author)->toBe($request->author)
        ->and($dto->publication_year)->toBe($request->publication_year)
        ->and($dto->description)->toBe($request->description)
        ->and($dto->genre_id)->toBe($request->genre_id)
        ->and($dto->cover_image)->toBe($request->file('cover_image'));
});
