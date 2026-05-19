<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Earning extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'platform_id',
        'royalty_report_id',
        'created_by',
        'period',
        'gross_amount',
        'label_share_percent',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'label_share_percent' => 'decimal:2',
        ];
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function royaltyReport(): BelongsTo
    {
        return $this->belongsTo(RoyaltyReport::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}