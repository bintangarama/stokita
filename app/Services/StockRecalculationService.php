<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;

class StockRecalculationService
{
    /**
     * Rekalkulasi stock dan average_cost sebuah item berdasarkan stock_movements yang ada (ledger).
     */
    public static function recalcItem(Item $item): void
    {
        // Ambil semua movement yang tersisa untuk item ini (permanent delete digunakan)
        $movements = StockMovement::where('item_id', $item->id)->get();

        $totalQtyBase = 0.0;
        $totalValue = 0.0; // hanya inbound contributes to value

        foreach ($movements as $mv) {
            $mvQty = abs((float) $mv->qty);

            $qtyBase = UnitConversionService::convert(
                $mvQty,
                (int) $mv->unit_id,
                (int) $item->base_unit_id
            );

            if (in_array($mv->movement_type, ['purchase_in', 'production_in', 'transfer_in'])) {
                $totalQtyBase += $qtyBase;
                $unitCost = (float) ($mv->unit_cost ?? 0);
                $totalValue += ($unitCost * $qtyBase);
            } else {
                $totalQtyBase -= $qtyBase;
            }
        }

        if ($totalQtyBase < 0) {
            $totalQtyBase = 0;
        }

        $item->current_stock = $totalQtyBase;
        $item->average_cost = $totalQtyBase > 0 ? ($totalValue / $totalQtyBase) : 0;
        $item->save();
    }
}
