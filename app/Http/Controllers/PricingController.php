<?php

namespace App\Http\Controllers;

use App\Enums\ServiceType;
use App\Services\CouponService;
use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function preview(Request $request, PricingService $pricing, CouponService $coupons)
    {
        $data = $request->validate([
            'service' => 'required|string|in:cycling,bike_rent',
            'code'    => 'required|string',
        ]);

        $user    = $request->user();
        $service = ServiceType::from($data['service']);

        $base = $pricing->basePrice($user, $service);
        $res  = $coupons->preview($data['code'], $user, $service, $base);

        if (!$res['ok']) {
            return response()->json(['message' => $res['message']], 422);
        }

        return response()->json([
            'base_price'  => $base,
            'final_price' => $res['new_price'],
            'discount'    => $res['discount'],
        ]);
    }
}