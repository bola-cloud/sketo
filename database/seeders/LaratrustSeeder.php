<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Laratrust\Models\LaratrustRole;
// use Laratrust\Models\LaratrustPermission;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define Permissions
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Categories
            'view-categories',
            'create-categories',
            'edit-categories',

            // Products
            'view-products',
            'create-products',
            'edit-products',
            'view-product-reports',

            // Cashier
            'view-cart',
            'manage-invoices',

            // Purchases
            'view-purchases',
            'create-purchases',
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            LaratrustPermission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $admin = LaratrustRole::create(['name' => 'admin']);
        $cashier = LaratrustRole::create(['name' => 'cashier']);
        $supervisor = LaratrustRole::create(['name' => 'supervisor']);

        // Assign all permissions to admin
        $admin->attachPermissions(LaratrustPermission::all());

        // Assign specific permissions to cashier
        $cashier->attachPermissions([
            'view-dashboard',
            'view-cart',
            'manage-invoices',
            'view-products',
        ]);

        // Assign specific permissions to supervisor
        $supervisor->attachPermissions([
            'view-dashboard',
            'view-categories',
            'edit-categories',
            'view-products',
            'edit-products',
            'view-product-reports',
            'view-purchases',
        ]);
    }
}
