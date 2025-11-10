<?php

namespace App\Services;

use App\Enums\ServiceType;
use App\Models\User;

class PricingService
{
    public function basePrice(User | null $user, ServiceType $service): int
    {
        return match ($service) {
            ServiceType::Cycling  => $user?->is_coffeerider ? 750 : 1500,
            ServiceType::BikeRent => 2000, // задам примерную базовую цену для аренды
        };
    }
}