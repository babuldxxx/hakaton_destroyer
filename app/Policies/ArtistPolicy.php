<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;

class ArtistPolicy
{
    private function role(User $user): string
    {
        $role = $user->role;
        return $role instanceof \BackedEnum ? (string) $role->value : $role->name;
    }

    /**
     * Может ли пользователь просматривать список артистов.
     */
    public function viewAny(User $user): bool
    {
        $role = $this->role($user);
        return in_array($role, ['label', 'artist']);
    }

    public function view(User $user, Artist $artist): bool
    {
        $role = $this->role($user);

        if ($role === 'label') {
            return $artist->label_id === $user->label_id;
        }

        if ($role === 'artist') {
            return $artist->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->role($user) === 'label';
    }

    public function update(User $user, Artist $artist): bool
    {
        return $this->view($user, $artist) && $this->role($user) === 'label';
    }

    public function delete(User $user, Artist $artist): bool
    {
        return $this->update($user, $artist);
    }
}
