<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        //
        // PERMISSIONS
        $permissions = [
            // Items
            'view items',
            'create items',
            'edit items',
            'delete items',

            // Purchases
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',

            // Orders
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'confirm orders',
            'complete orders',

            // Productions
            'view productions',
            'create productions',

            // Reports
            'view reports',

            // Users
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ROLES
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // ASSIGN PERMISSIONS
        $admin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'view items',
            'create items',
            'edit items',

            'view purchases',
            'create purchases',
            'edit purchases',

            'view orders',
            'create orders',
            'edit orders',
            'confirm orders',
            'complete orders',

            'view productions',
            'create productions',

            'view reports',
        ]);

        $staff->givePermissionTo([
            'view orders',
            'create orders',
            'view purchases',
            'create purchases',
        ]);
    }
}
