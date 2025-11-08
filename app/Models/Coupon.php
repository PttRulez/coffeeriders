<?php

namespace App\Models;

use App\Enums\ServiceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'service_type',
        'is_active',
        'starts_at',
        'ends_at',
        'max_uses',
        'max_uses_per_user',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'service_type' => ServiceType::class,
    ];

    // 👇 Связь с применениями купона
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // 🔹 Скоуп для активных купонов
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    // 🔹 Скоуп для фильтрации по типу услуги
    public function scopeService(Builder $query, string $service): Builder
    {
        return $query->where(function ($q) use ($service) {
            $q->whereNull('service_type')
              ->orWhere('service_type', $service);
        });
    }

    // 🔹 Проверка, можно ли применить купон пользователю
    public function isValidForUser(int $userId): bool
    {
        if (! $this->is_active) return false;

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        if ($this->max_uses !== null && $this->usages()->count() >= $this->max_uses) {
            return false;
        }

        if ($this->max_uses_per_user !== null &&
            $this->usages()->where('user_id', $userId)->count() >= $this->max_uses_per_user) {
            return false;
        }

        return true;
    }

    public function applyDiscount(int $price): int
    {
        if ($this->discount_type === 'fixed') {
            return max(0, $price - $this->discount_value);
        }
        return max(0, (int) round($price * (1 - $this->discount_value / 100)));
    }
}