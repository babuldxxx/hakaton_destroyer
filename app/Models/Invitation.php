<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['label_id', 'artist_id', 'status'];

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
