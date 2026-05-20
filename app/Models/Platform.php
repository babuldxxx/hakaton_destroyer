<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'code', 'icon', 'is_active'];

    protected static function booted(): void
    {
        static::creating(function ($platform) {
            if (empty($platform->slug)) {
                $platform->slug = Str::slug($platform->name);
            }
        });
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(SongPlatformEarning::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'song_platform');
    }
}