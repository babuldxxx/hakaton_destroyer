<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'VK Music',       'slug' => 'vk-music',     'icon' => 'vk'],
            ['name' => 'Apple Music',    'slug' => 'apple-music',  'icon' => 'apple'],
            ['name' => 'Spotify',        'slug' => 'spotify',      'icon' => 'spotify'],
            ['name' => 'Яндекс Музыка',  'slug' => 'yandex-music', 'icon' => 'yandex'],
            ['name' => 'Deezer',         'slug' => 'deezer',       'icon' => 'deezer'],
            ['name' => 'YouTube Music',  'slug' => 'youtube-music','icon' => 'youtube'],
        ];

        foreach ($platforms as $p) {
            Platform::factory()->create($p);
        }
    }
}
