<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Borrow;
use Illuminate\Database\Seeder;

final class Borrowseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Borrow::factory()->count(30)->create();
    }
}
