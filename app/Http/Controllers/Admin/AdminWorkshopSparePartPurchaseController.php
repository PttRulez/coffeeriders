<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkshopSparePartPurchaseRequest;
use App\Models\WorkshopSparePart;
use App\Models\WorkshopSparePartPurchase;
use App\Services\WorkshopSparePartStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminWorkshopSparePartPurchaseController extends Controller
{
    public function index(): Response
    {
        $items = WorkshopSparePartPurchase::query()
            ->with(['sparePart.category', 'user:id,name'])
            ->leftJoin('workshop_spare_parts', 'workshop_spare_parts.id', '=', 'workshop_spare_part_purchases.workshop_spare_part_id')
            ->orderByDesc('workshop_spare_part_purchases.purchased_at')
            ->orderByDesc('workshop_spare_part_purchases.id')
            ->select('workshop_spare_part_purchases.*')
            ->get();

        return Inertia::render('adminka/workshop/spare-part-purchases/Index', [
            'items' => $items,
        ]);
    }

    public function create(WorkshopSparePart $workshopSparePart): Response
    {
        return Inertia::render('adminka/workshop/spare-part-purchases/Form', [
            'item' => null,
            'sparePart' => $workshopSparePart->load('category:id,name'),
        ]);
    }

    public function store(
        WorkshopSparePartPurchaseRequest $request,
        WorkshopSparePart $workshopSparePart,
        WorkshopSparePartStockService $stockService,
    ): RedirectResponse {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['workshop_spare_part_id'] = $workshopSparePart->id;

        $purchase = WorkshopSparePartPurchase::create($data);
        $purchase->load('sparePart');
        $stockService->applyCreatedPurchase($purchase);

        return redirect()
            ->route('adminka.workshop.spare-parts.show', $workshopSparePart->id)
            ->with('success', 'Закупка добавлена');
    }

    public function edit(
        WorkshopSparePart $workshopSparePart,
        WorkshopSparePartPurchase $workshopSparePartPurchase,
    ): Response
    {
        $this->ensurePurchaseBelongsToSparePart($workshopSparePart, $workshopSparePartPurchase);

        return Inertia::render('adminka/workshop/spare-part-purchases/Form', [
            'item' => $workshopSparePartPurchase,
            'sparePart' => $workshopSparePart->load('category:id,name'),
        ]);
    }

    public function update(
        WorkshopSparePartPurchaseRequest $request,
        WorkshopSparePart $workshopSparePart,
        WorkshopSparePartPurchase $workshopSparePartPurchase,
        WorkshopSparePartStockService $stockService,
    ): RedirectResponse {
        $this->ensurePurchaseBelongsToSparePart($workshopSparePart, $workshopSparePartPurchase);

        $oldSparePartId = $workshopSparePartPurchase->workshop_spare_part_id;
        $oldQuantity = (int) $workshopSparePartPurchase->quantity;
        $data = $request->validated();
        $data['user_id'] = $workshopSparePartPurchase->user_id;
        $data['workshop_spare_part_id'] = $workshopSparePart->id;

        $workshopSparePartPurchase->update($data);
        $workshopSparePartPurchase->load('sparePart');
        $stockService->applyUpdatedPurchase(
            $workshopSparePartPurchase,
            $oldQuantity,
            (int) $oldSparePartId,
        );

        return redirect()
            ->route('adminka.workshop.spare-parts.show', $workshopSparePart->id)
            ->with('success', 'Закупка обновлена');
    }

    public function destroy(
        WorkshopSparePart $workshopSparePart,
        WorkshopSparePartPurchase $workshopSparePartPurchase,
        WorkshopSparePartStockService $stockService,
    ): RedirectResponse {
        $this->ensurePurchaseBelongsToSparePart($workshopSparePart, $workshopSparePartPurchase);

        $sparePart = $workshopSparePartPurchase->sparePart;
        $workshopSparePartPurchase->setRelation('sparePart', $sparePart);
        $workshopSparePartPurchase->delete();
        $stockService->applyDeletedPurchase($workshopSparePartPurchase);

        return redirect()
            ->route('adminka.workshop.spare-parts.show', $workshopSparePart->id)
            ->with('success', 'Закупка удалена');
    }

    private function ensurePurchaseBelongsToSparePart(
        WorkshopSparePart $workshopSparePart,
        WorkshopSparePartPurchase $workshopSparePartPurchase,
    ): void {
        if ($workshopSparePartPurchase->workshop_spare_part_id !== $workshopSparePart->id) {
            abort(HttpResponse::HTTP_NOT_FOUND);
        }
    }
}
