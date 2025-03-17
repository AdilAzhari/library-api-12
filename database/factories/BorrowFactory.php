<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Borrow>
 */
class BorrowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'book_id' => Book::query()->inRandomOrder()->first()->id,
            'borrowed_at' => $this->faker->dateTimeBetween('-2 years'),
            'returned_at' => $this->faker->dateTimeBetween('-2 years', '-2 month'),
        ];
    }
}
