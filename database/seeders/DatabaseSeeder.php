<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Enums\RightsType;
use App\Enums\SongAuthorRole;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\Artist;
use App\Models\CustomOrder;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Payout;
use App\Models\Platform;
use App\Models\Song;
use App\Models\SongAuthor;
use App\Models\SongPlatformEarning;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Сначала запускаем PlatformSeeder для создания правильных площадок
        $this->call(PlatformSeeder::class);

        // 1. Лейбл
        $label = Label::factory()->create(['name' => 'Test Label']);

        // 2. Менеджер лейбла (user с ролью label)
        $labelUser = User::factory()->create([
            'name' => 'Label Manager',
            'email' => 'label@example.com',
            'role' => UserRole::Label,
            'label_id' => $label->id,
        ]);

        // 3. Два артиста (+ их пользователи)
        $artistUsers = User::factory()->count(2)->create([
            'role' => UserRole::Artist,
        ]);

        $artists = $artistUsers->map(fn ($user) => Artist::factory()->create([
            'user_id' => $user->id,
            'label_id' => $label->id,
        ]));

        // 4. Жанр
        $genre = Genre::factory()->create(['name' => 'Pop']);

        // 5. Три песни
        $songs = Song::factory()->count(3)->create([
            'label_id' => $label->id,
            'genre_id' => $genre->id,
        ]);

        // 6. Получаем созданные из PlatformSeeder площадки для связи с доходами
        $vk = Platform::where('slug', 'vk-music')->first() ?? Platform::first();
        $ym = Platform::where('slug', 'yandex-music')->first() ?? Platform::latest()->first();

        // 7. Авторы песен и доли
        foreach ($songs as $song) {
            SongAuthor::factory()->create([
                'song_id' => $song->id,
                'artist_id' => $artists->first()->id,
                'role' => SongAuthorRole::Composer,
                'share_percentage' => 50.00,
                'rights_type' => RightsType::Author,
            ]);

            SongAuthor::factory()->create([
                'song_id' => $song->id,
                'artist_id' => $artists->last()->id,
                'role' => SongAuthorRole::Performer,
                'share_percentage' => 50.00,
                'rights_type' => RightsType::Related,
            ]);
        }

        // 8. Доходы по песням
        foreach ($songs as $song) {
            SongPlatformEarning::factory()->create([
                'song_id' => $song->id,
                'platform_id' => $vk->id,
                'amount' => 1500.00,
            ]);
        }

        // 9. Один заказ (custom order)
        $order = CustomOrder::factory()->create([
            'label_id' => $label->id,
            'song_id' => $songs->first()->id,
        ]);

        // 10. Тразнакции
        Transaction::factory()->create([
            'user_id' => $artistUsers->first()->id,
            'artist_id' => $artists->first()->id,
            'song_id' => $songs->first()->id,
            'platform_id' => $vk->id,
            'type' => TransactionType::PlatformEarning,
        ]);

        // 11. Одна выплата
        Payout::factory()->create([
            'artist_id' => $artists->first()->id,
            'status' => PayoutStatus::Completed,
            'paid_at' => now(),
        ]);
    }
}