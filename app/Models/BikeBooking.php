<?php

namespace App\Models;

use App\Enums\BookingStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeBooking extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'starts_at' => 'date:Y-m-d',
        'ends_at'   => 'date:Y-m-d',
        'status'    => BookingStatusEnum::class,
    ];
    
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }

    public function scopeCurrentOrUpcoming(Builder $query): Builder
    {
        return $query->whereDate('ends_at', '>=', today());
    }

    public function scopeChronological(Builder $query): Builder
    {
        return $query
            ->orderBy('starts_at')
            ->orderBy('id');
    }
}
