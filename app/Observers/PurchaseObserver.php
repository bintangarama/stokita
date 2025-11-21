<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Models\StockMovement;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        foreach ($purchase->items as $item) {
            StockMovement::create([
                'item_id' => $item->item_id,
                'movement_type' => 'purchase_in',
                'reference_table' => 'purchases',
                'reference_id' => $purchase->id,
                'qty' => $item->quantity,
                'unit_id' => $item->unit_id,
                'unit_cost' => $item->unit_price,
                'created_by' => $purchase->created_by,
                'notes' => 'Auto entry from purchase #' . $purchase->purchase_no,
            ]);
        }
    }
}
