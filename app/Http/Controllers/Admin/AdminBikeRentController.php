<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBikeRequest;
use App\Models\Bike;
use App\Services\BikeRentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Storage;

class AdminBikeRentController extends Controller
{
    public function __construct(private readonly BikeRentService $bikeRentService)
    {
    }
    
    public function index(): Response
    {
        return Inertia::render('adminka/rent-bikes/Index', [
            'bikes' => $this->bikeRentService->getAllBikes(),
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('adminka/rent-bikes/Create');
    }
    
    public function store(CreateBikeRequest $request): RedirectResponse
    {
        $v = $request->validated();
        
        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $path = $request->file('img')?->store('uploads', 'public');
            $url = Storage::url($path);
            $v['img_url'] = $url;
        }
        unset($v['img']);
        
        Bike::create($v);
        
        return to_route('adminka.rent-bikes.index');
    }
    
    public function edit(Bike $bike): Response
    {
        return Inertia::render('adminka/rent-bikes/Edit', [
            'bike' => $bike,
        ]);
    }
    
    public function update(CreateBikeRequest $request, Bike $bike): RedirectResponse
    {
        $v = $request->validated();
        
        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $path = $request->file('img')?->store('uploads', 'public');
            $url = Storage::url($path);
            $v['img_url'] = $url;
            
            if ($bike->img_url && Storage::disk('public')->exists($bike->img_url)) {
                Storage::disk('public')->delete($bike->img_url);
            }
        }
        unset($v['img']);
        
        $bike->update($v);
        
        return to_route('adminka.rent-bikes.index');
    }
}
