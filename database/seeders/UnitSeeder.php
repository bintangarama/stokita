<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $kg = Unit::create(['code' => 'KG', 'name' => 'Kilogram']);
        $gr = Unit::create(['code' => 'GR', 'name' => 'Gram']);
        $pcs = Unit::create(['code' => 'PCS', 'name' => 'Pieces']);
        $box = Unit::create(['code' => 'BOX', 'name' => 'Box']);
        $lt = Unit::create(['code' => 'LT', 'name' => 'Liter']);
        $ml = Unit::create(['code' => 'ML', 'name' => 'Mililiter']);

        // Konversi KG ke GR (1 KG = 1000 GR)
        UnitConversion::create([
            'unit_from_id' => $kg->id,
            'unit_to_id' => $gr->id,
            'factor' => 1000,
        ]);

        // Konversi BOX ke PCS (1 BOX = 12 PCS)
        UnitConversion::create([
            'unit_from_id' => $box->id,
            'unit_to_id' => $pcs->id,
            'factor' => 12,
        ]);

        // Konversi LT ke ML (1 LT = 1000 ML)
        UnitConversion::create([
            'unit_from_id' => $lt->id,
            'unit_to_id' => $ml->id,
            'factor' => 1000,
        ]);
    }
}
