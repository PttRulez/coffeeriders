<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BikeBooking;
use Illuminate\Http\RedirectResponse;

class AdminBikeBookingController extends Controller
{
    public function destroy(BikeBooking $bikeBooking): RedirectResponse
    {
        $bikeBooking->delete();
        
         return redirect()->back();
    }
}