<?php

namespace App\Services;

use App\Models\WorkshopSparePart;
use App\Models\WorkshopSparePartPurchase;

class WorkshopSparePartStockService
{
    public function recalculate(WorkshopSparePart $sparePart): void
    {
        $purchases = $sparePart->purchases()
            ->orderByDesc('purchased_at')
            ->orderByDesc('id')
            ->get(['id', 'quantity', 'purchase_price_rub']);

        $stockQuantity = (int) $sparePart->quantity;
        $purchasePriceRub = $this->calculateAveragePurchasePrice($purchases->all(), $stockQuantity);

        $sparePart->update([
            'purchase_price_rub' => $purchasePriceRub,
        ]);
    }

    public function applyCreatedPurchase(WorkshopSparePartPurchase $purchase): void
    {
        $sparePart = $purchase->sparePart;
        $sparePart->increment('quantity', (int) $purchase->quantity);
        $sparePart->refresh();

        $this->recalculate($sparePart);
    }

    public function applyUpdatedPurchase(
        WorkshopSparePartPurchase $purchase,
        int $oldQuantity,
        int $oldSparePartId,
    ): void {
        $newSparePartId = (int) $purchase->workshop_spare_part_id;
        $newQuantity = (int) $purchase->quantity;

        if ($newSparePartId !== $oldSparePartId) {
            $oldSparePart = WorkshopSparePart::find($oldSparePartId);
            if ($oldSparePart) {
                $oldNewQuantity = max(0, (int) $oldSparePart->quantity - $oldQuantity);
                $oldSparePart->update(['quantity' => $oldNewQuantity]);
                $this->recalculate($oldSparePart);
            }

            $newSparePart = $purchase->sparePart;
            $newSparePart->increment('quantity', $newQuantity);
            $newSparePart->refresh();
            $this->recalculate($newSparePart);

            return;
        }

        $delta = $newQuantity - $oldQuantity;
        $sparePart = $purchase->sparePart;

        if ($delta > 0) {
            $sparePart->increment('quantity', $delta);
        } elseif ($delta < 0) {
            $updatedQuantity = max(0, (int) $sparePart->quantity + $delta);
            $sparePart->update(['quantity' => $updatedQuantity]);
        }

        $sparePart->refresh();
        $this->recalculate($sparePart);
    }

    public function applyDeletedPurchase(WorkshopSparePartPurchase $purchase): void
    {
        $sparePart = $purchase->sparePart;
        $updatedQuantity = max(0, (int) $sparePart->quantity - (int) $purchase->quantity);

        $sparePart->update(['quantity' => $updatedQuantity]);
        $this->recalculate($sparePart);
    }

    /**
     * Calculates average unit cost for current stock by taking quantities from latest purchases first.
     */
    private function calculateAveragePurchasePrice(array $purchases, int $stockQuantity): int
    {
        if ($stockQuantity <= 0) {
            return 0;
        }

        $remainingQuantity = $stockQuantity;
        $stockCost = 0;

        foreach ($purchases as $purchase) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $purchaseQuantity = (int) $purchase->quantity;
            if ($purchaseQuantity <= 0) {
                continue;
            }

            $takenQuantity = min($remainingQuantity, $purchaseQuantity);
            $stockCost += $takenQuantity * (int) $purchase->purchase_price_rub;
            $remainingQuantity -= $takenQuantity;
        }

        if ($remainingQuantity > 0) {
            return 0;
        }

        return (int) round($stockCost / $stockQuantity);
    }
}
