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
        if (auth()->user()->hasRole('label')) {
            $labelId = auth()->user()->label_id;

            // Артисты лейбла, у которых есть pending-транзакции
            $artists = Artist::select('artists.id', 'users.name as artist_name', 'artists.user_id')
                ->selectRaw('COALESCE(SUM(CASE WHEN transactions.status = ? AND transactions.type = ? THEN transactions.amount ELSE 0 END), 0) as pending_amount', ['pending', 'author_royalty'])
                ->selectRaw('COUNT(CASE WHEN transactions.status = ? AND transactions.type = ? THEN 1 END) as pending_count', ['pending', 'author_royalty'])
                ->leftJoin('users', 'users.id', '=', 'artists.user_id')
                ->leftJoin('transactions', 'transactions.artist_id', '=', 'artists.id')
                ->where('artists.label_id', $labelId)
                ->where('artists.status', 'approved')
                ->groupBy('artists.id', 'users.name', 'artists.user_id')
                ->havingRaw('pending_amount > 0')
                ->get();

            // История выплат (без пагинации для простоты, можно вернуть paginate)
            $payouts = Payout::whereHas('artist', fn($q) => $q->where('label_id', $labelId))
                ->with(['artist.user'])
                ->latest()
                ->get();

            return Inertia::render('Payouts/Index', [
                'artists' => $artists,
                'payouts' => $payouts,
            ]);
        }

        // Артист
        $artist = Artist::where('user_id', auth()->id())->first();
        if (!$artist) abort(403);

        $stats = [
            'balance'      => Transaction::where('artist_id', $artist->id)->where('status', 'pending')->sum('amount'),
            'total_earned' => Transaction::where('artist_id', $artist->id)->sum('amount'),
            'total_paid'   => Payout::where('artist_id', $artist->id)->where('status', 'completed')->sum('amount'),
        ];

        $transactions = Transaction::with('earning.song')
            ->where('artist_id', $artist->id)
            ->latest()
            ->get();

        $payouts = Payout::where('artist_id', $artist->id)
            ->latest()
            ->get();

        return Inertia::render('Payouts/Index', [
            'stats'        => $stats,
            'transactions' => $transactions,
            'payouts'      => $payouts,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureLabel();

        if ($request->input('artist_id') === 'all') {
            $labelId = auth()->user()->label_id;
            $artists = Artist::where('label_id', $labelId)->get();
            foreach ($artists as $artist) {
                $this->createPayoutForArtist($artist->id);
            }
            return redirect()->route('payouts.index')->with('success', 'Выплаты созданы для всех артистов.');
        }

        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'method'    => 'nullable|string|max:255',
            'details'   => 'nullable|string',
        ]);

        $this->createPayoutForArtist($validated['artist_id']);
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

    private function createPayoutForArtist(int $artistId): void
    {
        DB::transaction(function () use ($artistId) {
            $transactions = Transaction::where('artist_id', $artistId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if ($transactions->isEmpty()) {
                return;
            }

            $total = $transactions->sum('amount');

            $payout = Payout::create([
                'artist_id'  => $artistId,
                'amount'     => $total,
                'currency'   => 'RUB',
                'status'     => 'pending',
                'method'     => 'bank',
                'created_by' => auth()->id(),
            ]);

            foreach ($transactions as $tx) {
                $tx->update([
                    'payout_id' => $payout->id,
                    'status'    => 'on_hold',
                ]);
            }
        });
    }

    private function ensureLabel(): void
    {
        if (!auth()->user()->hasRole('label')) {
            abort(403);
        }
    }
}
