<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Services\RoyaltyCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $label = Label::find($user->label_id);

        if (! $label) {
            return Inertia::render('Reports/Index', [
                'stats'      => ['total' => 0, 'labelTotal' => 0, 'average' => 0, 'tracks' => 0],
                'artists'    => [],
                'platforms'  => [],
                'periods'    => [],
                'reportRows' => [],
                'filters'    => ['period' => 'all', 'artist' => 'all', 'platform' => 'all'],
            ]);
        }

        $periodFilter = $request->input('period', 'all');
        $artistId     = $request->input('artist', 'all');
        $platformId   = $request->input('platform', 'all');

        $query = Earning::query()
            ->where('label_id', $label->id)
            ->with(['song', 'artist', 'platform']);

        if ($periodFilter !== 'all') {
            $now = now();
            match ($periodFilter) {
                'week'    => $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]),
                'month'   => $query->where('period', $now->format('Y-m')),
                'quarter' => $query->whereIn('period', [
                    $now->copy()->startOfQuarter()->format('Y-m'),
                    $now->copy()->startOfQuarter()->addMonth()->format('Y-m'),
                    $now->copy()->startOfQuarter()->addMonths(2)->format('Y-m'),
                ]),
                'year'    => $query->where('period', 'like', $now->format('Y') . '-%'),
                default   => null,
            };
        }

        if ($artistId !== 'all') {
            $query->whereHas('transactions', fn ($q) => $q->where('artist_id', $artistId));
        }

        if ($platformId !== 'all') {
            $query->where('platform_id', $platformId);
        }

        $earnings = $query->orderByDesc('created_at')->get();

        $total = (float) $earnings->sum('gross_amount');

        $labelTotal = (float) $earnings->sum(
            fn ($e) => round((float) $e->gross_amount * ((float) ($e->label_share_percent ?? 0) / 100), 2)
        );

        $tracksCount = $earnings->pluck('song_id')->filter()->unique()->count();
        if ($tracksCount === 0) {
            $tracksCount = $earnings
                ->map(fn ($e) => md5(($e->raw_track_name ?? '') . '|' . ($e->raw_artist_name ?? '')))
                ->unique()
                ->count();
        }

        $average = $tracksCount > 0 ? round($total / $tracksCount, 2) : 0;

        $availablePeriods = Earning::where('label_id', $label->id)
            ->selectRaw('DISTINCT period')
            ->orderByDesc('period')
            ->pluck('period');

        $reportRows = $earnings->map(function ($e) {
            $gross      = (float) $e->gross_amount;
            $labelShare = (float) ($e->label_share_percent ?? 0);

            // Считаем долю артистов: либо сумма из CSV (ArtistShares), либо остаток
            $authorShare = 100 - $labelShare;
            if (! empty($e->artist_shares)) {
                $parts = array_filter(array_map('trim', explode('/', $e->artist_shares)));
                $sum   = array_sum(array_map('floatval', $parts));
                if ($sum > 0) {
                    $authorShare = $sum;
                }
            }

            $labelAmount  = round($gross * ($labelShare / 100), 2);
            $artistAmount = round($gross * ($authorShare / 100), 2);

            return [
                'id'           => $e->id,
                'artist'       => $e->artist?->stage_name ?? $e->raw_artist_name ?? 'Неизвестен',
                'track'        => $e->song?->title        ?? $e->raw_track_name  ?? '—',
                'platform'     => $e->platform?->name     ?? '—',
                'period'       => $e->period,
                'revenue'      => $gross,
                'labelShare'   => $labelShare,
                'labelAmount'  => $labelAmount,
                'artistAmount' => $artistAmount,
                'authorShare'  => $authorShare,
                'artistShares' => $e->artist_shares,
            ];
        });

        return Inertia::render('Reports/Index', [
            'stats' => [
                'total'      => round($total, 2),
                'labelTotal' => round($labelTotal, 2),
                'average'    => $average,
                'tracks'     => $tracksCount,
            ],
            'artists'    => Artist::where('label_id', $label->id)->get(['id', 'stage_name']),
            'platforms'  => Platform::select('id', 'name')->get(),
            'periods'    => $availablePeriods,
            'reportRows' => $reportRows,
            'filters'    => [
                'period'   => $periodFilter,
                'artist'   => $artistId,
                'platform' => $platformId,
            ],
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt']]);

        $user  = $request->user();
        $label = Label::find($user->label_id);
        if (! $label) {
            return back()->with('error', 'Лейбл не найден.');
        }

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        fgetcsv($handle, 0, ','); // пропускаем заголовок

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) < 5) {
                $skipped++;
                continue;
            }

            $trackName    = trim($row[0] ?? '');
            $artistName   = trim($row[1] ?? '');
            $platformName = trim($row[2] ?? '');
            $period       = trim($row[3] ?? '');
            $amountRaw    = trim($row[4] ?? '0');

            if (empty($trackName) || empty($amountRaw)) {
                $skipped++;
                continue;
            }

            $amount = (float) str_replace([' ', ',', "\xc2\xa0"], ['', '.', ''], $amountRaw);

            if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
                $period = now()->format('Y-m');
            }

            $platform = Platform::where('slug', $this->slugify($platformName))
                ->orWhere('name', 'like', '%' . $platformName . '%')
                ->first();

            if (! $platform) {
                Log::warning("CSV Import: площадка не найдена: {$platformName}");
                $skipped++;
                continue;
            }

            $song = Song::where('title', $trackName)
                ->where('label_id', $label->id)
                ->first();

            if (! $song) {
                $song = Song::where('title', 'like', '%' . $trackName . '%')
                    ->where('label_id', $label->id)
                    ->first();
            }

            // Доля лейбла (6-я колонка)
            $labelPercent = 0;
            if (isset($row[5]) && trim($row[5]) !== '') {
                $labelPercent = (float) str_replace(',', '.', trim($row[5]));
            } elseif ($song) {
                $labelPercent = (float) ($song->label_share_percent ?? $label->default_royalty_percent ?? 0);
            }

            // Доли артистов (7-я колонка), например "25/25/20"
            $artistShares = (isset($row[6]) && trim($row[6]) !== '') ? trim($row[6]) : null;

            // Если песня не найдена
            if (! $song) {
                Earning::create([
                    'label_id'            => $label->id,
                    'song_id'             => null,
                    'platform_id'         => $platform->id,
                    'royalty_report_id'   => null,
                    'created_by'          => $label->id,
                    'period'              => $period,
                    'gross_amount'        => $amount,
                    'label_share_percent' => $labelPercent,
                    'artist_shares'       => $artistShares,
                    'raw_track_name'      => $trackName,
                    'raw_artist_name'     => $artistName,
                    'currency'            => 'RUB',
                    'status'              => 'pending',
                ]);

                $imported++;
                continue;
            }

            // Песня найдена
            $earning = Earning::create([
                'label_id'            => $label->id,
                'song_id'             => $song->id,
                'platform_id'         => $platform->id,
                'royalty_report_id'   => null,
                'created_by'          => $label->id,
                'period'              => $period,
                'gross_amount'        => $amount,
                'label_share_percent' => $labelPercent,
                'artist_shares'       => $artistShares,
                'raw_track_name'      => $trackName,
                'raw_artist_name'     => $artistName,
                'currency'            => 'RUB',
                'status'              => 'pending',
            ]);

            try {
                app(RoyaltyCalculator::class)->distribute($earning);
            } catch (\Throwable $e) {
                Log::warning('Royalty calc error earning #' . $earning->id . ': ' . $e->getMessage());
            }

            $imported++;
        }

        fclose($handle);

        return back()->with('success', "Импорт завершён. Добавлено: {$imported}, пропущено: {$skipped}.");
    }

    private function slugify(string $text): string
    {
        return Str::slug($text);
    }
}