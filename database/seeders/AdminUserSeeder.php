<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminUser = User::where('email', 'bola.ishak41@gmail.com')->first();

        if ($adminUser) {
            // Update existing user
            $adminUser->update([
                'name' => 'Bola Ishak',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin user updated successfully!');
        } else {
            // Create new admin user
            $adminUser = User::create([
                'name' => 'Bola Ishak',
                'email' => 'bola.ishak41@gmail.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);
            $this->command->info('Admin user created successfully!');
        }

        // Make sure admin role exists
        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ], [
            'display_name' => 'مسؤول',
            'description' => 'Administrator with full access'
        ]);

        // Detach all roles first, then attach admin role
        $adminUser->roles()->detach();
        $adminUser->roles()->attach($adminRole->id, ['user_type' => get_class($adminUser)]);

        // Make sure the admin role has all permissions
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
            $this->command->info("Admin role assigned {$allPermissions->count()} permissions");
        } else {
            $this->command->warn('No permissions found. Run PermissionsSeeder first.');
        }

        $this->command->info('✅ Admin User Details:');
        $this->command->info('   Name: Bola Ishak');
        $this->command->info('   Email: bola.ishak41@gmail.com');
        $this->command->info('   Password: 12345678');
        $this->command->info('   Role: admin');
        $this->command->info('   Permissions: All available permissions');
    }
}
