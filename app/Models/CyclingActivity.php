<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CyclingActivity extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'starts_at' => 'datetime:Y-m-d H:i',
        'ends_at'   => 'datetime:Y-m-d H:i',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function cyclingStation(): BelongsTo
    {
        return $this->belongsTo(CyclingStation::class);
    }
    
    public function couponUsage(): MorphOne
    {
        return $this->morphOne(CouponUsage::class, 'orderable');
    }
    
    public function getAppliedCouponCodeAttribute(): ?string
    {
        return $this->couponUsage?->coupon_code;
    }
    
    public function getFinalPriceAttribute(): ?int
    {
        return $this->couponUsage?->final_price;
    }
    
    public function getAppliedDiscountAttribute(): ?int
    {
        return $this->couponUsage?->applied_discount;
    }
    
    //    SCOPES:
    public function scopeOverlaps($query, $start, $end) {
        return $query->where(function ($q) use ($start, $end) {
            $q->where('ends_at', '>', $start)
                ->where('starts_at', '<', $end);
        });
    }
}