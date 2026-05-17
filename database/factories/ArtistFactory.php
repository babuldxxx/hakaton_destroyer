<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class ArtistFactory extends Factory
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
        'label_id' => \App\Models\Label::factory(),
        'stage_name' => fake()->name(),
        'real_name' => fake()->name(),
        'bio' => fake()->paragraph(),
        ];
    }
}
