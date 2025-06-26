<?php

namespace App\Http\Controllers;

use App\Services\BikeRentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BikeRentController extends Controller
{
    public function index(): Response
    {
        $rentService = new BikeRentService();
        
        return Inertia::render('rent-bikes/Index', [
            'bikes' => $rentService->getAllBikes()->toArray()
        ]);
    }
}
