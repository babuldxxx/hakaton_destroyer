<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = ['label_id', 'song_id', 'client_name', 'description', 'total_amount', 'label_share_percentage'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'label_share_percentage' => 'decimal:2',
    ];

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'order_id');
    }
}