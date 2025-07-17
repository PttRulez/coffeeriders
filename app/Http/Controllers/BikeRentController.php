<?php

namespace App\Http\Controllers;

use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Services\BikeRentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BikeRentController extends Controller
{
    public function __construct(private readonly BikeRentService $bikeRentService){}
    
    public function index(): Response
    {
        return Inertia::render('rent-bikes/Index', [
            'bikes' => $this->bikeRentService->getAllBikes()->toArray()
        ]);
    }
    
    public function show(Bike $bike): Response
    {
        return Inertia::render('rent-bikes/Show', [
            'bike' => fn () => BikeResource::make($bike)->resolve(),
        ]);
    }
}
