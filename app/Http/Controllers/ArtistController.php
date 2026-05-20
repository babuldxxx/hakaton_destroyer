<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Earning;
use App\Models\Invitation;
use App\Models\Label;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Artist::class);
        $user = auth()->user();

        if ($user->hasRole('artist')) {
            return redirect()->route('artists.show', $user->artist);
        }

        $label = Label::where('id', $user->label_id)->first();
        if (!$label) {
            abort(403, 'У вас нет привязанного лейбла.');
        }

        $myArtists = Artist::where('label_id', $label->id)
            ->where('status', 'approved')
            ->with('user')
            ->withCount('songs')
            ->get();

        $pendingArtists = Artist::whereNull('label_id')
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return Inertia::render('Artists/Index', [
            'artists'        => $myArtists,
            'pendingArtists' => $pendingArtists,
            'label'          => $label,
        ]);
    }

    public function show(Artist $artist)
    {
        $this->authorize('view', $artist);
        $artist->load(['user', 'songs.genre']);

        // Данные для графика доходов по месяцам (последние 12 месяцев)
        $revenueData = $this->getArtistRevenueData($artist);

        // Данные о доходах по площадкам
        $platformRevenue = Earning::whereHas('song.songAuthors', function ($q) use ($artist) {
            $q->where('artist_id', $artist->id);
        })
            ->with('platform')
            ->get()
            ->groupBy('platform_id')
            ->map(fn ($group) => [
                'platform' => $group->first()->platform->name,
                'total'    => $group->sum('gross_amount'),
            ])
            ->values();

        return Inertia::render('Artists/Show', [
            'artist'        => $artist,
            'revenueData'   => $revenueData,
            'platformRevenue' => $platformRevenue,
        ]);
    }

    /**
     * Готовит данные для графика доходов артиста по месяцам
     */
    private function getArtistRevenueData($artist): array
    {
        $currentYear = now()->year;
        $monthly = \App\Models\Earning::whereHas('song.songAuthors', fn ($q) => $q->where('artist_id', $artist->id))
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(gross_amount) as total')
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

    public function invite(Request $request, Artist $artist)
    {
        $this->authorize('invite', $artist);
        $label = Label::where('id', auth()->user()->label_id)->first();
        if (!$label) abort(403, 'У вас нет привязанного лейбла.');

        $existing = Invitation::where('artist_id', $artist->id)
            ->where('label_id', $label->id)
            ->where('status', 'pending')
            ->first();
        if ($existing) return back()->with('error', 'Приглашение уже отправлено.');

        Invitation::create([
            'label_id'  => $label->id,
            'artist_id' => $artist->id,
            'status'    => 'pending',
        ]);

        return back()->with('success', 'Приглашение отправлено артисту.');
    }

    public function invitations()
    {
        $this->authorize('viewAny', Artist::class);
        $user = auth()->user();
        if (!$user->hasRole('artist')) abort(403);

        $artist = $user->artist;
        if (!$artist) abort(404, 'Профиль артиста не найден.');

        $invitations = Invitation::where('artist_id', $artist->id)
            ->with('label')
            ->latest()
            ->get();

        return Inertia::render('Artists/Invitations', ['invitations' => $invitations]);
    }

    public function acceptInvitation(Invitation $invitation)
    {
        $this->authorize('respondToInvitation', $invitation);
        if ($invitation->status !== 'pending') return back()->with('error', 'Приглашение уже обработано.');

        $invitation->update(['status' => 'accepted']);
        $artist = $invitation->artist;
        $artist->update([
            'label_id' => $invitation->label_id,
            'status'   => 'approved',
        ]);

        return redirect()->route('artist.dashboard')->with('success', 'Вы успешно присоединились к лейблу!');
    }

    public function declineInvitation(Invitation $invitation)
    {
        $this->authorize('respondToInvitation', $invitation);
        if ($invitation->status !== 'pending') return back()->with('error', 'Приглашение уже обработано.');

        $invitation->update(['status' => 'declined']);
        return back()->with('success', 'Приглашение отклонено.');
    }

    public function destroy(Artist $artist)
    {
        $this->authorize('delete', $artist);

        $artist->update(['label_id' => null, 'status' => 'pending']);

        Invitation::where('artist_id', $artist->id)->delete();

        return back()->with('success', 'Артист отвязан от лейбла.');
    }

    public function edit(Artist $artist)
    {
        $this->authorize('update', $artist);
        return Inertia::render('Artists/Edit', ['artist' => $artist]);
    }

    public function update(Request $request, Artist $artist)
    {
        $this->authorize('update', $artist);
        $validated = $request->validate([
            'stage_name' => 'required|string|max:255',
            'real_name'  => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
        ]);
        $artist->update($validated);
        return redirect()->route('artists.show', $artist)->with('success', 'Данные обновлены');
    }
}
