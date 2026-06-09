<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Esloquent\SoftDeletes;

class Item extends Model
{
    //
    // use SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'base_unit_id',
        'track_stock',
        'reorder_threshold',
        'current_stock',
        'average_cost',
        'last_purchase_price',
        'selling_price',
        'last_reorder_alert_at'
    ];

    protected $casts = [
        'last_reorder_alert_at' => 'datetime',
        'track_stock' => 'boolean',
    ];

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function productionComponents()
    {
        return $this->hasMany(ProductionComponent::class, 'ingredient_item_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(\App\Models\StockMovement::class);
    }
}
