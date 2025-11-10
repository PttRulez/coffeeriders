<?php

namespace App\Services;

use App\Enums\ServiceType;
use App\Models\User;

class PricingService
{
    public function baseCyclingPrice(User | null $user): int
    {
        return $user?->is_coffeerider ? 750 : 1500;
    }
}