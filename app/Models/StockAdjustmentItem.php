<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    protected $fillable = [
        'stock_adjustment_id',
        'item_id',
        'unit_id',
        'adjust_qty',
        'adjust_qty_base',
        'reason'
    ];

    public function adjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
