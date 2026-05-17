<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = ['artist_id', 'amount', 'method', 'status', 'paid_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PayoutStatus::class,
        'paid_at' => 'datetime',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}