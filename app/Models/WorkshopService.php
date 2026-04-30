<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkshopService extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WorkshopCategory::class, 'workshop_category_id');
    }

    public function repairOrders(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkshopRepairOrder::class,
            'workshop_repair_order_services'
        )->withPivot('price_rub', 'mechanic_income_rub', 'workshop_income_rub')->withTimestamps();
    }
}
