<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopCategoryRequest;
use App\Models\WorkshopCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopCategoryController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopCategory::query()
            ->withCount('services')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('adminka/workshop/categories/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('adminka/workshop/categories/Form', [
            'item' => null,
        ]);
    }

    public function store(WorkshopCategoryRequest $request): RedirectResponse
    {
        WorkshopCategory::create($request->validated());

        return redirect()
            ->route('adminka.workshop.categories.index')
            ->with('success', 'Категория добавлена');
    }

    public function edit(WorkshopCategory $workshopCategory): Response
    {
        return Inertia::render('adminka/workshop/categories/Form', [
            'item' => $workshopCategory,
        ]);
    }

    public function update(
        WorkshopCategoryRequest $request,
        WorkshopCategory $workshopCategory,
    ): RedirectResponse {
        $workshopCategory->update($request->validated());

        return redirect()
            ->route('adminka.workshop.categories.index')
            ->with('success', 'Категория обновлена');
    }

    public function destroy(WorkshopCategory $workshopCategory): RedirectResponse
    {
        if ($workshopCategory->services()->exists()) {
            return redirect()
                ->route('adminka.workshop.categories.index')
                ->with('error', 'Нельзя удалить категорию с привязанными услугами');
        }

        $workshopCategory->delete();

        return redirect()
            ->route('adminka.workshop.categories.index')
            ->with('success', 'Категория удалена');
    }
}
