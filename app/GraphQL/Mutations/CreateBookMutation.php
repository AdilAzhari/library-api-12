<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Book;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

final class CreateBookMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createBook',
        'description' => 'Creates a new book',
    ];

    public function type(): Type
    {
        return GraphQL::type('Book'); // Returns the BookType
    }

    public function args(): array
    {
        return [
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title of the book',
            ],
            'author' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Author of the book',
            ],
            'publication_year' => [
                'type' => Type::int(),
                'description' => 'Year of publication',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return Book::create([
            'title' => $args['title'],
            'author' => $args['author'],
            'publication_year' => $args['publication_year'] ?? null,
            'status' => 'available', // Default status
        ]);
    }
}
