<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        // Удалим старые кривые записи
        Platform::query()->delete();

        // Добавляем площадки с нормальной кириллицей
        Platform::create(['name' => 'VK', 'code' => 'vk']);
        Platform::create(['name' => 'Яндекс Музыка', 'code' => 'yandex']);
        Platform::create(['name' => 'Apple Music', 'code' => 'apple']);
        Platform::create(['name' => 'Spotify', 'code' => 'spotify']);
        Platform::create(['name' => 'YouTube Music', 'code' => 'youtube']);
        Platform::create(['name' => 'Deezer', 'code' => 'deezer']);
    }
}
