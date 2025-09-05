<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

final class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::factory()->count(10)->create();
    }
}
