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
            'publication_year' => $this->faker->dateTimeBetween('-3 year')->format('Y'),
            'description' => $this->faker->paragraph(),
            'genre_id' => Genre::factory(),
            'ISBN' => $this->faker->isbn13,
            'status' => $this->faker->randomElement(['Available', 'Borrowed', 'Reserved']),
            'average_rating' => $this->faker->randomFloat(1, 1, 5),
        ];
    }

    public function withCoverImage(): BookFactory|Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'cover_image' => 'covers/' . $this->faker->uuid . '.jpg',
            ];
        });
    }

    public function available(): BookFactory|Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'available',
            ];
        });
    }

    public function borrowed(): BookFactory|Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'borrowed',
            ];
        });
    }

    public function reserved(): BookFactory|Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'reserved',
            ];
        });
    }
}
