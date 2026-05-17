<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        $platforms = [
            ['name' => 'VK Music',       'slug' => 'vk-music',     'code' => 'vk'],
            ['name' => 'Apple Music',    'slug' => 'apple-music',  'code' => 'apple'],
            ['name' => 'Spotify',        'slug' => 'spotify',      'code' => 'spotify'],
            ['name' => 'Яндекс Музыка',  'slug' => 'yandex-music', 'code' => 'yandex'],
            ['name' => 'Deezer',         'slug' => 'deezer',       'code' => 'deezer'],
            ['name' => 'YouTube Music',  'slug' => 'youtube-music','code' => 'youtube'],
        ];

        foreach ($platforms as $p) {
            Platform::create($p);
        }
    }
}