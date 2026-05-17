<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class SongPlatformEarningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'song_id' => \App\Models\Song::factory(),
        'platform_id' => \App\Models\Platform::factory(),
        'amount' => fake()->randomFloat(2, 100, 10000),
        'currency' => 'RUB',
        'period_start' => fake()->date(),
        'period_end' => fake()->date(),
        'reported_at' => fake()->dateTime(),
        ];
    }
}
