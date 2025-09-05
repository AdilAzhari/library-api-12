<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrow>
 */
final class BorrowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $book = Book::query()->inRandomOrder()->first();
        $user = User::query()->inRandomOrder()->first();

        return [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'borrowed_at' => $this->faker->dateTimeBetween('-1 month'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'returned_at' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }

    public function returned(): Factory|self
    {
        return $this->state(function (array $attributes) {
            return [
                'returned_at' => $this->faker->dateTimeBetween($attributes['borrowed_at'], 'now'),
            ];
        });
    }

    public function overdue(): Factory|self
    {
        return $this->state(function (array $attributes) {
            return [
                'due_date' => $this->faker->dateTimeBetween('-2 weeks', '-1 day'),
                'returned_at' => null,
            ];
        });
    }
}
