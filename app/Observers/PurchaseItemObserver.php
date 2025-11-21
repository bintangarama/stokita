<?php

namespace App\Observers;

use App\Models\PurchaseItem;
use App\Services\UnitConversionService;

class PurchaseItemObserver
{
    public function saving(PurchaseItem $item): void
    {
        $item->line_total = (float) ($item->qty ?? 0) * (float) ($item->unit_price ?? 0);
    }

    public function saved(PurchaseItem $item): void
    {
        // Pastikan semua field sudah terisi
        if (!$item->item || !$item->unit_id || !$item->qty || !$item->unit_price) {
            return;
        }

        $product = $item->item;
        if (!$product) return;

        // Konversi qty ke satuan dasar (kalau ada konversi)
        $qtyInBase = UnitConversionService::convert(
            (float) $item->qty,
            (int) $item->unit_id,
            (int) $product->base_unit_id
        );

        // Update stok & average cost
        $currentStock = $product->current_stock ?? 0;
        $currentCost  = $product->average_cost ?? 0;

        $newStockValue = ($currentCost * $currentStock) + ($item->unit_price * $qtyInBase);
        $newStockQty   = $currentStock + $qtyInBase;

        if ($newStockQty > 0) {
            $product->average_cost = $newStockValue / $newStockQty;
        }

        $product->current_stock = $newStockQty;
        $product->save();
    }
}
