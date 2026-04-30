<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopSparePartCategoryRequest;
use App\Models\WorkshopSparePartCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopSparePartCategoryController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopSparePartCategory::query()
            ->withCount('spareParts')
            ->orderBy('name')
            ->get();

        return Inertia::render('adminka/workshop/spare-part-categories/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/workshop/spare-part-categories/Form', [
            'item' => null,
        ]);
    }

    public function store(WorkshopSparePartCategoryRequest $request): RedirectResponse
    {
        WorkshopSparePartCategory::create($request->validated());

        return redirect()
            ->route('adminka.workshop.spare-part-categories.index')
            ->with('success', 'Категория запчастей добавлена');
    }

    public function edit(WorkshopSparePartCategory $workshopSparePartCategory): Response
    {
        return Inertia::render('adminka/workshop/spare-part-categories/Form', [
            'item' => $workshopSparePartCategory,
        ]);
    }

    public function update(
        WorkshopSparePartCategoryRequest $request,
        WorkshopSparePartCategory $workshopSparePartCategory,
    ): RedirectResponse {
        $workshopSparePartCategory->update($request->validated());

        return redirect()
            ->route('adminka.workshop.spare-part-categories.index')
            ->with('success', 'Категория запчастей обновлена');
    }

    public function destroy(WorkshopSparePartCategory $workshopSparePartCategory): RedirectResponse
    {
        if ($workshopSparePartCategory->spareParts()->exists()) {
            return redirect()
                ->route('adminka.workshop.spare-part-categories.index')
                ->with('error', 'Нельзя удалить категорию с привязанными запчастями');
        }

        $workshopSparePartCategory->delete();

        return redirect()
            ->route('adminka.workshop.spare-part-categories.index')
            ->with('success', 'Категория запчастей удалена');
    }
}
