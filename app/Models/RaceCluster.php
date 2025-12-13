<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RaceCluster extends Model
{
    protected $guarded = [];

    public const MAX_SLOTS = 4;

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function cyclingActivities(): HasMany
    {
        return $this->hasMany(CyclingActivity::class);
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->price ?? $this->race->price;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return self::MAX_SLOTS - $this->cyclingActivities()->count();
    }

    public function hasAvailableSlots(): bool
    {
        return $this->available_slots > 0;
    }
}
