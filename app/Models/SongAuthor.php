<?php

namespace App\Models;

use App\Enums\RightsType;
use App\Enums\SongAuthorRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongAuthor extends Model
{
    use HasFactory;

    protected $fillable = ['song_id', 'artist_id', 'role', 'share_percentage', 'rights_type'];

    protected $casts = [
        'role' => SongAuthorRole::class,
        'rights_type' => RightsType::class,
        'share_percentage' => 'decimal:2',
    ];

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}