<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users'],
            ['name' => 'Restore Users', 'slug' => 'users.restore', 'group' => 'users'],

            // Role Management
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'roles'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'group' => 'roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'group' => 'roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'group' => 'roles'],
            ['name' => 'Restore Roles', 'slug' => 'roles.restore', 'group' => 'roles'],

            // Permission Management
            ['name' => 'View Permissions', 'slug' => 'permissions.view', 'group' => 'permissions'],
            ['name' => 'Assign Permissions', 'slug' => 'permissions.assign', 'group' => 'permissions'],

            // Product Categories
            ['name' => 'View Product Categories', 'slug' => 'product-categories.view', 'group' => 'product-categories'],
            ['name' => 'Create Product Categories', 'slug' => 'product-categories.create', 'group' => 'product-categories'],
            ['name' => 'Edit Product Categories', 'slug' => 'product-categories.edit', 'group' => 'product-categories'],
            ['name' => 'Delete Product Categories', 'slug' => 'product-categories.delete', 'group' => 'product-categories'],
            ['name' => 'Restore Product Categories', 'slug' => 'product-categories.restore', 'group' => 'product-categories'],

            // Units
            ['name' => 'View Units', 'slug' => 'units.view', 'group' => 'units'],
            ['name' => 'Create Units', 'slug' => 'units.create', 'group' => 'units'],
            ['name' => 'Edit Units', 'slug' => 'units.edit', 'group' => 'units'],
            ['name' => 'Delete Units', 'slug' => 'units.delete', 'group' => 'units'],
            ['name' => 'Restore Units', 'slug' => 'units.restore', 'group' => 'units'],

            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'products'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'products'],
            ['name' => 'Restore Products', 'slug' => 'products.restore', 'group' => 'products'],

            // Market Categories
            ['name' => 'View Market Categories', 'slug' => 'market-categories.view', 'group' => 'market-categories'],
            ['name' => 'Create Market Categories', 'slug' => 'market-categories.create', 'group' => 'market-categories'],
            ['name' => 'Edit Market Categories', 'slug' => 'market-categories.edit', 'group' => 'market-categories'],
            ['name' => 'Delete Market Categories', 'slug' => 'market-categories.delete', 'group' => 'market-categories'],
            ['name' => 'Restore Market Categories', 'slug' => 'market-categories.restore', 'group' => 'market-categories'],

            // Markets
            ['name' => 'View Markets', 'slug' => 'markets.view', 'group' => 'markets'],
            ['name' => 'Create Markets', 'slug' => 'markets.create', 'group' => 'markets'],
            ['name' => 'Edit Markets', 'slug' => 'markets.edit', 'group' => 'markets'],
            ['name' => 'Delete Markets', 'slug' => 'markets.delete', 'group' => 'markets'],
            ['name' => 'Restore Markets', 'slug' => 'markets.restore', 'group' => 'markets'],

            // Market Products (Pricing)
            ['name' => 'View Market Products', 'slug' => 'market-products.view', 'group' => 'market-products'],
            ['name' => 'Create Market Products', 'slug' => 'market-products.create', 'group' => 'market-products'],
            ['name' => 'Edit Market Products', 'slug' => 'market-products.edit', 'group' => 'market-products'],
            ['name' => 'Delete Market Products', 'slug' => 'market-products.delete', 'group' => 'market-products'],
            ['name' => 'Restore Market Products', 'slug' => 'market-products.restore', 'group' => 'market-products'],

            // Audit Logs
            ['name' => 'View Audit Logs', 'slug' => 'audit-logs.view', 'group' => 'audit-logs'],

            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard'],
            ['name' => 'View Analytics', 'slug' => 'dashboard.analytics', 'group' => 'dashboard'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
