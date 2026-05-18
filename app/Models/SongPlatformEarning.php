<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongPlatformEarning extends Model
{
    protected $fillable = [
        'song_id',
        'platform_id',
        'amount',
        'currency',
        'period_start',
        'period_end',
        'reported_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'reported_at'  => 'datetime',
        'amount'       => 'decimal:2',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}