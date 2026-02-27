<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopCategory extends Model
{
    protected $guarded = [];

    public function services(): HasMany
    {
        return $this->hasMany(WorkshopService::class);
    }
}
