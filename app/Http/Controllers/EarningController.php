<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Earning;
use App\Services\RoyaltyCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EarningController extends Controller
{
    public function store(Request $request, Song $song)
    {
        if (auth()->id() !== $song->label_id && auth()->user()?->role !== 'admin') {
            abort(403, 'Только лейбл может добавлять доходы.');
        }

        $validated = $request->validate([
            'platform_id'         => 'required|exists:platforms,id',
            'period'              => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'gross_amount'        => 'required|numeric|min:0.01',
            'label_share_percent' => 'nullable|numeric|between:0,100',
        ]);

        $earning = Earning::create([
            'song_id'             => $song->id,
            'platform_id'         => $validated['platform_id'],
            'period'              => $validated['period'],
            'gross_amount'        => $validated['gross_amount'],
            'label_share_percent' => $validated['label_share_percent'] ?? 0,
            'created_by'          => auth()->id(),
            'status'              => 'pending',
        ]);

        app(RoyaltyCalculator::class)->distribute($earning);

        // Возвращаем на страницу трека (Inertia redirect)
        return redirect()->route('songs.show', $song)
            ->with('success', 'Доход добавлен и распределён между участниками.');
    }
}

