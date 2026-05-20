<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Transaction;
use App\Models\Payout;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $artist = $user->artist;

        if (!$artist || $artist->status !== 'approved') {
            $hasInvitations = Invitation::where('artist_id', $artist->id ?? 0)
                ->where('status', 'pending')
                ->exists();

            return Inertia::render('Dashboard/ArtistPending', [
                'message' => 'Ваш аккаунт ожидает подтверждения лейблом.',
                'hasInvitations' => $hasInvitations,
            ]);
        }

        // Реальные показатели
        $balance = Transaction::where('artist_id', $artist->id)
            ->where('status', 'pending')
            ->sum('amount');

        $totalEarned = Transaction::where('artist_id', $artist->id)
            ->sum('amount');

        $totalPaid = Payout::where('artist_id', $artist->id)
            ->where('status', 'paid')
            ->sum('amount');

        $tracksCount = $artist->songs()->count();

        // Топ-треки артиста по доходу (реальные)
        $topTracks = $artist->songs()
            ->withSum('earnings', 'gross_amount')
            ->orderByDesc('earnings_sum_gross_amount')
            ->take(5)
            ->get()
            ->map(fn ($song) => [
                'title'  => $song->title,
                'revenue' => $song->earnings_sum_gross_amount ?? 0,
            ]);

        return Inertia::render('Dashboard/Artist', [
            'stats' => [
                'balance'      => number_format($balance, 0, ',', ' ') . ' ₽',
                'total_income' => number_format($totalEarned, 0, ',', ' ') . ' ₽',
                'tracks_count' => (string) $tracksCount,
                'tracks_sub'   => $tracksCount > 0 ? '+1 за месяц' : '0',
                'paid_out'     => number_format($totalPaid, 0, ',', ' ') . ' ₽',
            ],
            'topTracks' => $topTracks,
            'revenueData' => $this->getArtistRevenueData($artist), // реальный график
        ]);
    }

    /** Готовит данные для графика доходов артиста по месяцам */
    private function getArtistRevenueData($artist): array
    {
        $currentYear = now()->year;
        $monthly = Transaction::where('artist_id', $artist->id)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float) ($monthly[$m] ?? 0);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Доход',
                    'data' => $data,
                    'borderColor' => '#7C3AED',
                    'backgroundColor' => 'rgba(124, 58, 237, 0.08)',
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#7C3AED',
                    'pointBorderColor' => '#0B0E14',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
                    'tension' => 0.4,
                    'fill' => true,
                ]
            ]
        ];
    }
}
