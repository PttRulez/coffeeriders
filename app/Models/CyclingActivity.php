<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CyclingActivity extends Model
{
    protected $guarded = [];
    
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
}