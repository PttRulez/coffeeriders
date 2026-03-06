<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'is_published' => 'boolean',
        'in_our_studio' => 'boolean',
    ];

    public function clusters(): HasMany
    {
        return $this->hasMany(RaceCluster::class)->orderBy('start_time');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'races_users')
            ->where('is_coffeerider', true)
            ->withTimestamps()
            ->orderBy('name');
    }
    
    public function scopeInOurStudio($query)
    {
        return $query->where('in_our_studio', true);
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
