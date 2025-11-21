<?php

namespace App\Services;

/**
 * Class SalesService.
 */

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class SalesService
{
    /**
     * Confirm a sale order and reduce stock
     */
    public static function confirm(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $itemRow) {
                $item = $itemRow->item;

                // Convert sold qty to base unit
                $qtyInBase = UnitConversionService::convert(
                    $itemRow->qty,
                    $itemRow->unit_id,
                    $item->base_unit_id
                );

                // Update item stock
                $item->current_stock -= $qtyInBase;
                $item->save();

                // Record stock movement
                StockMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'sale_out',
                    'reference_table' => 'orders',
                    'reference_id' => $order->id,
                    'qty' => -abs($itemRow->qty),
                    'unit_id' => $itemRow->unit_id,
                    'unit_cost' => $item->average_cost,
                    'created_by' => $order->created_by,
                    'notes' => 'Sale confirmed for order #' . $order->order_no,
                ]);
            }

            $order->update(['status' => 'confirmed']);
        });
    }

    /**
     * Cancel a sale order (optional)
     */
    public static function cancel(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $itemRow) {
                $item = $itemRow->item;
                $qtyInBase = UnitConversionService::convert(
                    $itemRow->qty,
                    $itemRow->unit_id,
                    $item->base_unit_id
                );

                $item->current_stock += $qtyInBase;
                $item->save();

                StockMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'adjustment',
                    'reference_table' => 'orders',
                    'reference_id' => $order->id,
                    'qty' => abs($itemRow->qty),
                    'unit_id' => $itemRow->unit_id,
                    'unit_cost' => $item->average_cost,
                    'created_by' => $order->created_by,
                    'notes' => 'Order cancelled, stock restored for order #' . $order->order_no,
                ]);
            }

            $order->update(['status' => 'cancelled']);
        });
    }
}
