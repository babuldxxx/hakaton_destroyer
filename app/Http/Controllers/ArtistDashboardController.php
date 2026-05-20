<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistDashboardController extends Controller
{
    /** Типы транзакций, которые считаем доходом артиста */
    private const EARNING_TYPES = ['author_rights', 'related_rights'];

    public function index()
    {
        $user   = auth()->user();
        $artist = $user->artist;

        if (! $artist || $artist->status !== 'approved') {
            $hasInvitations = Invitation::where('artist_id', $artist->id ?? 0)
                ->where('status', 'pending')
                ->exists();

            return Inertia::render('Dashboard/ArtistPending', [
                'message'        => 'Ваш аккаунт ожидает подтверждения лейблом.',
                'hasInvitations' => $hasInvitations,
            ]);
        }

        $baseQuery = Transaction::where('artist_id', $artist->id)
            ->whereIn('type', self::EARNING_TYPES);

        // --- Баланс (не выплачено) ---
        $balance = (clone $baseQuery)
            ->where('status', 'pending')
            ->sum('amount');

        // --- Всего заработано (все начисления за всё время) ---
        $totalEarned = (clone $baseQuery)->sum('amount');

        // --- Выплачено (payout, записывается со знаком минус из PayoutController) ---
        $totalPaid = Transaction::where('artist_id', $artist->id)
            ->where('type', 'payout')
            ->where('status', 'completed')
            ->sum('amount'); // отрицательное

        $tracksCount = $artist->songs()->count();

        $topTracks = $artist->songs()
            ->withSum('earnings', 'gross_amount')
            ->orderByDesc('earnings_sum_gross_amount')
            ->take(5)
            ->get()
            ->map(fn ($song) => [
                'title'   => $song->title,
                'revenue' => $song->earnings_sum_gross_amount ?? 0,
            ]);

        return Inertia::render('Dashboard/Artist', [
            'stats' => [
                'balance'      => $this->fmt(max(0, $balance)),
                'total_income' => $this->fmt(max(0, $totalEarned)),
                'tracks_count' => (string) $tracksCount,
                'tracks_sub'   => $tracksCount > 0 ? 'в каталоге' : '0',
                'paid_out'     => $this->fmt(abs($totalPaid)),
            ],
            'topTracks'   => $topTracks,
            'revenueData' => $this->getArtistRevenueData($artist),
        ]);
    }

    private function fmt(float $amount): string
    {
        return number_format($amount, 0, ',', ' ') . ' ₽';
    }

    private function getArtistRevenueData($artist): array
    {
        $year = now()->year;
        $monthly = Transaction::where('artist_id', $artist->id)
            ->whereIn('type', self::EARNING_TYPES) // только начисления!
            ->whereYear('created_at', $year)
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
            'datasets' => [[
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
            ]]
        ];
    }
}