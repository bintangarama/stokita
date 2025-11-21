<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'item_id',
        'name',
        'yield_qty',
        'version',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'yield_qty' => 'decimal:4',
        'is_active' => 'boolean',
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

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function activeIngredients()
    {
        return $this->ingredients()->get();
    }

    public function totalIngredientCount(): int
    {
        return $this->ingredients()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
