<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class Bookseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::factory()->count(50)->create();
    }
}
