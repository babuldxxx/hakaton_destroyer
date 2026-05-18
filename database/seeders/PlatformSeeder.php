<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'VK Music',       'slug' => 'vk-music',       'icon' => 'vk',        'code' => 'vk'],
            ['name' => 'Apple Music',    'slug' => 'apple-music',    'icon' => 'apple',     'code' => 'apple'],
            ['name' => 'Spotify',        'slug' => 'spotify',        'icon' => 'spotify',   'code' => 'spotify'],
            ['name' => 'Яндекс Музыка',  'slug' => 'yandex-music',   'icon' => 'yandex',    'code' => 'yandex'],
            ['name' => 'Deezer',         'slug' => 'deezer',         'icon' => 'deezer',    'code' => 'deezer'],
            ['name' => 'YouTube Music',  'slug' => 'youtube-music',  'icon' => 'youtube',   'code' => 'youtube'],
        ];

        foreach ($platforms as $p) {
            Platform::firstOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
    }
}
