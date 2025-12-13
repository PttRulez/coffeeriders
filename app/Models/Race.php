<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_published' => 'boolean',
    ];

    public function clusters(): HasMany
    {
        return $this->hasMany(RaceCluster::class)->orderBy('start_time');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today());
    }
}
