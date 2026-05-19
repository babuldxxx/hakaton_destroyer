<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                    'role'  => $request->user()->role instanceof \BackedEnum
                        ? $request->user()->role->value
                        : $request->user()->role,
                ] : null,
            ],
            'pendingInvitation' => function () use ($request) {
                $user = $request->user();

                if (! $user) {
                    return null;
                }

                $role = is_string($user->role) ? $user->role : ($user->role->value ?? null);

                if ($role !== 'artist') {
                    return null;
                }

                return \App\Models\ArtistInvitation::where('email', strtolower($user->email))
                    ->where('status', 'pending')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                    ->with('label:id,name')
                    ->latest()
                    ->first();
            },
        ]);
    }
}