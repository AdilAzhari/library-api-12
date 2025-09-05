<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Seeder;

final class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reservation::factory()->count(50)->create();
    }
}
