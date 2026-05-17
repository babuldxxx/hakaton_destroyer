<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Manager',
            'email' => 'label@example.com',
            'password' => bcrypt('password'),
            'role' => 'label',
        ]);

        User::factory()->create([
            'name' => 'Artist One',
            'email' => 'artist1@example.com',
            'password' => bcrypt('password'),
            'role' => 'artist',
        ]);

        User::factory()->create([
            'name' => 'Artist Two',
            'email' => 'artist2@example.com',
            'password' => bcrypt('password'),
            'role' => 'artist',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
