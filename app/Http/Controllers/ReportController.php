<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Label;
use App\Models\Platform;
use App\Models\Song;
use App\Models\SongAuthor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $label = Label::find($user->label_id);

        if (!$label) {
            abort(403, 'У вас нет привязанного лейбла.');
        }

        // Получаем фильтры
        $period = $request->input('period', 'month');
        $artistId = $request->input('artist', 'all');
        $platformId = $request->input('platform', 'all');

        // Базовый запрос: доходы по трекам лейбла
        $query = Earning::whereHas('song', fn($q) => $q->where('label_id', $label->id))
            ->with(['song.artists', 'platform']);

        // Фильтр по артисту
        if ($artistId !== 'all') {
            $query->whereHas('song.songAuthors', fn($q) => $q->where('artist_id', $artistId));
        }

        // Фильтр по площадке
        if ($platformId !== 'all') {
            $query->where('platform_id', $platformId);
        }

        // Фильтр по периоду
        $now = now();
        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()]);
                break;
            // 'year' — без фильтра
        }

        $earnings = $query->get();

        // Статистика
        $total = $earnings->sum('gross_amount');
        $tracksCount = $earnings->pluck('song_id')->unique()->count();
        $average = $tracksCount > 0 ? $total / $tracksCount : 0;

        // Строки отчёта
        $reportRows = $earnings->map(function ($earning) {
            $song = $earning->song;
            $artist = $song->artists->first();
            $authorsShare = $song->songAuthors->sum('share_percentage');

            return [
                'artist'      => $artist?->stage_name ?? 'Неизвестен',
                'track'       => $song->title,
                'platform'    => $earning->platform?->name ?? '—',
                'revenue'     => $earning->gross_amount,
                'authorShare' => $authorsShare > 0 ? round(100 - $earning->label_share_percent, 2) : 100,
                'labelShare'  => (float) $earning->label_share_percent,
            ];
        });

        // Списки для фильтров
        $artists = Artist::where('label_id', $label->id)
            ->where('status', 'approved')
            ->select('id', 'stage_name')
            ->get();

        $platforms = Platform::select('id', 'name')->get();

        return Inertia::render('Reports/Index', [
            'stats' => [
                'total'   => round($total, 2),
                'average' => round($average, 2),
                'tracks'  => $tracksCount,
            ],
            'artists'     => $artists,
            'platforms'   => $platforms,
            'reportRows'  => $reportRows,
            'filters'     => [
                'period'   => $period,
                'artist'   => $artistId,
                'platform' => $platformId,
            ],
        ]);
    }
}
