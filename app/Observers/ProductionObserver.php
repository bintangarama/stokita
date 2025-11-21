<?php

namespace App\Observers;

use App\Models\Production;
use App\Models\StockMovement;
use App\Services\UnitConversionService;

class ProductionObserver
{
    public function deleting(Production $production)
    {
        // 1. Kembalikan stok BAHAN (reverse production_out)
        foreach ($production->components as $component) {
            $item = $component->ingredientItem;
            if (!$item) continue;

            $qtyBase = UnitConversionService::convert(
                $component->qty_used,
                $component->unit_id,
                $item->base_unit_id,
            );

            $item->current_stock += $qtyBase;
            $item->save();
        }

        // 2. Kurangi stok PRODUK JADI (reverse production_in)
        $producedItem = $production->producedItem;
        if ($producedItem) {

            $qtyBase = UnitConversionService::convert(
                $production->produced_qty,
                $production->produced_unit_id,
                $producedItem->base_unit_id
            );

            $producedItem->current_stock -= $qtyBase;
            if ($producedItem->current_stock < 0) {
                $producedItem->current_stock = 0;
            }
            $producedItem->save();
        }

        // 3. Hapus semua StockMovement terkait produksi ini
        StockMovement::where('reference_table', 'productions')
            ->where('reference_id', $production->id)
            ->delete();
    }
}
