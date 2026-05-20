<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ─── АРТИСТ ───
        if ($user->hasRole('artist')) {
            $artist = $user->artist;

            if (! $artist) {
                return Inertia::render('Payouts/Index', [
                    'artists'      => [],
                    'payouts'      => [],
                    'stats'        => ['balance' => 0, 'total_earned' => 0, 'total_paid' => 0],
                    'transactions' => [],
                ]);
            }

            $base = Transaction::where('artist_id', $artist->id);

            $balance = (clone $base)
                ->whereIn('type', ['author_rights', 'related_rights'])
                ->where('status', 'pending')
                ->sum('amount');

            $totalEarned = (clone $base)
                ->whereIn('type', ['author_rights', 'related_rights'])
                ->sum('amount');

            $totalPaid = (clone $base)
                ->where('type', 'payout')
                ->where('status', 'completed')
                ->sum('amount'); // отрицательное

            $transactions = (clone $base)
                ->with(['earning.song'])
                ->orderByDesc('created_at')
                ->get();

            return Inertia::render('Payouts/Index', [
                'artists'      => [],
                'payouts'      => [],
                'stats'        => [
                    'balance'      => (float) $balance,
                    'total_earned' => (float) $totalEarned,
                    'total_paid'   => abs((float) $totalPaid),
                ],
                'transactions' => $transactions,
            ]);
        }

        // ─── ЛЕЙБЛ ───
        $labelId = $user->label_id;

        // Суммируем pending начисления по артистам
        $artists = Transaction::query()
            ->whereHas('earning', fn ($q) => $q->where('label_id', $labelId))
            ->whereIn('type', ['author_rights', 'related_rights'])
            ->where('status', 'pending')
            ->with('artist')
            ->get()
            ->groupBy('artist_id')
            ->map(fn ($items, $artistId) => [
                'id'             => (int) $artistId,
                'artist_name'    => $items->first()->artist?->stage_name ?? 'Неизвестно',
                'pending_count'  => $items->count(),
                'pending_amount' => round($items->sum('amount'), 2),
            ])
            ->values();

        // История выплат (type = payout, записано со знаком минус)
        $payouts = Transaction::query()
            ->whereHas('earning', fn ($q) => $q->where('label_id', $labelId))
            ->where('type', 'payout')
            ->with('artist')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Payouts/Index', [
            'artists'      => $artists,
            'payouts'      => $payouts,
            'stats'        => (object) [],
            'transactions' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['artist_id' => 'required|integer']);

        $labelId = $request->user()->label_id;

        DB::transaction(function () use ($request, $labelId) {
            $pendingTx = Transaction::query()
                ->whereHas('earning', fn ($q) => $q->where('label_id', $labelId))
                ->where('artist_id', $request->artist_id)
                ->whereIn('type', ['author_rights', 'related_rights'])
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if ($pendingTx->isEmpty()) {
                abort(422, 'Нет начислений для выплаты');
            }

            $total = round($pendingTx->sum('amount'), 2);

            // Запись о расходе (выплате)
            Transaction::create([
                'user_id'     => auth()->id(),
                'artist_id'   => $request->artist_id,
                'song_id'     => null,
                'platform_id' => null,
                'type'        => 'payout',
                'amount'      => -$total,
                'description' => 'Выплата артисту',
                'status'      => 'completed',
                'period'      => now()->format('Y-m'),
            ]);

            // Закрываем начисления
            Transaction::whereIn('id', $pendingTx->pluck('id'))
                ->update(['status' => 'paid']);
        });

        return redirect()->route('payouts.index')->with('success', 'Выплата выполнена');
    }
}