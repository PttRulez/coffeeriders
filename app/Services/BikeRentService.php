<?php

namespace App\Services;

use App\Enums\BikeCategory;
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
    
    function getByCategoryName(string $categoryName): Collection
    {
        return Bike::where('category', $categoryName)->get();
    }
}