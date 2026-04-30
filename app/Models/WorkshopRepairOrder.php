<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkshopRepairOrder extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_WORK = 'in_work';
    public const STATUS_FINISHED = 'finished';

    public const AVAILABLE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_WORK,
        self::STATUS_FINISHED,
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'finished_at' => 'datetime',
            'mechanic_income_rub' => 'integer',
            'workshop_works_income_rub' => 'integer',
            'workshop_spare_parts_income_rub' => 'integer',
            'workshop_income_rub' => 'integer',
        ];
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkshopService::class,
            'workshop_repair_order_services'
        )->withPivot('price_rub', 'mechanic_income_rub', 'workshop_income_rub')->withTimestamps();
    }

    public function spareParts(): BelongsToMany
    {
        return $this->belongsToMany(
            WorkshopSparePart::class,
            'workshop_repair_order_spare_parts'
        )
            ->wherePivot('external', false)
            ->withPivot('quantity', 'purchase_price_rub', 'sale_price_rub')
            ->withTimestamps();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(WorkshopRepairOrderPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function sparePartItems(): HasMany
    {
        return $this->hasMany(WorkshopRepairOrderSparePart::class)
            ->orderBy('id');
    }
}
