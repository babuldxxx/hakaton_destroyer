<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Invitation;
use App\Models\Label;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    // ---------- просмотр списка ----------
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

    // ---------- карточка артиста ----------
    public function show(Artist $artist)
    {
        $this->authorize('view', $artist);
        $artist->load(['user', 'songs']);

        return Inertia::render('Artists/Show', [
            'artist' => $artist,
        ]);
    }

    // ---------- пригласить артиста (лейбл) ----------
    public function invite(Request $request, Artist $artist)
    {
        $this->authorize('invite', $artist);

        $label = Label::where('id', auth()->user()->label_id)->first();
        if (!$label) {
            abort(403, 'У вас нет привязанного лейбла.');
        }

        $existing = Invitation::where('artist_id', $artist->id)
            ->where('label_id', $label->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'Приглашение уже отправлено.');
        }

        Invitation::create([
            'label_id'  => $label->id,
            'artist_id' => $artist->id,
            'status'    => 'pending',
        ]);

        return back()->with('success', 'Приглашение отправлено артисту.');
    }

    // ---------- список приглашений (артист) ----------
    public function invitations()
    {
        $this->authorize('viewAny', Artist::class);
        $user = auth()->user();

        if (!$user->hasRole('artist')) {
            abort(403);
        }

        $artist = $user->artist;
        if (!$artist) {
            abort(404, 'Профиль артиста не найден.');
        }

        $invitations = Invitation::where('artist_id', $artist->id)
            ->with('label')
            ->latest()
            ->get();

        return Inertia::render('Artists/Invitations', [
            'invitations' => $invitations,
        ]);
    }

    // ---------- принять приглашение (артист) ----------
    public function acceptInvitation(Invitation $invitation)
    {
        $this->authorize('respondToInvitation', $invitation);

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'Приглашение уже обработано.');
        }

        $invitation->update(['status' => 'accepted']);

        $artist = $invitation->artist;
        $artist->update([
            'label_id' => $invitation->label_id,
            'status'   => 'approved',
        ]);

        return redirect()->route('artist.dashboard')
            ->with('success', 'Вы успешно присоединились к лейблу!');
    }

    // ---------- отклонить приглашение (артист) ----------
    public function declineInvitation(Invitation $invitation)
    {
        $this->authorize('respondToInvitation', $invitation);

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'Приглашение уже обработано.');
        }

        $invitation->update(['status' => 'declined']);

        return back()->with('success', 'Приглашение отклонено.');
    }

    // ---------- отвязать артиста от лейбла (бывшее удаление) ----------
    public function destroy(Artist $artist)
    {
        $this->authorize('delete', $artist); // политика detach

        $artist->update([
            'label_id' => null,
            'status'   => 'pending',
        ]);

        // Опционально: отменить активные приглашения от этого лейбла
        Invitation::where('artist_id', $artist->id)
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Артист отвязан от лейбла.');
    }

    // ---------- редактирование (опционально) ----------
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

        return redirect()->route('artists.show', $artist)
            ->with('success', 'Данные обновлены');
    }
}
