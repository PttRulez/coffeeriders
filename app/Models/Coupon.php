<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'service_type', 'is_active', 'starts_at', 'ends_at',
    ];

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

    public function applyDiscount(int $price): int
    {
        if ($this->discount_type === 'fixed') {
            return max(0, $price - $this->discount_value);
        }

        // процент
        return max(0, round($price * (1 - $this->discount_value / 100)));
    }
}