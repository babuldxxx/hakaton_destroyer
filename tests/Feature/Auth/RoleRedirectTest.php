<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_label_user_redirects_to_label_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'label',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('label.dashboard', absolute: false));
    }

    public function test_artist_user_redirects_to_artist_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'artist',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('artist.dashboard', absolute: false));
    }

    public function test_artist_cannot_access_label_dashboard()
    {
        $user = User::factory()->create(['role' => 'artist']);
        $response = $this->actingAs($user)->get('/label/dashboard');
        $response->assertStatus(403);
    }

    public function test_label_cannot_access_artist_dashboard()
    {
        $user = User::factory()->create(['role' => 'label']);
        $response = $this->actingAs($user)->get('/artist/dashboard');
        $response->assertStatus(403);
    }
}
