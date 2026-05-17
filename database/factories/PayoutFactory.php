<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PayoutStatus;

/**
 * @extends Factory<Model>
 */
class PayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'artist_id' => \App\Models\Artist::factory(),
        'amount' => fake()->randomFloat(2, 1000, 50000),
        'method' => fake()->randomElement(['bank_transfer', 'card', 'crypto']),
        'status' => PayoutStatus::Pending,
        'paid_at' => null,
        ];
    }
}
