<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopRepairOrderPhoto extends Model
{
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(WorkshopRepairOrder::class, 'workshop_repair_order_id');
    }
}
