<?php

namespace App\Services;

use App\Models\{Purchase, PurchaseItem, StockMovement, Item};
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public static function process(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // pastikan items dan relasi ter-load
            $purchase->load('items.item');

            // rollback previous movements (hapus movements lama & rekalkulasi item terkait)
            self::rollbackStockMovement($purchase);

            // apply stock changes once per item (create new movements kemudian rekalkulasi item)
            foreach ($purchase->items as $item) {
                self::applyItemStock($item, $purchase);
            }

            // recalc total
            self::recalculateTotal($purchase);
        });
    }

    /**
     * Hapus semua stock movements yang terkait purchase ini, kemudian rekalkulasi setiap item terkait.
     * Pendekatan: delete movements -> recalc per item dari ledger yang tersisa.
     */
    public static function rollbackStockMovement(Purchase $purchase): void
    {
        // ambil daftar item_id yang terpengaruh oleh movement purchase ini
        $affectedItemIds = StockMovement::where('reference_table', 'purchases')
            ->where('reference_id', $purchase->id)
            ->pluck('item_id')
            ->unique()
            ->toArray();

        // hapus semua movement terkait purchase (permanent delete)
        StockMovement::where('reference_table', 'purchases')
            ->where('reference_id', $purchase->id)
            ->delete();

        // rekalkulasi tiap item yang terpengaruh
        foreach ($affectedItemIds as $itemId) {
            $item = Item::find($itemId);
            if ($item) {
                \App\Services\StockRecalculationService::recalcItem($item);
                ReorderAlertService::check($item);
            }
        }
    }

    /**
     * Buat movement baru untuk purchase item, lalu rekalkulasi item dari ledger.
     */
    public static function applyItemStock(PurchaseItem $item, Purchase $purchase): void
    {
        $product = $item->item;
        if (! $product) {
            return;
        }

        $qty = abs((float) $item->qty);

        // buat movement (qty positif; movement_type menentukan direction)
        StockMovement::create([
            'item_id' => $item->item_id,
            'movement_type' => 'purchase_in',
            'reference_table' => 'purchases',
            'reference_id' => $purchase->id,
            'qty' => $qty,
            'unit_id' => $item->unit_id,
            'unit_cost' => (float) $item->unit_price,
            'created_by' => $purchase->created_by,
            'notes' => 'Auto entry from purchase #' . ($purchase->invoice_no ?: $purchase->id),
        ]);

        // rekalkulasi item dari ledger (single source of truth)
        \App\Services\StockRecalculationService::recalcItem($product);
        ReorderAlertService::check($product);
    }

    public static function recalculateTotal(Purchase $purchase): void
    {
        $purchase->update([
            'total_amount' => $purchase->items()->sum('line_total')
        ]);
    }

    public static function delete(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // rollback & delete movements
            self::rollbackStockMovement($purchase);

            // delete purchase items
            $purchase->items()->delete();

            // delete purchase record itself (forceDelete jika model memakai soft deletes)
            if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses($purchase))) {
                $purchase->forceDelete();
            } else {
                $purchase->delete();
            }
        });
    }

    /**
     * Delete a single purchase item and rollback stock for that item only.
     */
    public static function deleteItem(PurchaseItem $item): void
    {
        DB::transaction(function () use ($item) {
            $purchase = $item->purchase;
            $product = $item->item;
            if (! $product) {
                $item->delete();
                return;
            }

            // ambil movements terkait purchase & item
            $movements = StockMovement::where('reference_table', 'purchases')
                ->where('reference_id', $purchase->id)
                ->where('item_id', $product->id)
                ->pluck('id')
                ->toArray();

            // hapus movements
            StockMovement::whereIn('id', $movements)->delete();

            // rekalkulasi item dari ledger yang tersisa
            \App\Services\StockRecalculationService::recalcItem($product);

            // hapus purchase item
            $item->delete();

            // update purchase total
            $purchase->update([
                'total_amount' => $purchase->items()->sum('line_total')
            ]);
        });
    }
}
