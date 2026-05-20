<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\Invitation;
use App\Models\User;

class ArtistPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('label') || $user->hasRole('artist');
    }

    public function view(User $user, Artist $artist): bool
    {
        if ($user->hasRole('label')) {
            return $artist->label_id === $user->label_id;
        }
        if ($user->hasRole('artist')) {
            return $artist->user_id === $user->id;
        }
        return false;
    }

    public function update(User $user, Artist $artist): bool
    {
        return $user->hasRole('label') && $artist->label_id === $user->label_id;
    }

    public function delete(User $user, Artist $artist): bool
    {
        return $user->hasRole('label') && $artist->label_id === $user->label_id;
    }

    public function invite(User $user, Artist $artist): bool
    {
        return $user->hasRole('label') && is_null($artist->label_id);
    }

    public function respondToInvitation(User $user, Invitation $invitation): bool
    {
        return $user->hasRole('artist') && $user->artist?->id === $invitation->artist_id;
    }
}
