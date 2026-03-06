<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'created_at',
        'updated_at',
        'remember_token',
        'email_verified_at',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar'];
    
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
        ];
    }
    
    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN->value;
    }
    
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    
    public function cyclingActivities(): HasMany
    {
        return $this->hasMany(CyclingActivity::class);
    }
    
    public function cyclingOrders(): HasMany
    {
        return $this->hasMany(CyclingOrder::class);
    }
    
    public function hasCyclingActivitiesLeft(): bool
    {
        return $this->paid_cycling_count > 0;
    }
    
    public function isCoffeeRider(): bool
    {
        return $this->is_coffeerider;
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->avatar_url;
    }

    public function participatingRaces(): BelongsToMany
    {
        return $this->belongsToMany(Race::class, 'races_users')->withTimestamps();
    }
}
