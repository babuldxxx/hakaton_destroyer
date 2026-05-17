<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TransactionType;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => \App\Models\User::factory(),
        'artist_id' => \App\Models\Artist::factory(),
        'song_id' => \App\Models\Song::factory(),
        'platform_id' => null,
        'order_id' => null,
        'type' => TransactionType::PlatformEarning,
        'amount' => fake()->randomFloat(2, 100, 5000),
        'description' => fake()->sentence(),
        ];
    }
}
