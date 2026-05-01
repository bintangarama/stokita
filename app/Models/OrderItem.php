<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_id',
        'unit_id',
        'qty',
        'unit_price',
        'line_total'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->line_total = (float)$item->qty * (float)$item->unit_price;
        });
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
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
