<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\SongAuthorRole;
use App\Enums\RightsType;

/**
 * @extends Factory<Model>
 */
class SongAuthorFactory extends Factory
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
        'artist_id' => \App\Models\Artist::factory(),
        'role' => SongAuthorRole::Author,
        'share_percentage' => fake()->randomFloat(2, 0, 100),
        'rights_type' => RightsType::Author,
        ];
    }
}
