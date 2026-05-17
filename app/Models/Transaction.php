<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'artist_id', 'song_id', 'platform_id', 'order_id', 'type', 'amount', 'description'];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function customOrder(): BelongsTo
    {
        return $this->belongsTo(CustomOrder::class, 'order_id');
    }
}