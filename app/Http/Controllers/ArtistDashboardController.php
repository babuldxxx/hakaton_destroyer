<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $artist = $user->artist; // отношение HasOne

        if (!$artist || $artist->status !== 'approved') {
            // Есть ли активные приглашения?
            $hasInvitations = Invitation::where('artist_id', $artist->id ?? 0)
                ->where('status', 'pending')
                ->exists();

            return Inertia::render('Dashboard/ArtistPending', [
                'message' => 'Ваш аккаунт ожидает подтверждения лейблом.',
                'hasInvitations' => $hasInvitations,
            ]);
        }

        // Полноценный дашборд артиста
        return Inertia::render('Dashboard/Artist', [
            'stats' => [
                'balance' => '87 500 ₽',
                'total_income' => '524 300 ₽',
                'tracks_count' => '1',
                'tracks_sub' => '+2 за месяц',
                'paid_out' => '436 800 ₽',
            ],
        ]);
    }
}
