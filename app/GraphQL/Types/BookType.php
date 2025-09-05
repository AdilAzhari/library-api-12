<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Book;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

final class BookType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Book',
        'description' => 'A book in the library',
        'model' => Book::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of the book',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title of the book',
            ],
            'author' => [
                'type' => Type::string(),
                'description' => 'Author of the book',
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Availability status (available/reserved/borrowed)',
            ],
        ];
    }
}
