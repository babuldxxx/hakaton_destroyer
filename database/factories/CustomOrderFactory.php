<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class CustomOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'label_id' => \App\Models\Label::factory(),
        'song_id' => null,
        'client_name' => fake()->company(),
        'description' => fake()->paragraph(),
        'total_amount' => fake()->randomFloat(2, 10000, 100000),
        'label_share_percentage' => fake()->randomFloat(2, 10, 50),
        ];
    }
}
