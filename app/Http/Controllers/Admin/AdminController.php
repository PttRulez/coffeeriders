<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BikeBooking;
use Inertia\Response;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('adminka/Index', [
            'bookings' => BikeBooking::with('bike')->get()
        ]);
    }
}
