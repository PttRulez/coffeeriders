<?php

namespace App\Http\Controllers;

use App\Models\WorkshopService;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(): Response
    {
        $services = WorkshopService::query()
            ->with('category:id,name,sort_order')
            ->leftJoin('workshop_categories', 'workshop_categories.id', '=', 'workshop_services.workshop_category_id')
            ->orderBy('workshop_categories.sort_order')
            ->orderBy('workshop_categories.name')
            ->orderBy('workshop_services.name')
            ->select(
                'workshop_services.id',
                'workshop_services.name',
                'workshop_services.price_rub',
                'workshop_services.additional_info',
                'workshop_services.workshop_category_id',
            )
            ->get();

        return Inertia::render('workshop/Index', [
            'services' => $services,
        ]);
    }
}
