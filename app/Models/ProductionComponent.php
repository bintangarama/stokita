<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionComponent extends Model
{
    protected $fillable = [
        'production_id',
        'ingredient_item_id',
        'unit_id',
        'qty_used',
        'unit_cost',
        'line_total_cost',
    ];

    protected $casts = [
        'qty_used' => 'decimal:8',
        'unit_cost' => 'decimal:4',
        'line_total_cost' => 'decimal:4',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function ingredientItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'ingredient_item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function calculateLineCost(): float
    {
        return $this->qty_used * $this->unit_cost;
    }
}
