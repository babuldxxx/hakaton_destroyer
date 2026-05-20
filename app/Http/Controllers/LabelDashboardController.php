<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Payout;
use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $label = Label::find($user->label_id);

        if (!$label) {
            return Inertia::render('Dashboard/Label', [
                'stats' => [
                    'totalRevenue' => '0 ₽',
                    'artistsCount' => 0,
                    'tracksCount'  => 0,
                    'pendingPayouts'=> '0 ₽',
                ],
                'revenueData' => $this->emptyChartData(),
                'platformData' => ['labels' => [], 'datasets' => []],
                'topTracks' => [],
                'filters' => ['period' => 'year'],
            ]);
        }

        $period = $request->input('period', 'year'); // week/month/quarter/half/year

        // Общие KPI (за всё время)
        $totalRevenue = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
            ->sum('gross_amount');
        $artistsCount = Artist::where('label_id', $label->id)->where('status', 'approved')->count();
        $tracksCount = Song::where('label_id', $label->id)->count();
        $pendingPayouts = Payout::whereHas('artist', fn($q) => $q->where('label_id', $label->id))
            ->where('status', 'pending')
            ->sum('amount');

        // График доходов за выбранный период
        $revenueData = $this->getLabelRevenueChart($label, $period);

        // Распределение по площадкам (donut) – за всё время
        $platformData = $this->getPlatformDistribution($label);

        // Топ-5 треков за всё время
        $topTracks = Song::where('label_id', $label->id)
            ->withSum('earnings', 'gross_amount')
            ->orderByDesc('earnings_sum_gross_amount')
            ->take(5)
            ->get()
            ->map(fn($song) => [
                'title'  => $song->title,
                'artist' => $song->artists->first()?->stage_name ?? 'Неизвестен',
                'amount' => number_format($song->earnings_sum_gross_amount ?? 0, 0, ',', ' ') . ' ₽',
                'growth' => '+0%',
            ]);

        return Inertia::render('Dashboard/Label', [
            'stats' => [
                'totalRevenue'  => number_format($totalRevenue, 0, ',', ' ') . ' ₽',
                'artistsCount'  => $artistsCount,
                'tracksCount'   => $tracksCount,
                'pendingPayouts'=> number_format($pendingPayouts, 0, ',', ' ') . ' ₽',
            ],
            'revenueData' => $revenueData,
            'platformData' => $platformData,
            'topTracks' => $topTracks,
            'filters' => ['period' => $period],
        ]);
    }

    private function getLabelRevenueChart($label, string $period): array
    {
        // Определяем интервал дат
        $now = now();
        switch ($period) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                // Группируем по дням недели
                $earnings = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('DAYOFWEEK(created_at) as dow, SUM(gross_amount) as total')
                    ->groupBy('dow')
                    ->pluck('total', 'dow');
                $labels = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
                $data = [];
                for ($d = 1; $d <= 7; $d++) {
                    $data[] = (float) ($earnings[$d] ?? 0);
                }
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                // По дням месяца (1..31)
                $earnings = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('DAY(created_at) as day, SUM(gross_amount) as total')
                    ->groupBy('day')
                    ->pluck('total', 'day');
                $daysInMonth = $start->daysInMonth;
                $labels = range(1, $daysInMonth);
                $data = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $data[] = (float) ($earnings[$d] ?? 0);
                }
                break;
            case 'quarter':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                // По месяцам квартала
                $earnings = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('MONTH(created_at) as month, SUM(gross_amount) as total')
                    ->groupBy('month')
                    ->pluck('total', 'month');
                $labels = [];
                $data = [];
                for ($m = $start->month; $m <= $end->month; $m++) {
                    $labels[] = $this->monthName($m);
                    $data[] = (float) ($earnings[$m] ?? 0);
                }
                break;
            case 'half':
                $start = $now->copy()->subMonths(5)->startOfMonth(); // последние 6 месяцев
                $end = $now->copy()->endOfMonth();
                $earnings = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('MONTH(created_at) as month, SUM(gross_amount) as total')
                    ->groupBy('month')
                    ->pluck('total', 'month');
                $labels = [];
                $data = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $m = $date->month;
                    $labels[] = $this->monthName($m);
                    $data[] = (float) ($earnings[$m] ?? 0);
                }
                break;
            case 'year':
            default:
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $earnings = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('MONTH(created_at) as month, SUM(gross_amount) as total')
                    ->groupBy('month')
                    ->pluck('total', 'month');
                $labels = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
                $data = [];
                for ($m = 1; $m <= 12; $m++) {
                    $data[] = (float) ($earnings[$m] ?? 0);
                }
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

    private function monthName(int $m): string
    {
        $names = ['','Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
        return $names[$m] ?? $m;
    }

    private function getPlatformDistribution($label): array
    {
        $platforms = Earning::whereHas('song', fn ($q) => $q->where('label_id', $label->id))
            ->with('platform')
            ->get()
            ->groupBy('platform_id')
            ->map(fn ($group) => $group->sum('gross_amount'));

        $names = [];
        $amounts = [];
        $colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#64748B'];

        foreach ($platforms as $platformId => $sum) {
            $platform = \App\Models\Platform::find($platformId);
            if ($platform) {
                $names[] = $platform->name;
                $amounts[] = $sum;
            }
        }

        return [
            'labels' => $names,
            'datasets' => [[
                'data' => $amounts,
                'backgroundColor' => $colors,
                'borderColor' => 'white',
                'borderWidth' => 2,
                'hoverOffset' => 4,
            ]]
        ];
    }

    private function emptyChartData(): array
    {
        return [
            'labels' => ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
            'datasets' => [
                [
                    'label' => 'Доход',
                    'data' => array_fill(0, 12, 0),
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
