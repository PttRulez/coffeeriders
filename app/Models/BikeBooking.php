<?php

namespace App\Models;

use App\Enums\BookingStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikeBooking extends Model
{
    protected $fillable = [
        'bike_id',
        'user_id',
        'customer_name',
        'telegram_username',
        'phone',
        'comment',
        'starts_at',
        'ends_at',
        'status',
    ];
    
    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'status'    => BookingStatusEnum::class,
    ];
    
    public function bike(): BelongsTo
    {
        return $this->belongsTo(Bike::class);
    }
}