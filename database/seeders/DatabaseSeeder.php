<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\UserRoles;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $genres = Genre::factory()->count(5)->create();

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@library.com',
            'password' => bcrypt('password'),
            'role' => UserRoles::ADMIN->value,
        ]);

        // Create regular users
        $users = User::factory()->count(10)->create();

        // Create books with covers
        Book::factory()
            ->count(20)
            ->withCoverImage()
            ->create()
            ->each(function ($book) use ($genres) {
                $book->genre()->associate($genres->random());
                $book->save();
            });

        // Create reservations
        Reservation::factory()
            ->count(15)
            ->create();

        // Create borrowings
        Borrow::factory()
            ->count(30)
            ->create();

        // Create some returned borrowings
        Borrow::factory()
            ->count(10)
            ->returned()
            ->create();

        // Create some overdue borrowings
        Borrow::factory()
            ->count(5)
            ->overdue()
            ->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => UserRoles::ADMIN->value,
        ]);
        User::factory()->create([
            'name' => 'librarian',
            'email' => 'librarian@example.com',
            'role' => UserRoles::LIBRARIAN->value,
        ]);
        User::factory()->create([
            'name' => 'member',
            'email' => 'member@example.com',
            'role' => UserRoles::USER->value,
        ]);

        $this->call([
            BookSeeder::class,
            Borrowseeder::class,
            ReservationSeeder::class,
            ReviewSeeder::class,
            GenreSeeder::class,
        ]);
    }
}
