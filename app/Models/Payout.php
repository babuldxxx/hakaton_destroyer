<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'artist_id', 'amount', 'currency', 'status',
        'paid_at', 'method', 'details', 'created_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}