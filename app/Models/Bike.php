<?php

namespace App\Models;

use App\Enums\BikeCategory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    protected $table = 'bikes';
    
    protected $guarded = [];
    
    protected $casts = [
        'prices' => 'array',
        'category' => BikeCategory::class,
    ];
}
