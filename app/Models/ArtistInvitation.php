<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistInvitation extends Model
{
    protected $fillable = [
        'label_id',
        'artist_user_id',
        'email',
        'token',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function label()
    {
        return $this->belongsTo(User::class, 'label_id');
    }

    public function artistUser()
    {
        return $this->belongsTo(User::class, 'artist_user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}