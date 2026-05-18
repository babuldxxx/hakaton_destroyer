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

        // 3. Создаём пользователей-артистов явно
        $artistUser1 = User::factory()->create([
            'name' => 'Artist One',
            'email' => 'artist1@example.com',
            'role' => UserRole::Artist,
            'label_id' => $label->id,
        ]);

        $artistUser2 = User::factory()->create([
            'name' => 'Artist Two',
            'email' => 'artist2@example.com',
            'role' => UserRole::Artist,
            'label_id' => $label->id,
        ]);

        // 4. Создаём артистов
        $artist1 = Artist::factory()->create([
            'user_id' => $artistUser1->id,
            'label_id' => $label->id,
        ]);

        $artist2 = Artist::factory()->create([
            'user_id' => $artistUser2->id,
            'label_id' => $label->id,
        ]);

        $artists = collect([$artist1, $artist2]);

        // 5. Жанр
        $genre = Genre::factory()->create(['name' => 'Pop']);

        // 6. Три песни
        $songs = Song::factory()->count(3)->create([
            'label_id' => $label->id,
            'genre_id' => $genre->id,
        ]);

        // 7. Получаем площадки
        $vk = Platform::query()->where('slug', 'vk-music')->first() ?? Platform::query()->first();
        $ym = Platform::query()->where('slug', 'yandex-music')->first() ?? Platform::query()->latest()->first();

        if (!$vk || !$ym) {
            throw new \Exception('Platforms not found. Run PlatformSeeder first.');
        }

        // 8. Авторы песен и доли
        foreach ($songs as $song) {
            SongAuthor::factory()->create([
                'song_id' => $song->id,
                'artist_id' => $artist1->id,
                'role' => SongAuthorRole::Composer,
                'share_percentage' => 50.00,
                'rights_type' => RightsType::Author,
            ]);

            SongAuthor::factory()->create([
                'song_id' => $song->id,
                'artist_id' => $artist2->id,
                'role' => SongAuthorRole::Performer,
                'share_percentage' => 50.00,
                'rights_type' => RightsType::Related,
            ]);
        }

        // 9. Доходы по песням
        foreach ($songs as $song) {
            SongPlatformEarning::factory()->create([
                'song_id' => $song->id,
                'platform_id' => $vk->id,
                'amount' => 1500.00,
            ]);
        }

        // 10. Один заказ
        $order = CustomOrder::factory()->create([
            'label_id' => $label->id,
            'song_id' => $songs->first()->id,
        ]);

        // 11. Транзакции
        Transaction::factory()->create([
            'user_id' => $artistUser1->id,
            'artist_id' => $artist1->id,
            'song_id' => $songs->first()->id,
            'platform_id' => $vk->id,
            'type' => TransactionType::PlatformEarning,
        ]);

        // 12. Одна выплата
        Payout::factory()->create([
            'artist_id' => $artist1->id,
            'status' => PayoutStatus::Completed,
            'paid_at' => now(),
        ]);
    }
}
