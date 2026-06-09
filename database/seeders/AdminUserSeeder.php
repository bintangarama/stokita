<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@stokita.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('adminadmin'),
            ]
        );

        $user->assignRole('admin');
    }
}
