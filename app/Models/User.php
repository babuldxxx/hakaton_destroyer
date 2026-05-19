<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property UserRole $role
 * @property int|null $label_id
 * @property-read Label|null $label
 * @property-read Artist|null $artist
 */
#[Fillable(['name', 'nickname', 'email', 'password', 'role', 'label_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
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

    public function transactions()
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
