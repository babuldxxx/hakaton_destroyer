<?php

namespace Database\Seeders;

use App\Enums\RightsType;
use App\Enums\SongAuthorRole;
use App\Models\Artist;
use App\Models\CustomOrder;
use App\Models\Genre;
use App\Models\Invitation;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\SongAuthor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlatformSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // Жанры
        $genres = ['Pop', 'Rock', 'Hip-Hop', 'Electronic', 'R&B', 'Jazz', 'Classical'];
        foreach ($genres as $name) {
            Genre::firstOrCreate(['name' => $name]);
        }

        // --- ЛЕЙБЛ 1 ---
        $label1 = Label::factory()->create(['name' => 'Quantum Records', 'description' => 'Ведущий независимый лейбл']);
        $label1User = User::factory()->create([
            'name'     => 'Алексей Менеджер',
            'email'    => 'label1@example.com',
            'password' => Hash::make('password'),
            'label_id' => $label1->id,
        ]);
        $label1User->assignRole('label');

        $artistsLabel1 = [];
        $stageNamesL1 = ['Vega Storm', 'Neon Pulse', 'Lunar Echo', 'Crystal Waves', 'Silver Fang', 'Ember Sky', 'Astra Nova'];
        for ($i = 0; $i < 7; $i++) {
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
                'stage_name' => $stageNamesL1[$i % count($stageNamesL1)],
                'real_name'  => $user->name,
                'status'     => 'approved',
            ]);
            $artistsLabel1[] = $artist;
        }

        // --- ЛЕЙБЛ 2 ---
        $label2 = Label::factory()->create(['name' => 'EchoSphere Music', 'description' => 'Электронная и инди-сцена']);
        $label2User = User::factory()->create([
            'name'     => 'Мария Директор',
            'email'    => 'label2@example.com',
            'password' => Hash::make('password'),
            'label_id' => $label2->id,
        ]);
        $label2User->assignRole('label');

        $artistsLabel2 = [];
        $stageNamesL2 = ['Midnight Mirage', 'Digital Horizon', 'Stellar Drift', 'Nova Whisper', 'Echo Flux', 'Zenith Wave'];
        for ($i = 0; $i < 6; $i++) {
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
                'stage_name' => $stageNamesL2[$i],
                'real_name'  => $user->name,
                'status'     => 'approved',
            ]);
            $artistsLabel2[] = $artist;
        }

        // Свободные артисты
        $pendingArtists = [];
        $freeNames = ['Shadow Voice', 'Dream Walker', 'Skyline Riot'];
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create([
                'name'     => "Free Artist {$i}",
                'email'    => "free{$i}@example.com",
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('artist');
            $artist = Artist::factory()->create([
                'user_id'    => $user->id,
                'stage_name' => $freeNames[$i],
                'real_name'  => $user->name,
                'status'     => 'pending',
                'label_id'   => null,
            ]);
            $pendingArtists[] = $artist;
        }

        // Приглашения
        if (isset($pendingArtists[0])) {
            Invitation::create(['label_id' => $label1->id, 'artist_id' => $pendingArtists[0]->id, 'status' => 'pending']);
        }
        if (isset($pendingArtists[1])) {
            Invitation::create(['label_id' => $label2->id, 'artist_id' => $pendingArtists[1]->id, 'status' => 'pending']);
        }

        // --- ТРЕКИ (без доходов) ---
        $allArtists = array_merge($artistsLabel1, $artistsLabel2);
        $platforms = Platform::all();
        $songTitles = [
            'Midnight Dreams', 'Electric Heart', 'Summer Rain', 'Golden Lights',
            'Neon Nights', 'Lost in Tokyo', 'Ocean Drive', 'Crystal Tears',
            'Firestorm', 'Silent Echo', 'Starlight', 'Velocity',
            'Bittersweet', 'Horizon', 'Lunar', 'Fade Away',
            'Rising Sun', 'Afterglow', 'Phantom', 'Dynamite',
            'Skybound', 'Frozen Flame', 'Neon Jungle', 'Silver Lining',
            'Echoes of You', 'Runaway', 'Higher Ground', 'Into the Blue',
            'Shattered Glass', 'Last Goodbye', 'Wildfire', 'Gravity'
        ];

        foreach ($songTitles as $title) {
            $artist = $allArtists[array_rand($allArtists)];
            $genre = Genre::inRandomOrder()->first();
            $label = $artist->label;

            // Дата выпуска в пределах последнего года
            $releasedAt = now()->subDays(rand(10, 365));
            $writtenAt = $releasedAt->copy()->subMonths(rand(1, 4));

            $song = Song::factory()->create([
                'title'       => $title,
                'label_id'    => $label ? $label->id : null,
                'genre_id'    => $genre->id,
                'written_at'  => $writtenAt,
                'released_at' => $releasedAt,
                'cover_path' => 'https://loremflickr.com/400/400/music,album?random=' . rand(1, 1000),
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
        }

        // --- КАСТОМНЫЕ ЗАКАЗЫ (без привязки к доходам) ---
        foreach ([$label1, $label2] as $label) {
            for ($i = 0; $i < 3; $i++) {
                $song = Song::where('label_id', $label->id)->inRandomOrder()->first();
                if ($song) {
                    CustomOrder::create([
                        'label_id'           => $label->id,
                        'song_id'            => $song->id,
                        'client_name'        => "Клиент " . chr(65+$i),
                        'description'        => "Заказ на создание трека",
                        'total_amount'       => rand(30000, 120000),
                        'label_share_percentage'=> rand(10, 30),
                    ]);
                }
            }
        }
    }
}
