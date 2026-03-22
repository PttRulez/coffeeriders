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
        return $this->addTitleImage(Bike::with('images')->get());
    }

    /**
     * @return Collection<int, Bike>
     */
    function getPublishedBikes(): Collection
    {
        return $this->addTitleImage(
            Bike::where('is_published', true)->with('images')->get()
        );
    }
    
    function getByCategoryName(string $categoryName): Collection
    {
        return $this->addTitleImage(
            Bike::where('category', $categoryName)->with('images')->get()
        );
    }

    /**
     * @return Collection<int, Bike>
     */
    function getPublishedByCategoryName(string $categoryName): Collection
    {
        return $this->addTitleImage(
            Bike::where('category', $categoryName)
                ->where('is_published', true)
                ->with('images')
                ->get()
        );
    }

    /**
     * @param Collection<int, Bike> $bikes
     * @return Collection<int, Bike>
     */
    private function addTitleImage(Collection $bikes): Collection
    {
        return $bikes->each(function ($bike) {
            $bike['title_img'] = $bike->images->filter(function ($image) {
                return $image->is_primary;
            })->first()->url ?? '';
        });
    }
}
