<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Payout;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function index()
    {
        // ---------- ЛЕЙБЛ ----------
        if (auth()->user()->hasRole('label')) {
            $labelId = auth()->user()->label_id;

            $artists = Artist::select('artists.id', 'users.name as artist_name', 'artists.user_id')
                ->selectRaw('COALESCE(SUM(CASE WHEN transactions.status = ? THEN transactions.amount ELSE 0 END), 0) as pending_amount', ['pending'])
                ->selectRaw('COUNT(CASE WHEN transactions.status = ? THEN 1 END) as pending_count', ['pending'])
                ->leftJoin('users', 'users.id', '=', 'artists.user_id')
                ->leftJoin('transactions', 'transactions.artist_id', '=', 'artists.id')
                ->where('artists.label_id', $labelId)
                ->groupBy('artists.id', 'users.name', 'artists.user_id')
                ->havingRaw('pending_amount > 0')
                ->get();

            $payouts = Payout::whereHas('artist', fn ($q) => $q->where('label_id', $labelId))
                ->with(['artist.user'])
                ->latest()
                ->paginate(20);

            return Inertia::render('Payouts/Index', [
                'artists' => $artists,
                'payouts' => $payouts,
            ]);
        }

        // ---------- АРТИСТ ----------
        $artist = Artist::where('user_id', auth()->id())->first();
        if (!$artist) abort(403);

        $stats = [
            'balance'      => Transaction::where('artist_id', $artist->id)->where('status', 'pending')->sum('amount'),
            'total_earned' => Transaction::where('artist_id', $artist->id)->sum('amount'),
            'total_paid'   => Payout::where('artist_id', $artist->id)->where('status', 'paid')->sum('amount'),
        ];

        $transactions = Transaction::with('earning.song')
            ->where('artist_id', $artist->id)
            ->latest()
            ->paginate(20);

        $payouts = Payout::where('artist_id', $artist->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Payouts/Index', [
            'stats'        => $stats,
            'transactions' => $transactions,
            'payouts'      => $payouts,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureLabel();

        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'method'    => 'nullable|string|max:255',
            'details'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $transactions = Transaction::where('artist_id', $validated['artist_id'])
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if ($transactions->isEmpty()) {
                abort(400, 'Нет доступных начислений для выплаты.');
            }

            $total = $transactions->sum('amount');

            $payout = Payout::create([
                'artist_id'  => $validated['artist_id'],
                'amount'     => $total,
                'currency'   => 'RUB',
                'status'     => 'pending',
                'method'     => $validated['method'] ?? 'bank',
                'details'    => $validated['details'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($transactions as $tx) {
                $tx->update([
                    'payout_id' => $payout->id,
                    'status'    => 'on_hold',
                ]);
            }
        });

        return redirect()->route('payouts.index')->with('success', 'Выплата создана.');
    }

    public function markPaid(Payout $payout)
    {
        $this->ensureLabel();

        DB::transaction(function () use ($payout) {
            $payout->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            $payout->transactions()->update(['status' => 'paid']);
        });

        return back()->with('success', 'Выплата подтверждена.');
    }

    private function ensureLabel(): void
    {
        if (!auth()->user()->hasRole('label')) {
            abort(403);
        }
    }
}
