<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongPlatformEarning extends Model
{
    use HasFactory;

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

    public function definition(): array
    {
        return [
            'song_id' => \App\Models\Song::factory(),
            'platform_id' => \App\Models\Platform::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'currency' => 'RUB',
            'period_start' => fake()->date(),
            'period_end' => fake()->date(),
        ];
    }
}
