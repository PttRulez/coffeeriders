<?php

namespace App\Http\Controllers;

use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Services\BikeRentService;
use Inertia\Inertia;
use Inertia\Response;

class BikeController extends Controller
{
    public function __construct(private readonly BikeRentService $bikeRentService){}

    public function index(): Response
    {
        return Inertia::render('rent-bikes/Index', [
            'bikes' => $this->bikeRentService->getPublishedBikes()
        ]);
    }

    public function category(string $categoryName): Response
    {
        return Inertia::render('rent-bikes/' . ucfirst($categoryName), [
            'bikes' => $this->bikeRentService->getPublishedByCategoryName($categoryName)->toArray()
        ]);
    }

    public function show(Bike $bike): Response
    {
        abort_unless($bike->is_published, 404);

        return Inertia::render('rent-bikes/Show', [
            'bike' => fn () => BikeResource::make($bike)->resolve(),
        ]);
    }
}
