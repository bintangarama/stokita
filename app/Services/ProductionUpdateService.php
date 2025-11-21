<?php

namespace App\Services;

use App\Models\{
    Production,
    ProductionComponent,
    StockMovement,
    Recipe
};
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class ProductionUpdateService
{
    /**
     * Update produksi dengan cara:
     * 1) rollback efek produksi lama (mengembalikan stok bahan, mengurangi stok produk)
     * 2) hapus movement & components lama
     * 3) hitung ulang produksi baru berdasarkan input (unit user dipakai)
     * 4) kurangi stok bahan baru, simpan components baru, buat movement baru
     * 5) tambah stok produk baru, simpan movement IN
     *
     * Semua qty di StockMovement disimpan dalam base unit (qty_base) dan unit_id = base_unit_id.
     *
     * @param Production $production  Model produksi yang akan diupdate
     * @param array $data             Data baru (harus mencakup: recipe_id, produced_qty, produced_unit_id, overhead_cost optional)
     * @return Production
     * @throws \Exception
     */
    public static function updateProduction(Production $production, array $data): Production
    {
        return DB::transaction(function () use ($production, $data) {

            // ---------------------------
            // 1) Ambil resep & produced item
            // ---------------------------
            $recipe = Recipe::with(['ingredients.ingredientItem', 'item'])
                ->findOrFail($data['recipe_id']);

            $producedItem = $recipe->item;
            $producedBaseUnit = $producedItem->base_unit_id;

            $outputQty = (float) $data['produced_qty'];
            $outputUnitId = (int) $data['produced_unit_id'];

            // ---------------------------
            // 2) Konversi output user -> base unit (wajib)
            // ---------------------------
            $outputQtyBase = UnitConversionService::convert(
                qty: $outputQty,
                fromUnitId: $outputUnitId,
                toUnitId: $producedBaseUnit
            );

            if (($yield = (float) $recipe->yield_qty) <= 0) {
                throw new \Exception("Yield resep tidak valid.");
            }

            // batch multiplier berdasarkan base unit
            $batchMultiplier = $outputQtyBase / $yield;

            // ---------------------------
            // 3) ROLLBACK produksi lama
            //    -> kembalikan stok bahan menggunakan qty_used_base (jika ada)
            //    -> kurangi stok produk hasil
            //    -> hapus komponen & stock movements lama
            // ---------------------------

            // 3A: restore bahan
            $oldComponents = $production->components()->get();
            foreach ($oldComponents as $comp) {
                $ingredientItem = $comp->ingredientItem;
                if (! $ingredientItem) {
                    continue;
                }

                // Jika qty_used_base ada gunakan langsung, jika tidak fallback convert
                if (isset($comp->qty_used_base) && $comp->qty_used_base !== null) {
                    $qtyBaseOld = (float) $comp->qty_used_base;
                } else {
                    // fallback: convert dari qty_used (unit resep) -> base unit bahan
                    $qtyBaseOld = UnitConversionService::convert(
                        qty: (float) $comp->qty_used,
                        fromUnitId: $comp->unit_id,
                        toUnitId: $ingredientItem->base_unit_id
                    );
                }

                // kembalikan stok bahan
                $ingredientItem->current_stock = ($ingredientItem->current_stock ?? 0) + $qtyBaseOld;
                $ingredientItem->save();
            }

            // 3B: rollback stok produk jadi lama (kurangi jumlah yang sebelumnya ditambahkan)
            $oldProducedQtyBase = UnitConversionService::convert(
                qty: (float) $production->produced_qty,
                fromUnitId: $production->produced_unit_id,
                toUnitId: $producedBaseUnit
            );

            // kurangi stok produk (mengembalikan kondisi sebelum produksi)
            $producedItem->current_stock = max(0, ($producedItem->current_stock ?? 0) - $oldProducedQtyBase);
            $producedItem->save();

            // 3C: hapus komponen & movement lama
            ProductionComponent::where('production_id', $production->id)->delete();

            StockMovement::where('reference_table', 'productions')
                ->where('reference_id', $production->id)
                ->delete();

            // ---------------------------
            // 4) Hitung ulang kebutuhan bahan untuk produksi baru
            // ---------------------------
            $totalMaterialCost = 0.0;
            $componentsToInsert = [];

            foreach ($recipe->ingredients as $ingredient) {
                $ingredientItem = $ingredient->ingredientItem;
                if (! $ingredientItem) {
                    throw new \Exception("Ingredient item untuk resep tidak ditemukan.");
                }

                // qty yang dibutuhkan pada unit resep (misal resep: 100 g)
                $qtyRecipeUnit = (float) $ingredient->qty * $batchMultiplier;

                // konversi qtyRecipeUnit -> base unit bahan (misal gram)
                $qtyBaseNew = UnitConversionService::convert(
                    qty: $qtyRecipeUnit,
                    fromUnitId: $ingredient->unit_id,
                    toUnitId: $ingredientItem->base_unit_id
                );

                // cek stok cukup (stok saat ini sudah di-rollback sebelumnya)
                if (($ingredientItem->current_stock ?? 0) < $qtyBaseNew) {
                    throw new \Exception("Stok {$ingredientItem->name} tidak cukup untuk produksi baru.");
                }

                $unitCost = (float) ($ingredientItem->average_cost ?? 0);
                $lineCost = $unitCost * $qtyBaseNew;
                $totalMaterialCost += $lineCost;

                // kurangi stok bahan (apply perubahan baru)
                $ingredientItem->current_stock = ($ingredientItem->current_stock ?? 0) - $qtyBaseNew;
                $ingredientItem->save();

                // simpan data component (qty dalam unit resep + qty base)
                $componentsToInsert[] = [
                    'production_id'      => $production->id,
                    'ingredient_item_id' => $ingredientItem->id,
                    'unit_id'            => $ingredient->unit_id,    // unit resep (for display)
                    'qty_used'           => $qtyRecipeUnit,         // contoh: 100 * multiplier
                    'qty_used_base'      => $qtyBaseNew,            // base unit (misal gram)
                    'unit_cost'          => $unitCost,
                    'line_total_cost'    => $lineCost,
                ];
            }

            // ---------------------------
            // 5) Simpan components baru + buat stock movements OUT (semua dalam base unit)
            // ---------------------------
            foreach ($componentsToInsert as $comp) {
                ProductionComponent::create($comp);

                // buat movement out — selalu simpan qty dalam base unit & unit_id = base_unit bahan
                $ingredientItem = \App\Models\Item::find($comp['ingredient_item_id']);
                $baseUnitId = $ingredientItem->base_unit_id;

                StockMovement::create([
                    'item_id'        => $comp['ingredient_item_id'],
                    'movement_type'  => 'production_out',
                    'reference_table' => 'productions',
                    'reference_id'   => $production->id,
                    'qty'            => $comp['qty_used_base'],  // base unit
                    'unit_id'        => $baseUnitId,             // base unit id (fixed)
                    'unit_cost'      => $comp['unit_cost'],
                    'created_by'     => $production->produced_by,
                    'notes'          => 'Bahan keluar (update produksi #' . $production->id . ')',
                ]);
            }

            // ---------------------------
            // 6) Tambah stok produk hasil (IN) + movement IN
            // ---------------------------
            $overheadCost = (float) ($data['overhead_cost'] ?? $production->overhead_cost ?? 0);
            $totalCost = $totalMaterialCost + $overheadCost;

            // Tambah stok produk sekarang berdasarkan qty base (outputQtyBase)
            $producedItem->current_stock = ($producedItem->current_stock ?? 0) + $outputQtyBase;

            // Perhitungan average cost sederhana (kamu bisa ganti ke weighted average bila perlu)
            $producedItem->average_cost = $outputQtyBase > 0 ? ($totalCost / $outputQtyBase) : $producedItem->average_cost;

            $producedItem->save();

            // Movement in dalam base unit produk
            StockMovement::create([
                'item_id'        => $producedItem->id,
                'movement_type'  => 'production_in',
                'reference_table' => 'productions',
                'reference_id'   => $production->id,
                'qty'            => $outputQtyBase,
                'unit_id'        => $producedBaseUnit,
                'unit_cost'      => $producedItem->average_cost,
                'created_by'     => $production->produced_by,
                'notes'          => 'Hasil produksi (update #' . $production->id . ')',
            ]);

            // ---------------------------
            // 7) Update record produksi
            // ---------------------------
            $production->update([
                'recipe_id'        => $data['recipe_id'],
                'produced_qty'     => $outputQty,
                'produced_unit_id' => $outputUnitId,
                'cost_total'       => $totalMaterialCost,
                'overhead_cost'    => $overheadCost,
                'selling_price'    => $producedItem->selling_price ?? $production->selling_price,
            ]);

            return $production;
        });
    }
}
