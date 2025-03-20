<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id ?? User::factory()->create(),
            'book_id' => Book::query()->inRandomOrder()->first()->id ?? Book::factory()->create(),
        ];
    }
}
