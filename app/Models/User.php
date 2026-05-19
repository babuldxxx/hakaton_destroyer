<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'nickname', 'email', 'password', 'label_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function artist(): HasOne
    {
        return $this->hasOne(Artist::class);
    }

    // Методы из ветки fix/registration-role
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function balance(): float
    {
        // Сколько начислено, но ещё не выплачено
        return $this->transactions()
            ->whereIn('status', ['pending', 'on_hold'])
            ->sum('amount');
    }

    public function totalEarned(): float
    {
        return $this->transactions()->sum('amount');
    }
}