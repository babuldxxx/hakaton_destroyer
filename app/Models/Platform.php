<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ДОБАВИЛИ ИМПОРТ

class Platform extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'code', 'icon', 'is_active'];

    public function earnings(): HasMany
    {
        return $this->hasMany(SongPlatformEarning::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ДОБАВИЛИ ОБРАТНУЮ СВЯЗЬ С ТРЕКАМИ
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'song_platform');
    }
}