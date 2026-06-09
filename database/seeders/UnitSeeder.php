<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['code' => 'KG', 'name' => 'Kilogram'],
            ['code' => 'GR', 'name' => 'Gram'],
            ['code' => 'PCS', 'name' => 'Pieces'],
            ['code' => 'BOX', 'name' => 'Box'],
            ['code' => 'LT', 'name' => 'Liter'],
            ['code' => 'ML', 'name' => 'Mililiter'],
            ['code' => 'PAC', 'name' => 'Pack'],
            ['code' => 'BTL', 'name' => 'Botol'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['code' => $unit['code']], $unit);
        }

        // Conversions
        $kg = Unit::where('code', 'KG')->first();
        $gr = Unit::where('code', 'GR')->first();
        $lt = Unit::where('code', 'LT')->first();
        $ml = Unit::where('code', 'ML')->first();

        $conversions = [
            [
                'unit_from_id' => $kg->id,
                'unit_to_id' => $gr->id,
                'factor' => 1000,
            ],
            [
                'unit_from_id' => $gr->id,
                'unit_to_id' => $kg->id,
                'factor' => 0.001,
            ],
            [
                'unit_from_id' => $lt->id,
                'unit_to_id' => $ml->id,
                'factor' => 1000,
            ],
            [
                'unit_from_id' => $ml->id,
                'unit_to_id' => $lt->id,
                'factor' => 0.001,
            ],
        ];

        foreach ($conversions as $conv) {
            UnitConversion::updateOrCreate(
                [
                    'unit_from_id' => $conv['unit_from_id'],
                    'unit_to_id' => $conv['unit_to_id'],
                ],
                ['factor' => $conv['factor']]
            );
        }
    }
}
