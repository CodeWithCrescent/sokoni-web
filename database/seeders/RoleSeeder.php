<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin role - gets all permissions
        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'is_default' => false,
            ]
        );
        $admin->syncPermissions(Permission::pluck('id')->toArray());

        // Customer role
        $customer = Role::updateOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Customer',
                'description' => 'Regular customer who can browse and order products',
                'is_default' => true,
            ]
        );
        $customerPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'products.view',
            'markets.view',
            'market-products.view',
        ])->pluck('id')->toArray();
        $customer->syncPermissions($customerPermissions);

        // Collector role
        $collector = Role::updateOrCreate(
            ['slug' => 'collector'],
            [
                'name' => 'Market Collector',
                'description' => 'Collects and packs orders at markets',
                'is_default' => false,
            ]
        );
        $collectorPermissions = Permission::whereIn('slug', [
            'dashboard.view',
            'products.view',
            'markets.view',
            'market-products.view',
        ])->pluck('id')->toArray();
        $collector->syncPermissions($collectorPermissions);

        // Driver role
        $driver = Role::updateOrCreate(
            ['slug' => 'driver'],
            [
                'name' => 'Delivery Driver',
                'description' => 'Delivers orders to customers',
                'is_default' => false,
            ]
        );
        $driverPermissions = Permission::whereIn('slug', [
            'dashboard.view',
        ])->pluck('id')->toArray();
        $driver->syncPermissions($driverPermissions);
    }
}
