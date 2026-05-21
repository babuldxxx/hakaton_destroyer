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
        'label_id',
        'song_id',
        'platform_id',
        'royalty_report_id',
        'created_by',
        'period',
        'gross_amount',
        'label_share_percent',
        'artist_shares',
        'raw_track_name',
        'raw_artist_name',
        'currency',
        'status',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }
}