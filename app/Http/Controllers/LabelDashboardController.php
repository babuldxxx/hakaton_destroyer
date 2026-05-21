<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabelDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $label = Label::find($user->label_id);

        if (! $label) {
            return Inertia::render('Dashboard/Label', [
                'stats'        => $this->emptyStats(),
                'revenueData'  => $this->emptyChartData(),
                'platformData' => ['labels' => [], 'datasets' => []],
                'topTracks'    => [],
                'filters'      => ['period' => 'year'],
            ]);
        }

        $period = $request->input('period', 'year');

        // Общий доход — по ВСЕМ записям лейбла (все периоды)
        $totalRevenue = Earning::where('label_id', $label->id)->sum('gross_amount');
        $labelIncome = Transaction::where('type', 'label_share')
            ->whereHas('earning', fn ($q) => $q->where('label_id', $label->id))
            ->sum('amount');

        $artistsCount = Artist::where('label_id', $label->id)->where('status', 'approved')->count();
        $tracksCount  = Song::where('label_id', $label->id)->count();

        $pendingPayouts = (float) Transaction::query()
            ->whereHas('earning', fn($q) => $q->where('label_id', $label->id))
            ->where('type', 'author_royalty')   // только авторские роялти
            ->where('status', 'pending')
            ->sum('amount');

        $revenueData  = $this->getLabelRevenueChart($label, $period);
        $platformData = $this->getPlatformDistribution($label);

        $topTracks = Song::where('label_id', $label->id)
            ->withSum('earnings', 'gross_amount')
            ->orderByDesc('earnings_sum_gross_amount')
            ->take(5)
            ->get()
            ->map(fn ($song) => [
                'title'  => $song->title,
                'artist' => $song->artists->first()?->stage_name ?? $song->raw_artist_name ?? 'Неизвестен',
                'amount' => $song->earnings_sum_gross_amount ?? 0,
                'growth' => '+0%',
            ]);

        return Inertia::render('Dashboard/Label', [
            'stats' => [
                'totalRevenue'   => number_format($totalRevenue, 0, ',', ' ') . ' ₽',
                'labelIncome'    => number_format($labelIncome,  0, ',', ' ') . ' ₽',
                'artistsCount'   => $artistsCount,
                'tracksCount'    => $tracksCount,
                'pendingPayouts' => number_format($pendingPayouts, 0, ',', ' ') . ' ₽',
            ],
            'revenueData'  => $revenueData,
            'platformData' => $platformData,
            'topTracks'    => $topTracks,
            'filters'      => ['period' => $period],
        ]);
    }

    private function getLabelRevenueChart($label, string $period): array
    {
        $now       = now();
        $baseQuery = Earning::where('label_id', $label->id);

        switch ($period) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                $raw = (clone $baseQuery)
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('DAYOFWEEK(created_at) as dow, SUM(gross_amount) as total')
                    ->groupBy('dow')
                    ->pluck('total', 'dow');
                $labels = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
                $data = [];
                for ($d = 1; $d <= 7; $d++) {
                    $data[] = (float) ($raw[$d] ?? 0);
                }
                break;

            case 'month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $daysInMonth = $start->daysInMonth;
                $raw = (clone $baseQuery)
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('DAY(created_at) as day, SUM(gross_amount) as total')
                    ->groupBy('day')
                    ->pluck('total', 'day');
                $labels = range(1, $daysInMonth);
                $data = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $data[] = (float) ($raw[$d] ?? 0);
                }
                break;

            case 'quarter':
                $start = $now->copy()->startOfQuarter();
                $end   = $now->copy()->endOfQuarter();
                // Исправлено: фильтр по period (Y-m), а не created_at
                $raw = (clone $baseQuery)
                    ->whereBetween('period', [$start->format('Y-m'), $end->format('Y-m')])
                    ->selectRaw("SUBSTRING(period, 6, 2) as month, SUM(gross_amount) as total")
                    ->groupBy('month')
                    ->pluck('total', 'month');
                $labels = []; $data = [];
                for ($m = $start->month; $m <= $end->month; $m++) {
                    $labels[] = $this->monthName($m);
                    $key = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $data[] = (float) ($raw[$key] ?? 0);
                }
                break;

            case 'half':
                // Исправлено: последние 6 мес по period, а не created_at
                $labels = []; $data = [];
                for ($i = 5; $i >= 0; $i--) {
                    $m = $now->copy()->subMonths($i);
                    $labels[] = $this->monthName($m->month);
                    $p = $m->format('Y-m');
                    $sum = (clone $baseQuery)->where('period', $p)->sum('gross_amount');
                    $data[] = (float) $sum;
                }
                break;

            case 'year':
            default:
                $year = $now->year;
                // Исправлено: фильтр и группировка по period (Y-m)
                $raw = (clone $baseQuery)
                    ->where('period', 'like', "{$year}-%")
                    ->selectRaw("SUBSTRING(period, 6, 2) as month, SUM(gross_amount) as total")
                    ->groupBy('month')
                    ->pluck('total', 'month');
                $labels = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
                $data = [];
                for ($m = 1; $m <= 12; $m++) {
                    $key = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $data[] = (float) ($raw[$key] ?? 0);
                }
                break;
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

    private function monthName(int $m): string
    {
        $names = ['','Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
        return $names[$m] ?? (string) $m;
    }

    private function getPlatformDistribution($label): array
    {
        $raw = Earning::where('label_id', $label->id)
            ->selectRaw('platform_id, SUM(gross_amount) as total')
            ->groupBy('platform_id')
            ->get();

        $names = []; $amounts = [];
        $colors = ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#64748B'];

        foreach ($raw as $row) {
            $p = Platform::find($row->platform_id);
            if ($p) {
                $names[] = $p->name;
                $amounts[] = (float) $row->total;
            }
        }

        return [
            'labels' => $names,
            'datasets' => [[
                'data' => $amounts,
                'backgroundColor' => array_slice($colors, 0, count($amounts)),
                'borderColor' => 'white',
                'borderWidth' => 2,
                'hoverOffset' => 4,
            ]]
        ];
    }

    private function emptyStats(): array
    {
        return [
            'totalRevenue'   => '0 ₽',
            'labelIncome'    => '0 ₽',
            'artistsCount'   => 0,
            'tracksCount'    => 0,
            'pendingPayouts' => '0 ₽',
        ];
    }

    private function emptyChartData(): array
    {
        return [
            'labels' => ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
            'datasets' => [[
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
            ]]
        ];
    }
}
