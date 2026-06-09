<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kg = Unit::where('code', 'KG')->first();
        $gr = Unit::where('code', 'GR')->first();
        $pcs = Unit::where('code', 'PCS')->first();
        $lt = Unit::where('code', 'LT')->first();

        // Items
        $nasiAyam = Item::where('sku', 'FG-001')->first();
        $nasiRendang = Item::where('sku', 'FG-002')->first();
        $sambal = Item::where('sku', 'CP-001')->first();
        $ayamUngkep = Item::where('sku', 'CP-002')->first();
        
        $beras = Item::where('sku', 'RM-001')->first();
        $ayam = Item::where('sku', 'RM-002')->first();
        $minyak = Item::where('sku', 'RM-003')->first();
        $bawangMerah = Item::where('sku', 'RM-006')->first();
        $bawangPutih = Item::where('sku', 'RM-007')->first();
        $cabai = Item::where('sku', 'RM-008')->first();
        $daging = Item::where('sku', 'RM-009')->first();
        $santan = Item::where('sku', 'RM-010')->first();

        // 1. Recipe for Sambal Terasi (Yield 1 KG)
        $recipeSambal = Recipe::updateOrCreate(
            ['item_id' => $sambal->id, 'name' => 'Resep Sambal Terasi Standar'],
            ['yield_qty' => 1, 'notes' => 'Resep standar untuk pelengkap nasi kotak']
        );

        $ingredientsSambal = [
            ['item' => $cabai, 'qty' => 0.5],
            ['item' => $bawangMerah, 'qty' => 0.2],
            ['item' => $bawangPutih, 'qty' => 0.1],
            ['item' => $minyak, 'qty' => 0.2],
        ];

        foreach ($ingredientsSambal as $ing) {
            RecipeIngredient::updateOrCreate(
                ['recipe_id' => $recipeSambal->id, 'ingredient_item_id' => $ing['item']->id],
                ['unit_id' => $ing['item']->base_unit_id, 'qty' => $ing['qty']]
            );
        }

        // 2. Recipe for Ayam Ungkep (Yield 10 PCS)
        $recipeAyam = Recipe::updateOrCreate(
            ['item_id' => $ayamUngkep->id, 'name' => 'Resep Ayam Ungkep Lengkuas'],
            ['yield_qty' => 10, 'notes' => 'Ayam diungkep sebelum dibakar/goreng']
        );

        $ingredientsAyam = [
            ['item' => $ayam, 'qty' => 2], // 2kg untuk 10 potong
            ['item' => $bawangPutih, 'qty' => 0.1],
            ['item' => $bawangMerah, 'qty' => 0.1],
        ];

        foreach ($ingredientsAyam as $ing) {
            RecipeIngredient::updateOrCreate(
                ['recipe_id' => $recipeAyam->id, 'ingredient_item_id' => $ing['item']->id],
                ['unit_id' => $ing['item']->base_unit_id, 'qty' => $ing['qty']]
            );
        }

        // 3. Recipe for Nasi Kotak Ayam Bakar (Yield 1 PCS) - BoM Concept
        $recipeNasiAyam = Recipe::updateOrCreate(
            ['item_id' => $nasiAyam->id, 'name' => 'Nasi Kotak Ayam Bakar'],
            ['yield_qty' => 1, 'notes' => 'Paket Nasi Kotak Ayam Bakar Lengkap']
        );

        $ingredientsNasiAyam = [
            ['item' => $beras, 'qty' => 0.15], // 150gr beras
            ['item' => $ayamUngkep, 'qty' => 1], // 1 potong ayam
            ['item' => $sambal, 'qty' => 0.02], // 20gr sambal
        ];

        foreach ($ingredientsNasiAyam as $ing) {
            RecipeIngredient::updateOrCreate(
                ['recipe_id' => $recipeNasiAyam->id, 'ingredient_item_id' => $ing['item']->id],
                ['unit_id' => $ing['item']->base_unit_id, 'qty' => $ing['qty']]
            );
        }

        // 4. Recipe for Nasi Kotak Rendang (Yield 1 PCS)
        $recipeNasiRendang = Recipe::updateOrCreate(
            ['item_id' => $nasiRendang->id, 'name' => 'Nasi Kotak Rendang Daging'],
            ['yield_qty' => 1, 'notes' => 'Paket Nasi Kotak Rendang Daging']
        );

        $ingredientsNasiRendang = [
            ['item' => $beras, 'qty' => 0.15],
            ['item' => $daging, 'qty' => 0.1], // 100gr daging
            ['item' => $santan, 'qty' => 0.2], // 200ml santan
            ['item' => $sambal, 'qty' => 0.02],
        ];

        foreach ($ingredientsNasiRendang as $ing) {
            RecipeIngredient::updateOrCreate(
                ['recipe_id' => $recipeNasiRendang->id, 'ingredient_item_id' => $ing['item']->id],
                ['unit_id' => $ing['item']->base_unit_id, 'qty' => $ing['qty']]
            );
        }
    }
}
