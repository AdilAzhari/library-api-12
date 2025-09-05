<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Book;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_Book_C;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

final class BooksQuery extends Query
{
    protected $attributes = [
        'name' => 'books',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Book')); // Returns a list of BookType
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'description' => 'Optional book ID to filter by',
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'Filter by status (available/reserved/borrowed)',
            ],
        ];
    }

    public function resolve($args): Collection|array|_IH_Book_C
    {
        if (isset($args['id'])) {
            return Book::query()->where('id', $args['id'])->get();
        }

        if (isset($args['status'])) {
            return Book::query()->where('status', $args['status'])->get();
        }

        return App\Models\Book::query()->all(); // Return all books if no filters
    }
}
