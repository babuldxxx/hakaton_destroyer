<?php

namespace Database\Factories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'title' => fake()->sentence(3),
        'lyrics' => fake()->paragraph(),
        'written_at' => fake()->date(),
        'released_at' => fake()->date(),
        'label_id' => \App\Models\Label::factory(),
        'wav_path' => fake()->filePath(),
        'mp3_path' => fake()->filePath(),
        'isrc' => 'US-' . fake()->unique()->randomNumber(6, true),
        'genre_id' => \App\Models\Genre::factory(),
        ];
    }
}
