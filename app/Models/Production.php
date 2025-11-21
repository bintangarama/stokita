<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    protected $fillable = [
        'produced_item_id',
        'recipe_id',
        'produced_unit_id',
        'produced_qty',
        'cost_total',
        'overhead_cost',
        'selling_price',
        'produced_by',
        'produced_at',
        'notes',
    ];

    protected $casts = [
        'produced_qty' => 'decimal:4',
        'cost_total' => 'decimal:4',
        'overhead_cost' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'produced_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function producedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'produced_item_id');
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'produced_unit_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'produced_by');
    }

    public function components(): HasMany
    {
        return $this->hasMany(ProductionComponent::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function totalCostWithOverhead(): float
    {
        return (float) $this->cost_total + (float) $this->overhead_cost;
    }

    public function costPerUnit(): float
    {
        return $this->produced_qty > 0
            ? $this->totalCostWithOverhead() / $this->produced_qty
            : 0;
    }
}
