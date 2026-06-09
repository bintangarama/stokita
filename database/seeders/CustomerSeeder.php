<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'PT. Teknologi Maju',
                'phone' => '0215551234',
                'email' => 'info@teknologimaju.com',
                'address' => 'Sudirman Central Business District, Jakarta',
            ],
            [
                'name' => 'Bank Mandiri Cabang Bekasi',
                'phone' => '0218884321',
                'email' => 'bekasi@bankmandiri.co.id',
                'address' => 'Jl. Ahmad Yani No. 1, Bekasi',
            ],
            [
                'name' => 'Ibu Ratna (Arisan Ceria)',
                'phone' => '081398765432',
                'email' => 'ratna@gmail.com',
                'address' => 'Perumahan Harapan Indah, Blok A3/12, Bekasi',
            ],
            [
                'name' => 'Masjid Al-Ikhlas',
                'phone' => '0217776655',
                'email' => 'pengurus@alikhlas.org',
                'address' => 'Jl. Kebon Jeruk No. 8, Jakarta Barat',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['name' => $customer['name']], $customer);
        }
    }
}
