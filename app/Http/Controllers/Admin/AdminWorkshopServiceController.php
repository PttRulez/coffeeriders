<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopServiceRequest;
use App\Models\WorkshopCategory;
use App\Models\WorkshopService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopServiceController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopService::query()
            ->with('category')
            ->leftJoin('workshop_categories', 'workshop_categories.id', '=', 'workshop_services.workshop_category_id')
            ->orderBy('workshop_categories.sort_order')
            ->orderBy('workshop_categories.name')
            ->orderBy('workshop_services.name')
            ->select('workshop_services.*')
            ->get();

        return Inertia::render('adminka/workshop/services/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/workshop/services/Form', [
            'item' => null,
            'categories' => WorkshopCategory::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(WorkshopServiceRequest $request): RedirectResponse
    {
        WorkshopService::create($request->validated());

        return redirect()
            ->route('adminka.workshop.services.index')
            ->with('success', 'Услуга добавлена');
    }

    public function edit(WorkshopService $workshopService): Response
    {
        return Inertia::render('adminka/workshop/services/Form', [
            'item' => $workshopService,
            'categories' => WorkshopCategory::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(
        WorkshopServiceRequest $request,
        WorkshopService $workshopService,
    ): RedirectResponse {
        $workshopService->update($request->validated());

        return redirect()
            ->route('adminka.workshop.services.index')
            ->with('success', 'Услуга обновлена');
    }

    public function destroy(WorkshopService $workshopService): RedirectResponse
    {
        $workshopService->delete();

        return redirect()
            ->route('adminka.workshop.services.index')
            ->with('success', 'Услуга удалена');
    }
}
