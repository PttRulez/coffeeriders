<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBikeRequest;
use App\Http\Resources\BikeResource;
use App\Models\Bike;
use App\Models\BikeImage;
use App\Services\BikeRentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Services\ImageService;
use Inertia\Inertia;
use Inertia\Response;
use Storage;

class AdminBikeController extends Controller
{
    public function __construct(private readonly BikeRentService $bikeRentService)
    {
    }
    
    public function destroyImage(Bike $bike, BikeImage $image)
    {
        abort_unless($image->bike_id === $bike->id, 404);
        
        if ($image->url && Storage::disk('public')->exists($bike->url)) {
            Storage::disk('public')->delete($image->url);
        }
        $image->delete();
        
        if (!$bike->primaryImage && $bike->images()->exists()) {
            $bike->images()->first()->update(['is_primary' => true]);
        }
        
        return back();
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
    
    public function show(Bike $bike): Response
    {
        return Inertia::render('adminka/rent-bikes/Show', [
            'bike' => fn() => BikeResource::make($bike)->resolve(),
        ]);
    }
    
    public function store(CreateBikeRequest $request, ImageService $imgService): RedirectResponse
    {
        $v = $request->validated();
        
        DB::transaction(function () use ($request, $v, $imgService) {
            unset($v['images']);
            $bike = Bike::create($v);
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $i => $file) {
                    if (!$file->isValid()) {
                        continue;
                    }
                    
                    $url = $imgService->save($file);
                    
                    $bike->images()->create([
                        'url' => $url,
                        'alt' => $bike->name,
                        'sort' => $i,
                        'is_primary' => $i === 0,
                    ]);
                }
            }
        });
        
        return to_route('adminka.rent-bikes.index');
    }
    
    public function edit(Bike $bike): Response
    {
        return Inertia::render('adminka/rent-bikes/Edit', [
            'bike' => $bike,
        ]);
    }
    
    public function update(CreateBikeRequest $request, Bike $bike, ImageService $imgService): RedirectResponse
    {
        $v = $request->validated();
        
        DB::transaction(function () use ($request, $bike, $v, $imgService) {
            unset($v['images']);
            $bike->update($v);
            
            if ($request->hasFile('images')) {
                $start = (int)$bike->images()->max('sort');
                
                foreach ($request->file('images') as $i => $file) {
                    if (!$file->isValid()) continue;
                    
                    $url = $imgService->save($file);
                    
                    $bike->images()->create([
                        'url' => $url,
                        'alt' => $bike->name,
                        'sort' => $start + $i + 1,
                        'is_primary' => false,
                    ]);
                }
            }
        });
        
        return to_route('adminka.rent-bikes.index');
    }
}
