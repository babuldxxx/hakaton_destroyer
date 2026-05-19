<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'label_id', 'stage_name', 'real_name', 'bio', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function songAuthors(): HasMany
    {
        return $this->hasMany(SongAuthor::class);
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'song_authors');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function balance(): float
    {
        return $this->transactions()
            ->whereIn('status', ['pending', 'on_hold'])
            ->sum('amount');
    }
}