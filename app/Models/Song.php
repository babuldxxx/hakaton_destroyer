<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Song extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'lyrics', 'written_at', 'released_at', 'label_id', 'wav_path', 'mp3_path', 'isrc', 'genre_id'];

    protected $casts = [
        'written_at' => 'date',
        'released_at' => 'date',
    ];

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function songAuthors(): HasMany
    {
        return $this->hasMany(SongAuthor::class);
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'song_authors');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(SongPlatformEarning::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function customOrders(): HasMany
    {
        return $this->hasMany(CustomOrder::class);
    }
}