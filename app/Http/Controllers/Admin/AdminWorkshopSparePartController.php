<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopSparePartRequest;
use App\Models\WorkshopSparePart;
use App\Models\WorkshopSparePartCategory;
use App\Services\ImageService;
use App\Services\WorkshopSparePartStockService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopSparePartController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopSparePart::query()
            ->with('category')
            ->leftJoin(
                'workshop_spare_part_categories',
                'workshop_spare_part_categories.id',
                '=',
                'workshop_spare_parts.workshop_spare_part_category_id'
            )
            ->orderBy('workshop_spare_part_categories.name')
            ->orderBy('workshop_spare_parts.name')
            ->select('workshop_spare_parts.*')
            ->get();

        return Inertia::render('adminka/workshop/spare-parts/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/workshop/spare-parts/Form', [
            'item' => null,
            'categories' => WorkshopSparePartCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(
        WorkshopSparePartRequest $request,
        ImageService $imageService,
        WorkshopSparePartStockService $stockService,
    ): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $imageService->save($request->file('photo'), 'workshop-parts');
        }

        $data['purchase_price_rub'] = 0;
        $data['quantity'] = 0;

        unset($data['photo']);

        $sparePart = WorkshopSparePart::create($data);
        $stockService->recalculate($sparePart);

        return redirect()
            ->route('adminka.workshop.spare-parts.index')
            ->with('success', 'Запчасть добавлена');
    }

    public function edit(WorkshopSparePart $workshopSparePart): Response
    {
        return Inertia::render('adminka/workshop/spare-parts/Form', [
            'item' => $workshopSparePart,
            'categories' => WorkshopSparePartCategory::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(
        WorkshopSparePartRequest $request,
        WorkshopSparePart $workshopSparePart,
        ImageService $imageService,
        WorkshopSparePartStockService $stockService,
    ): RedirectResponse {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($workshopSparePart->photo_url) {
                $imageService->delete($workshopSparePart->photo_url);
            }

            $data['photo_url'] = $imageService->save($request->file('photo'), 'workshop-parts');
        }

        unset($data['photo']);

        $workshopSparePart->update($data);
        $stockService->recalculate($workshopSparePart);

        return redirect()
            ->route('adminka.workshop.spare-parts.index')
            ->with('success', 'Запчасть обновлена');
    }

    public function destroy(WorkshopSparePart $workshopSparePart, ImageService $imageService): RedirectResponse
    {
        if ($workshopSparePart->photo_url) {
            $imageService->delete($workshopSparePart->photo_url);
        }

        $workshopSparePart->delete();

        return redirect()
            ->route('adminka.workshop.spare-parts.index')
            ->with('success', 'Запчасть удалена');
    }

    public function show(WorkshopSparePart $workshopSparePart): Response
    {
        $workshopSparePart->load([
            'category:id,name',
            'purchases' => fn ($query) => $query
                ->with('user:id,name')
                ->orderByDesc('purchased_at')
                ->orderByDesc('id'),
        ]);

        return Inertia::render('adminka/workshop/spare-parts/Show', [
            'item' => $workshopSparePart,
        ]);
    }
}
