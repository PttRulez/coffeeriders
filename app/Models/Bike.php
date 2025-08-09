<?php

namespace App\Models;

use App\Enums\BikeCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bike extends Model
{
    protected $table = 'bikes';
    
    protected $guarded = [];
    
    protected $casts = [
        'prices' => 'array',
        'category' => BikeCategory::class,
    ];
    
    public function bookings(): HasMany
    {
        return $this->hasMany(BikeBooking::class);
    }
}
