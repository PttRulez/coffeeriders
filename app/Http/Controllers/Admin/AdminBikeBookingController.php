<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BikeBooking;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminBikeBookingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('adminka/rent-bikes/Bookings', [
            'bookings' => BikeBooking::with('bike')->get()
        ]);
    }
    
    public function destroy(BikeBooking $bikeBooking): RedirectResponse
    {
        $bikeBooking->delete();
        
        return back()->with('success', 'Бронь удалена');
    }
}