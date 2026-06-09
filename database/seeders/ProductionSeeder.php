<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use App\Services\ProductionService;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        // Recipes
        $recipeSambal = Recipe::where('name', 'Resep Sambal Terasi Standar')->first();
        $recipeAyamUngkep = Recipe::where('name', 'Resep Ayam Ungkep Lengkuas')->first();
        $recipeNasiAyam = Recipe::where('name', 'Nasi Kotak Ayam Bakar')->first();

        // 1. Produksi Sambal (2 KG)
        ProductionService::produce(
            recipeId: $recipeSambal->id,
            outputQty: 2,
            userId: $admin->id
        );

        // 2. Produksi Ayam Ungkep (20 PCS)
        ProductionService::produce(
            recipeId: $recipeAyamUngkep->id,
            outputQty: 20,
            userId: $admin->id
        );

        // 3. Produksi Nasi Kotak Ayam Bakar (15 PCS)
        ProductionService::produce(
            recipeId: $recipeNasiAyam->id,
            outputQty: 15,
            userId: $admin->id
        );
    }
}
