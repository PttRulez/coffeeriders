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
        return Bike::with('images')->get()->each(function ($bike) {
            $bike['title_img'] = $bike->images->filter(function ($image) {
                return $image->is_primary;
            })->first()->url ?? '';
        });
    }
    
    function getByCategoryName(string $categoryName): Collection
    {
        return Bike::where('category', $categoryName)->get();
    }
}