<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponStoreRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminCouponController extends Controller
{
    public function index(): \Inertia\Response
    {
        $coupons = \App\Models\Coupon::query()
            ->withCount(['usages as total_usages'])
            ->latest('id')
            ->get(); // без пагинации и фильтров
        
        return \Inertia\Inertia::render('adminka/coupons/Index', [
            'items' => $coupons,
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('adminka/coupons/Form', [
            'item' => null,
            'enums' => [
                'services' => ['cycling', 'bike_rent'],
                'types' => ['percent', 'fixed'],
            ],
        ]);
    }
    
    public function store(CouponStoreRequest $request)
    {
        Coupon::create($request->validated());
        return redirect()->route('adminka.coupons.index')
            ->with('success', 'Купон создан');
    }
    
    public function edit(Coupon $coupon): Response
    {
        $coupon->loadCount(['usages as total_usages']);
        return Inertia::render('adminka/coupons/Form', [
            'item' => $coupon,
            'enums' => [
                'services' => ['cycling', 'bike_rent'],
                'types' => ['percent', 'fixed'],
            ],
        ]);
    }
    
    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());
        return redirect()->route('adminka.coupons.index')
            ->with('success', 'Купон обновлён');
    }
    
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Купон удалён');
    }
    
    public function toggleActive(\App\Models\Coupon $coupon)
{
    $coupon->update([
        'is_active' => ! $coupon->is_active,
    ]);

    return response()->json([
        'ok' => true,
        'new_state' => $coupon->is_active,
    ]);
}
}