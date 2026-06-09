<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        
        // Suppliers
        $supSembako = Supplier::where('name', 'Toko Sembako Makmur')->first();
        $supAyam = Supplier::where('name', 'Rumah Potong Ayam Barokah')->first();
        $supSayur = Supplier::where('name', 'PD. Sayur Segar Jaya')->first();

        // Items
        $beras = Item::where('sku', 'RM-001')->first();
        $ayam = Item::where('sku', 'RM-002')->first();
        $minyak = Item::where('sku', 'RM-003')->first();
        $bawangMerah = Item::where('sku', 'RM-006')->first();
        $bawangPutih = Item::where('sku', 'RM-007')->first();

        // 1. Purchase: Sembako (Beras & Minyak)
        $p1 = Purchase::create([
            'supplier_id' => $supSembako->id,
            'invoice_no' => 'INV/' . now()->format('Ymd') . '/001',
            'purchase_date' => Carbon::now()->subDays(5),
            'created_by' => $admin->id,
            'notes' => 'Stok awal beras dan minyak',
        ]);

        $items1 = [
            ['item' => $beras, 'qty' => 100, 'price' => 14500],
            ['item' => $minyak, 'qty' => 50, 'price' => 17000],
        ];

        foreach ($items1 as $row) {
            PurchaseItem::create([
                'purchase_id' => $p1->id,
                'item_id' => $row['item']->id,
                'unit_id' => $row['item']->base_unit_id,
                'qty' => $row['qty'],
                'unit_price' => $row['price'],
                'line_total' => $row['qty'] * $row['price'],
            ]);
        }
        $p1->update(['total_amount' => $p1->items->sum('line_total')]);

        // 2. Purchase: Ayam
        $p2 = Purchase::create([
            'supplier_id' => $supAyam->id,
            'invoice_no' => 'INV/' . now()->format('Ymd') . '/002',
            'purchase_date' => Carbon::now()->subDays(3),
            'created_by' => $admin->id,
            'notes' => 'Pembelian ayam segar',
        ]);

        PurchaseItem::create([
            'purchase_id' => $p2->id,
            'item_id' => $ayam->id,
            'unit_id' => $ayam->base_unit_id,
            'qty' => 30,
            'unit_price' => 34000,
            'line_total' => 30 * 34000,
        ]);
        $p2->update(['total_amount' => $p2->items->sum('line_total')]);

        // 3. Purchase: Sayuran & Bumbu
        $p3 = Purchase::create([
            'supplier_id' => $supSayur->id,
            'invoice_no' => 'INV/' . now()->format('Ymd') . '/003',
            'purchase_date' => Carbon::now()->subDays(1),
            'created_by' => $admin->id,
            'notes' => 'Stok bumbu dapur dan sayuran',
        ]);

        $cabai = Item::where('sku', 'RM-008')->first();
        $items3 = [
            ['item' => $bawangMerah, 'qty' => 10, 'price' => 32000],
            ['item' => $bawangPutih, 'qty' => 5, 'price' => 38000],
            ['item' => $cabai, 'qty' => 10, 'price' => 45000],
        ];

        foreach ($items3 as $row) {
            PurchaseItem::create([
                'purchase_id' => $p3->id,
                'item_id' => $row['item']->id,
                'unit_id' => $row['item']->base_unit_id,
                'qty' => $row['qty'],
                'unit_price' => $row['price'],
                'line_total' => $row['qty'] * $row['price'],
            ]);
        }
        $p3->update(['total_amount' => $p3->items->sum('line_total')]);
    }
}
