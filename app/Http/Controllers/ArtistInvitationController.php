<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\ArtistInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ArtistInvitationController extends Controller
{
    /** Лейбл: ростер + инвайты */
    public function index()
    {
        $user = auth()->user();
        $role = is_string($user->role) ? $user->role : ($user->role->value ?? null);

        if ($role !== 'label') {
            abort(403);
        }

        return Inertia::render('Artists/Index', [
            'artists' => Artist::where('label_id', $user->id)
                ->with('user')
                ->latest()
                ->get(),

            'invitations' => ArtistInvitation::where('label_id', $user->id)
                ->latest()
                ->get(),
        ]);
    }

    /** Лейбл: отправить приглашение */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $label = auth()->user();
        $email = strtolower(trim($request->email));

        if ($email === strtolower($label->email)) {
            return back()->with('error', 'Нельзя пригласить самого себя');
        }

        $alreadyArtist = Artist::whereHas('user', fn ($q) => $q->where('email', $email))
            ->where('label_id', $label->id)
            ->exists();

        if ($alreadyArtist) {
            return back()->with('error', 'Этот артист уже в вашем лейбле');
        }

        $exists = ArtistInvitation::where('label_id', $label->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Приглашение уже отправлено');
        }

        $targetUser = User::where('email', $email)->first();

        ArtistInvitation::create([
            'label_id'       => $label->id,
            'artist_user_id' => $targetUser?->id,
            'email'          => $email,
            'token'          => Str::random(32),
            'status'         => 'pending',
            'expires_at'     => now()->addDays(7),
        ]);

        return back()->with('success', 'Приглашение отправлено');
    }

    /** Лейбл: отменить приглашение */
    public function destroy(ArtistInvitation $invitation)
    {
        if ($invitation->label_id !== auth()->id()) {
            abort(403);
        }

        $invitation->delete();

        return back()->with('success', 'Приглашение отменено');
    }

    /** Артист: страница приглашения */
    public function show(string $token)
    {
        $invitation = ArtistInvitation::with('label:id,name,email')
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->status !== 'pending') {
            return Inertia::render('Invitations/Show', [
                'invitation' => $invitation,
                'expired'    => false,
                'message'    => $invitation->status === 'accepted'
                    ? 'Приглашение уже принято.'
                    : 'Приглашение отменено или отклонено.',
            ]);
        }

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return Inertia::render('Invitations/Show', [
                'invitation' => $invitation,
                'expired'    => true,
                'message'    => 'Срок действия приглашения истёк.',
            ]);
        }

        return Inertia::render('Invitations/Show', [
            'invitation' => $invitation,
            'expired'    => false,
            'message'    => null,
        ]);
    }

    /** Артист: принять */
    public function accept(string $token)
    {
        $invitation = ArtistInvitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return back()->with('error', 'Приглашение истекло');
        }

        $user = auth()->user();

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403);
        }

        \DB::transaction(function () use ($user, $invitation) {
            $artist = Artist::where('user_id', $user->id)->first();

            if ($artist && $artist->label_id && $artist->label_id !== $invitation->label_id) {
                throw new \Exception('Вы уже состоите в другом лейбле');
            }

            if (! $artist) {
                Artist::create([
                    'user_id'    => $user->id,
                    'label_id'   => $invitation->label_id,
                    'stage_name' => $user->name,
                    'real_name'  => $user->name,
                ]);
            } else {
                $artist->update(['label_id' => $invitation->label_id]);
            }

            $invitation->update([
                'status'         => 'accepted',
                'artist_user_id' => $user->id,
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Вы присоединились к лейблу');
    }

    /** Артист: отклонить */
    public function decline(string $token)
    {
        $invitation = ArtistInvitation::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $user = auth()->user();

        if (strtolower($user->email) !== strtolower($invitation->email)) {
            abort(403);
        }

        $invitation->update(['status' => 'declined']);

        return redirect()->route('dashboard')->with('success', 'Приглашение отклонено');
    }
}