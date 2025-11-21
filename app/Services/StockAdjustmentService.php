<?php

namespace App\Services;

/**
 * Class StockAdjustmentService.
 */

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Services\UnitConversionService;

class StockAdjustmentService
{
    /**
     * @param int $itemId
     * @param float $adjustQty — jumlah selisih (positif = tambah, negatif = kurang)
     * @param int $unitId — satuan input user
     * @param string|null $reason — alasan penyesuaian
     * @param int|null $userId
     */
    public static function adjustStock(int $itemId, float $adjustQty, int $unitId, ?string $reason = null, ?int $userId = null)
    {
        return DB::transaction(function () use ($itemId, $adjustQty, $unitId, $reason, $userId) {
            $item = Item::findOrFail($itemId);

            // Konversi ke satuan dasar
            $qtyInBase = UnitConversionService::convert($adjustQty, $unitId, $item->base_unit_id);

            // Update stok
            $item->update([
                'current_stock' => $item->current_stock + $qtyInBase,
            ]);

            // Simpan log adjustment (jika punya tabel stock_adjustments)
            if (class_exists(\App\Models\StockAdjustment::class)) {
                \App\Models\StockAdjustment::create([
                    'item_id' => $item->id,
                    'adjust_qty' => $adjustQty,
                    'unit_id' => $unitId,
                    'adjust_qty_base' => $qtyInBase,
                    'reason' => $reason,
                    'adjusted_by' => $userId,
                ]);
            }

            return $item;
        });
    }
}
