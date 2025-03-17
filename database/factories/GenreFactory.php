<?php

namespace Database\Factories;

use App\Enum\GenreStatus;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement([GenreStatus::ACTIVE, GenreStatus::INACTIVE]),
            'slug' => $this->faker->slug(),
            'image' => $this->faker->imageUrl(),
        ];
    }
}
