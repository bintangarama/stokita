<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'item_id',
        'movement_type',
        'reference_table',
        'reference_id',
        'qty',
        'unit_id',
        'unit_cost',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'unit_cost' => 'decimal:4',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isInbound(): bool
    {
        return in_array($this->movement_type, ['purchase_in', 'production_in', 'transfer_in']);
    }

    public function isOutbound(): bool
    {
        return in_array($this->movement_type, ['sale_out', 'production_out', 'transfer_out']);
    }

    public function direction(): string
    {
        return $this->isInbound() ? '+' : '-';
    }

    public function reference(): ?Model
    {
        if (!$this->reference_table || !$this->reference_id) {
            return null;
        }

        $model = '\\App\\Models\\' . \Str::studly(\Str::singular($this->reference_table));
        return class_exists($model) ? $model::find($this->reference_id) : null;
    }
}
