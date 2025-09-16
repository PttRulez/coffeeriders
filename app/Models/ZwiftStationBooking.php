<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZwiftStationBooking extends Model
{
    protected $fillable = [
        'zwift_station_id', 'user_id',
        'starts_at', 'ends_at', 'duration_minutes',
        'note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(ZwiftStation::class, 'zwift_station_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
