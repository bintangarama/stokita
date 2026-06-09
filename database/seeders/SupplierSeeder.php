<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PD. Sayur Segar Jaya',
                'contact_person' => 'Bpk. Ahmad',
                'phone' => '081234567890',
                'email' => 'ahmad@sayursegar.com',
                'address' => 'Pasar Induk Kramat Jati, Jakarta',
                'notes' => 'Supplier sayuran segar harian',
            ],
            [
                'name' => 'Rumah Potong Ayam Barokah',
                'contact_person' => 'Ibu Siti',
                'phone' => '081234567891',
                'email' => 'siti@ayambarokah.com',
                'address' => 'Jl. Peternakan No. 45, Bogor',
                'notes' => 'Supplier ayam broiler dan kampung',
            ],
            [
                'name' => 'Toko Sembako Makmur',
                'contact_person' => 'Bpk. Liem',
                'phone' => '081234567892',
                'email' => 'liem@sembakomakmur.com',
                'address' => 'Jl. Raya Perdagangan No. 10, Bekasi',
                'notes' => 'Supplier beras, minyak, dan gula',
            ],
            [
                'name' => 'UD. Bumbu Nusantara',
                'contact_person' => 'Ibu Wati',
                'phone' => '081234567893',
                'email' => 'wati@bumbunusantara.com',
                'address' => 'Pasar Minggu, Jakarta Selatan',
                'notes' => 'Supplier bumbu dapur dan rempah-rempah',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(['name' => $supplier['name']], $supplier);
        }
    }
}
