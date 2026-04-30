<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopSparePart extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WorkshopSparePartCategory::class, 'workshop_spare_part_category_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(WorkshopSparePartPurchase::class);
    }

    public function repairOrders(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkshopRepairOrder::class,
            'workshop_repair_order_spare_parts'
        )
            ->wherePivot('external', false)
            ->withPivot('quantity', 'purchase_price_rub', 'sale_price_rub')
            ->withTimestamps();
    }

}
