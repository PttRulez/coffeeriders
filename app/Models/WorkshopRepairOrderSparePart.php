<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopRepairOrderSparePart extends Model
{
    protected $guarded = [];

    public function repairOrder(): BelongsTo
    {
        return $this->belongsTo(WorkshopRepairOrder::class, 'workshop_repair_order_id');
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(WorkshopSparePart::class, 'workshop_spare_part_id');
    }
}
