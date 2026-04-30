<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopSparePartPurchase extends Model
{
    protected $guarded = [];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(WorkshopSparePart::class, 'workshop_spare_part_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
