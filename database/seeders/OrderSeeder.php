<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\SalesService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        
        // Customers
        $custTech = Customer::where('name', 'PT. Teknologi Maju')->first();
        $custRatna = Customer::where('name', 'Ibu Ratna (Arisan Ceria)')->first();

        // Finish Goods
        $nasiAyam = Item::where('sku', 'FG-001')->first();
        $snackBox = Item::where('sku', 'FG-003')->first();

        // 1. Order: PT. Teknologi Maju (Confirmed - Will deduct stock)
        $o1 = Order::create([
            'customer_id' => $custTech->id,
            'order_date' => Carbon::now(),
            'status' => 'confirmed',
            'created_by' => $admin->id,
            'notes' => 'Pesanan makan siang meeting direksi',
        ]);

        OrderItem::create([
            'order_id' => $o1->id,
            'item_id' => $nasiAyam->id,
            'unit_id' => $nasiAyam->base_unit_id,
            'qty' => 10,
            'unit_price' => $nasiAyam->selling_price,
            'line_total' => 10 * $nasiAyam->selling_price,
        ]);

        SalesService::recalculateTotal($o1);
        SalesService::process($o1);

        // 2. Order: Ibu Ratna (Draft - Will NOT deduct stock yet)
        $o2 = Order::create([
            'customer_id' => $custRatna->id,
            'order_date' => Carbon::now()->addDay(),
            'status' => 'draft',
            'created_by' => $admin->id,
            'notes' => 'Rencana pesanan arisan minggu depan',
        ]);

        OrderItem::create([
            'order_id' => $o2->id,
            'item_id' => $snackBox->id,
            'unit_id' => $snackBox->base_unit_id,
            'qty' => 25,
            'unit_price' => $snackBox->selling_price,
            'line_total' => 25 * $snackBox->selling_price,
        ]);

        SalesService::recalculateTotal($o2);
        SalesService::process($o2);
    }
}
