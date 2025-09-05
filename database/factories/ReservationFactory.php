<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $book = Book::query()->inRandomOrder()->first();
        $user = User::query()->inRandomOrder()->first();

        return [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'reserved_at' => $this->faker->dateTimeBetween('-1 week'),
            'expires_at' => $this->faker->dateTimeBetween('now', '+7 days'),
            'canceled_at' => null,
            'fulfilled_by_borrow_id' => null,
        ];
    }

    public function canceled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'canceled_at' => $this->faker->dateTimeBetween($attributes['reserved_at']),
            ];
        });
    }

    public function fulfilled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'fulfilled_by_borrow_id' => Borrow::factory()->create([
                    'book_id' => $attributes['book_id'],
                    'user_id' => $attributes['user_id'],
                ])->id,
            ];
        });
    }

    public function expired(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => $this->faker->dateTimeBetween('-1 week', '-1 day'),
            ];
        });
    }
}
