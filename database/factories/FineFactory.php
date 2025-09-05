<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fine>
 */
final class FineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reasons = ['overdue', 'lost_book', 'damaged_book', 'late_return', 'processing_fee'];
        $statuses = ['pending', 'paid', 'partial', 'waived'];
        $reason = $this->faker->randomElement($reasons);

        $amount = match ($reason) {
            'overdue' => $this->faker->randomFloat(2, 0.50, 25.00),
            'lost_book' => $this->faker->randomFloat(2, 15.00, 150.00),
            'damaged_book' => $this->faker->randomFloat(2, 5.00, 75.00),
            'late_return' => $this->faker->randomFloat(2, 1.00, 10.00),
            'processing_fee' => $this->faker->randomFloat(2, 2.00, 15.00),
            default => $this->faker->randomFloat(2, 1.00, 50.00),
        };

        return [
            'user_id' => \App\Models\User::factory(),
            'borrow_id' => \App\Models\Borrow::factory(),
            'book_id' => \App\Models\Book::factory(),
            'amount' => $amount,
            'reason' => $reason,
            'status' => $this->faker->randomElement($statuses),
            'description' => $this->faker->sentence(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'paid_at' => null,
            'paid_amount' => null,
            'payment_method' => null,
            'waived_by' => null,
            'waived_at' => null,
            'waiver_reason' => null,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
            'paid_amount' => null,
        ]);
    }

    public function paid()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'paid_amount' => $attributes['amount'],
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'online', 'check']),
        ]);
    }

    public function overdue()
    {
        return $this->state(fn (array $attributes) => [
            'reason' => 'overdue',
            'amount' => $this->faker->randomFloat(2, 0.50, 25.00),
            'description' => 'Overdue fine for '.$this->faker->numberBetween(1, 30).' days',
        ]);
    }
}
