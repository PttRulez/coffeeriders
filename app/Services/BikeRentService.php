<?php

namespace App\Services;

use App\Models\Bike;
use Illuminate\Support\Collection;

class BikeRentService
{
    /**
     * @return Collection<int, Bike>
     */
    function getAllBikes(): Collection
    {
        return Bike::all();
    }
}