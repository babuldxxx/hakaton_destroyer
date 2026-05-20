<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Enums\RightsType;
use App\Enums\SongAuthorRole;
use App\Models\Artist;
use App\Models\CustomOrder;
use App\Models\Earning;
use App\Models\Genre;
use App\Models\Invitation;
use App\Models\Label;
use App\Models\Payout;
use App\Models\Platform;
use App\Models\Song;
use App\Models\SongAuthor;
use App\Models\Transaction;
use App\Models\User;
use App\Services\RoyaltyCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Площадки и роли
        $this->call(PlatformSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // Жанры
        $genres = [
            ['name' => 'Pop'],
            ['name' => 'Rock'],
            ['name' => 'Hip-Hop'],
            ['name' => 'Electronic'],
            ['name' => 'R&B'],
        ];
        foreach ($genres as $g) {
            Genre::firstOrCreate($g);
        }

        // ---------- ЛЕЙБЛ 1 ----------
        $label1 = Label::factory()->create(['name' => 'Label One', 'description' => 'Первый крупный лейбл']);
        $label1User = User::factory()->create([
            'name'     => 'Label1 Manager',
            'email'    => 'label1@example.com',
            'password' => Hash::make('password'),
            'label_id' => $label1->id,
        ]);
        $label1User->assignRole('label');

        // Артисты лейбла 1 (принятые)
        $artistsLabel1 = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'name'     => "Artist L1-{$i}",
                'email'    => "artist1_{$i}@example.com",
                'password' => Hash::make('password'),
                'label_id' => $label1->id,
            ]);
            $user->assignRole('artist');
            $artist = Artist::factory()->create([
                'user_id'    => $user->id,
                'label_id'   => $label1->id,
                'stage_name' => "Star L1-{$i}",
                'real_name'  => $user->name,
                'status'     => 'approved',
            ]);
            $artistsLabel1[] = $artist;
        }

        // ---------- ЛЕЙБЛ 2 ----------
        $label2 = Label::factory()->create(['name' => 'Label Two', 'description' => 'Второй независимый лейбл']);
        $label2User = User::factory()->create([
            'name'     => 'Label2 Manager',
            'email'    => 'label2@example.com',
            'password' => Hash::make('password'),
            'label_id' => $label2->id,
        ]);
        $label2User->assignRole('label');

        // Артисты лейбла 2 (принятые)
        $artistsLabel2 = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'name'     => "Artist L2-{$i}",
                'email'    => "artist2_{$i}@example.com",
                'password' => Hash::make('password'),
                'label_id' => $label2->id,
            ]);
            $user->assignRole('artist');
            $artist = Artist::factory()->create([
                'user_id'    => $user->id,
                'label_id'   => $label2->id,
                'stage_name' => "Star L2-{$i}",
                'real_name'  => $user->name,
                'status'     => 'approved',
            ]);
            $artistsLabel2[] = $artist;
        }

        // Непривязанные артисты (pending) – 3 шт.
        $pendingArtists = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = User::factory()->create([
                'name'     => "Free Artist {$i}",
                'email'    => "free{$i}@example.com",
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('artist');
            $artist = Artist::factory()->create([
                'user_id'    => $user->id,
                'stage_name' => "FreeStar {$i}",
                'real_name'  => $user->name,
                'status'     => 'pending',
                'label_id'   => null,
            ]);
            $pendingArtists[] = $artist;
        }

        // ---------- ПРИГЛАШЕНИЯ ----------
        if (isset($pendingArtists[0])) {
            Invitation::create([
                'label_id'  => $label1->id,
                'artist_id' => $pendingArtists[0]->id,
                'status'    => 'pending',
            ]);
        }
        if (isset($pendingArtists[1])) {
            Invitation::create([
                'label_id'  => $label2->id,
                'artist_id' => $pendingArtists[1]->id,
                'status'    => 'pending',
            ]);
        }

        // ---------- ПЕСНИ ----------
        $allArtists = array_merge($artistsLabel1, $artistsLabel2);
        $platforms = Platform::all();
        $songTitles = [
            'Midnight Dreams', 'Electric Heart', 'Summer Rain', 'Golden Lights',
            'Neon Nights', 'Lost in Tokyo', 'Ocean Drive', 'Crystal Tears',
            'Firestorm', 'Silent Echo', 'Starlight', 'Velocity',
            'Bittersweet', 'Horizon', 'Lunar', 'Fade Away',
            'Rising Sun', 'Afterglow', 'Phantom', 'Dynamite'
        ];

        foreach ($songTitles as $index => $title) {
            $artist = $allArtists[array_rand($allArtists)];
            $genre = Genre::inRandomOrder()->first();
            $label = $artist->label;

            $song = Song::factory()->create([
                'title'       => $title,
                'label_id'    => $label ? $label->id : null,
                'genre_id'    => $genre->id,
                'written_at'  => now()->subMonths(rand(1, 12)),
                'released_at' => now()->subDays(rand(10, 365)),
            ]);

            $song->platforms()->sync(
                $platforms->random(rand(1, 4))->pluck('id')->toArray()
            );

            // Авторы
            $authors = collect([$artist]);
            if (rand(0, 1) && count($allArtists) > 1) {
                $second = $allArtists[array_rand($allArtists)];
                if ($second->id !== $artist->id) {
                    $authors->push($second);
                }
            }
            $roles = [SongAuthorRole::Performer, SongAuthorRole::Composer, SongAuthorRole::Producer];
            $totalShare = 100;
            foreach ($authors as $i => $author) {
                $share = ($i === $authors->count() - 1) ? $totalShare : rand(10, min(50, $totalShare));
                $totalShare -= $share;
                SongAuthor::create([
                    'song_id'         => $song->id,
                    'artist_id'       => $author->id,
                    'role'            => $roles[$i % count($roles)],
                    'share_percentage'=> $share,
                    'rights_type'     => $i === 0 ? RightsType::Author : RightsType::Related,
                ]);
            }

            // Доходы и распределение
            $calculator = app(RoyaltyCalculator::class);
            foreach ($song->platforms as $platform) {
                for ($m = 0; $m < 6; $m++) {
                    $period = now()->subMonths($m)->format('Y-m');
                    $gross = rand(500, 5000) + (rand(0, 99) / 100);
                    $earning = Earning::create([
                        'song_id'             => $song->id,
                        'platform_id'         => $platform->id,
                        'period'              => $period,
                        'gross_amount'        => $gross,
                        'label_share_percent' => rand(0, 30),
                        'created_by'          => $label ? $label->users()->first()?->id ?? 1 : 1,
                        'status'              => 'pending',
                    ]);
                    $calculator->distribute($earning);
                }
            }
        }

        // ---------- ТРАНЗАКЦИИ (уже созданы через distribute) ----------

        // ---------- ВЫПЛАТЫ ----------
        foreach ($allArtists as $artist) {
            if (rand(0, 1)) {
                Payout::create([
                    'artist_id' => $artist->id,
                    'amount'    => rand(10000, 50000),
                    'method'    => 'Банковская карта',
                    'status'    => PayoutStatus::Completed,
                    'paid_at'   => now()->subDays(rand(1, 30)),
                ]);
            }
            if (rand(0, 1)) {
                Payout::create([
                    'artist_id' => $artist->id,
                    'amount'    => rand(5000, 30000),
                    'method'    => 'Банковская карта',
                    'status'    => PayoutStatus::Pending,
                ]);
            }
        }

        // ---------- КАСТОМНЫЕ ЗАКАЗЫ ----------
        foreach ([$label1, $label2] as $label) {
            for ($i = 0; $i < 2; $i++) {
                $song = Song::where('label_id', $label->id)->inRandomOrder()->first();
                if ($song) {
                    CustomOrder::create([
                        'label_id'           => $label->id,
                        'song_id'            => $song->id,
                        'client_name'        => "Клиент " . chr(65+$i),
                        'description'        => "Заказ на создание трека",
                        'total_amount'       => rand(20000, 100000),
                        'label_share_percentage'=> rand(10, 30),
                    ]);
                }
            }
        }
    }
}
