<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@stokita.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Units & Conversions
        $this->call(UnitSeeder::class);

        // 3. Customers & Suppliers
        Customer::factory(10)->create();
        Supplier::factory(5)->create();

        // 4. Items (Raw Materials, Components, Finish Goods)
        $units = Unit::all();

        // Raw Materials
        Item::factory(10)->create([
            'type' => 'raw_material',
            'base_unit_id' => $units->where('code', 'KG')->first()->id ?? $units->random()->id,
        ]);

        // Components
        Item::factory(5)->create([
            'type' => 'component',
            'base_unit_id' => $units->where('code', 'PCS')->first()->id ?? $units->random()->id,
        ]);

        // Finish Goods
        Item::factory(5)->create([
            'type' => 'finish_good',
            'base_unit_id' => $units->where('code', 'PCS')->first()->id ?? $units->random()->id,
        ]);
    }
}
