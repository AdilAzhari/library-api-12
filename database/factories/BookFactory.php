<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'publication_year' => $this->faker->dateTimeBetween('-3 year')->format('Y'),
            'genre_id' => Genre::factory()->create([
                'name' => $this->faker->word(),
                'slug' => $this->faker->slug(),
                'image' => $this->faker->imageUrl(),
                'status' => 1,
                'description' => $this->faker->paragraph(),
            ]),
            'average_rating' => $this->faker->numberBetween(1,5),
            'is_borrowed' => $this->faker->randomElement([0,1]),
        ];
    }
}
