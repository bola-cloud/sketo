<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class InitMultiVendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Super Admin Role
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
        ], [
            'display_name' => 'Platform Owner',
            'description' => 'Can manage all vendors and system settings.',
        ]);

        // 2. Create Vendor Owner Role
        $owner = Role::firstOrCreate([
            'name' => 'owner',
        ], [
            'display_name' => 'Store Owner',
            'description' => 'Can manage their own store and staff.',
        ]);

        // 3. Assign User 1 as Super Admin (if exists)
        $user = User::find(1);
        if ($user) {
            // First user becomes Super Admin of the whole platform
            if (!$user->hasRole('super_admin')) {
                $user->addRole($superAdmin);
            }
        }
    }
}
