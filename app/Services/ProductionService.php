<?php

namespace App\Services;

use App\Models\{
    Recipe,
    Production,
    ProductionComponent,
    StockMovement
};
use Illuminate\Support\Facades\DB;
use App\Services\UnitConversionService;

class ProductionService
{
    public static function produce(
        int $recipeId,
        float $outputQty,
        int $userId,
        ?float $overheadCost = null,
        ?int $outputUnitId = null
    ): Production {

        return DB::transaction(function () use ($recipeId, $outputQty, $userId, $overheadCost, $outputUnitId) {

            $recipe = Recipe::with(['ingredients.ingredientItem', 'item'])->findOrFail($recipeId);
            $producedItem = $recipe->item;
            $baseUnit     = $producedItem->base_unit_id;

            /** -------------------------------
             * 1. KONVERSI OUTPUT → BASE UNIT
             * --------------------------------*/
            $outputQtyBase = UnitConversionService::convert(
                qty: $outputQty,
                fromUnitId: $outputUnitId ?: $baseUnit,
                toUnitId: $baseUnit
            );

            if ($recipe->yield_qty <= 0) {
                throw new \Exception("Yield resep tidak valid.");
            }

            $batchMultiplier = $outputQtyBase / $recipe->yield_qty;

            $totalMaterialCost = 0;
            $components = [];

            /** -------------------------------
             * 2. KURANGI STOK BAHAN
             * --------------------------------*/
            foreach ($recipe->ingredients as $ingredient) {

                $item = $ingredient->ingredientItem;

                $qtyRecipeUnit = $ingredient->qty * $batchMultiplier;

                $qtyBase = UnitConversionService::convert(
                    qty: $qtyRecipeUnit,
                    fromUnitId: $ingredient->unit_id,
                    toUnitId: $item->base_unit_id
                );

                if ($item->current_stock < $qtyBase) {
                    throw new \Exception("Stok {$item->name} tidak cukup.");
                }

                $unitCost = $item->average_cost ?? 0;
                $lineCost = $unitCost * $qtyBase;
                $totalMaterialCost += $lineCost;

                $item->current_stock -= $qtyBase;
                $item->save();

                $components[] = [
                    'ingredient_item_id' => $item->id,
                    'unit_id'            => $ingredient->unit_id,
                    'qty_used'           => $qtyRecipeUnit,
                    'qty_used_base'      => $qtyBase,
                    'unit_cost'          => $unitCost,
                    'line_total_cost'    => $lineCost,
                ];
            }

            $overheadCost = $overheadCost ?? ($totalMaterialCost * 0.05);
            $grandTotalCost = $totalMaterialCost + $overheadCost;

            /** -------------------------------
             * 3. BUAT RECORD PRODUKSI
             * --------------------------------*/
            $production = Production::create([
                'produced_item_id' => $producedItem->id,
                'recipe_id'        => $recipeId,
                'produced_unit_id' => $outputUnitId ?: $baseUnit,
                'produced_qty'     => $outputQty,
                'cost_total'       => $totalMaterialCost,
                'overhead_cost'    => $overheadCost,
                'selling_price'    => $producedItem->selling_price,
                'produced_by'      => $userId,
                'produced_at'      => now(),
                'notes'            => 'Produksi otomatis',
            ]);

            /** -------------------------------
             * 4. INSERT COMPONENTS
             * --------------------------------*/
            foreach ($components as $comp) {
                $comp['production_id'] = $production->id;
                ProductionComponent::create($comp);

                StockMovement::create([
                    'item_id'        => $comp['ingredient_item_id'],
                    'movement_type'  => 'production_out',
                    'reference_table' => 'productions',
                    'reference_id'   => $production->id,
                    'qty'            => $comp['qty_used_base'],         // ALWAYS base unit
                    'unit_id'        => $producedItem->base_unit_id,    // base unit
                    'unit_cost'      => $comp['unit_cost'],
                    'created_by'     => $userId,
                ]);
            }

            /** -------------------------------
             * 5. TAMBAH STOK PRODUK JADI
             * --------------------------------*/
            $producedItem->current_stock += $outputQtyBase;

            $producedItem->average_cost =
                $outputQtyBase > 0 ? ($grandTotalCost / $outputQtyBase) : $producedItem->average_cost;

            $producedItem->save();

            StockMovement::create([
                'item_id'        => $producedItem->id,
                'movement_type'  => 'production_in',
                'reference_table' => 'productions',
                'reference_id'   => $production->id,
                'qty'            => $outputQtyBase,
                'unit_id'        => $baseUnit,
                'unit_cost'      => $producedItem->average_cost,
                'created_by'     => $userId,
            ]);

            return $production;
        });
    }
}
