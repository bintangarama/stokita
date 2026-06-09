<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
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
        $btl = Unit::where('code', 'BTL')->first();

        $items = [
            // RAW MATERIALS (Bahan Baku)
            [
                'sku' => 'RM-001',
                'name' => 'Beras Pandan Wangi',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 50,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-002',
                'name' => 'Ayam Broiler Utuh',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 20,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-003',
                'name' => 'Minyak Goreng',
                'type' => 'raw_material',
                'base_unit_id' => $lt->id,
                'reorder_threshold' => 10,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-004',
                'name' => 'Garam Meja',
                'type' => 'raw_material',
                'base_unit_id' => $gr->id,
                'reorder_threshold' => 500,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-005',
                'name' => 'Telur Ayam',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 5,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-006',
                'name' => 'Bawang Merah',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 2,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-007',
                'name' => 'Bawang Putih',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 1,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-008',
                'name' => 'Cabai Merah Keriting',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 2,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-009',
                'name' => 'Daging Sapi (Paha)',
                'type' => 'raw_material',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 5,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'RM-010',
                'name' => 'Santan Kelapa',
                'type' => 'raw_material',
                'base_unit_id' => $lt->id,
                'reorder_threshold' => 5,
                'current_stock' => 0,
                'average_cost' => 0,
            ],

            // COMPONENTS (Bahan Setengah Jadi)
            [
                'sku' => 'CP-001',
                'name' => 'Sambal Terasi Matang',
                'type' => 'component',
                'base_unit_id' => $kg->id,
                'reorder_threshold' => 2,
                'current_stock' => 0,
                'average_cost' => 0,
            ],
            [
                'sku' => 'CP-002',
                'name' => 'Ayam Ungkep Lengkuas',
                'type' => 'component',
                'base_unit_id' => $pcs->id,
                'reorder_threshold' => 40,
                'current_stock' => 0,
                'average_cost' => 0,
            ],

            // FINISH GOODS (Produk Jadi)
            [
                'sku' => 'FG-001',
                'name' => 'Nasi Kotak Ayam Bakar',
                'type' => 'finish_good',
                'base_unit_id' => $pcs->id,
                'reorder_threshold' => 0,
                'current_stock' => 0,
                'average_cost' => 0,
                'selling_price' => 25000,
            ],
            [
                'sku' => 'FG-002',
                'name' => 'Nasi Kotak Rendang Daging',
                'type' => 'finish_good',
                'base_unit_id' => $pcs->id,
                'reorder_threshold' => 0,
                'current_stock' => 0,
                'average_cost' => 0,
                'selling_price' => 35000,
            ],
            [
                'sku' => 'FG-003',
                'name' => 'Snack Box (3 Macam)',
                'type' => 'finish_good',
                'base_unit_id' => $pcs->id,
                'reorder_threshold' => 0,
                'current_stock' => 0,
                'average_cost' => 0,
                'selling_price' => 15000,
            ],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(['sku' => $item['sku']], $item);
        }
    }
}
