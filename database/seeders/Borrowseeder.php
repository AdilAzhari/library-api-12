<?php

namespace Database\Seeders;

use App\Models\Borrow;
use Illuminate\Database\Seeder;

class Borrowseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Borrow::factory()->count(30)->create();
    }
}
