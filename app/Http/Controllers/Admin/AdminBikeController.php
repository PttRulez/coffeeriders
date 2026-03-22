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

class AdminBikeController extends Controller
{
    public function __construct(private readonly BikeRentService $bikeRentService)
    {
    }
    
    public function destroyImage(Bike $bike, BikeImage $image, ImageService $imageService)
    {
        abort_unless($image->bike_id === $bike->id, 404);

        DB::transaction(function () use ($bike, $image, $imageService) {
            if ($image->url) {
                $imageService->delete($image->url);
            }

            $image->delete();

            if (!$bike->images()->where('is_primary', true)->exists()) {
                $newPrimary = $bike->images()->orderBy('sort')->first();

                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }
        });

        return back();
    }

    public function setPrimaryImage(Bike $bike, BikeImage $image)
    {
        abort_unless($image->bike_id === $bike->id, 404);

        DB::transaction(function () use ($bike, $image) {
            $bike->images()->where('id', '!=', $image->id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

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
                    
                    $url = $imgService->save($file, dir: 'rent-bikes');
                    
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
            'bike' => $bike->load('images'),
        ]);
    }
    
    public function update(CreateBikeRequest $request, Bike $bike, ImageService $imgService): RedirectResponse
    {
        $v = $request->validated();
        
        DB::transaction(function () use ($request, $bike, $v, $imgService) {
            unset($v['images']);
            $bike->update($v);
            
            if ($request->hasFile('images')) {
                $startIndex = $bike->images->count();
                
                foreach ($request->file('images') as $i => $file) {
                    if (!$file->isValid()) continue;
                    
                    $url = $imgService->save($file);
                    $curIndex = $startIndex + $i;
                    $bike->images()->create([
                        'url' => $url,
                        'alt' => $bike->name,
                        'sort' => $curIndex,
                        'is_primary' => $curIndex === 0,
                    ]);
                }
            }
        });
        
        return to_route('adminka.rent-bikes.index');
    }
}
