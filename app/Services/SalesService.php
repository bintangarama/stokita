<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Services\StockRecalculationService;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public static function process(Order $order): void
    {
        DB::transaction(function () use ($order) {

            // Load relasi baru setelah update form
            $order->load('items.item');

            // Jika order kembali ke draft → rollback stok
            if ($order->isDraft()) {
                self::rollbackStock($order);
                return;
            }

            // Jika order dikonfirmasi → kurangi stok
            if ($order->isConfirmed()) {
                self::rollbackStock($order); // bersihkan sebelumnya
                self::applyStock($order);
            }

            // Completed → tidak ada perubahan stok lebih lanjut
            if ($order->isCompleted()) {
                return; // stok sudah pernah dikurangi saat confirmed
            }

            // Cancelled → rollback stok
            if ($order->isCancelled()) {
                self::rollbackStock($order);
            }
        });
    }

    private static function applyStock(Order $order): void
    {
        foreach ($order->items as $item) {

            $product = $item->item;
            if (! $product) continue;

            $qty = abs((float) $item->qty);

            // kurangi stok
            $product->current_stock -= $qty;
            if ($product->current_stock < 0) $product->current_stock = 0;
            $product->save();

            ReorderAlertService::check($product);

            // catat movement
            StockMovement::create([
                'item_id' => $item->item_id,
                'movement_type' => 'sale_out',
                'reference_table' => 'orders',
                'reference_id' => $order->id,
                'qty' => $qty,
                'unit_id' => $item->unit_id,
                'unit_cost' => $product->average_cost,
                'created_by' => $order->created_by ?? auth()->id(),
                'notes' => "Penjualan atas order #{$order->order_no}",
            ]);
        }
    }
    private static function rollbackStock(Order $order): void
    {
        $movements = StockMovement::where('reference_table', 'orders')
            ->where('reference_id', $order->id)
            ->get();

        foreach ($movements as $mv) {
            $product = $mv->item;
            if (!$product) continue;

            $product->current_stock += abs($mv->qty);
            $product->save();
            ReorderAlertService::check($product);
        }

        // hapus movement lama
        StockMovement::where('reference_table', 'orders')
            ->where('reference_id', $order->id)
            ->delete();
    }


    // 

    public static function recalculateTotal(Order $order): void
    {
        $total = $order->items()->sum('line_total');
        $grand = $total - (float)($order->discount ?? 0);

        $order->update([
            'total_amount' => $total,
            'grand_total' => max(0, $grand),
        ]);
    }

    public static function delete(Order $order): void
    {
        DB::transaction(function () use ($order) {

            self::rollback($order);

            $order->items()->delete();

            $order->delete();
        });
    }
}
