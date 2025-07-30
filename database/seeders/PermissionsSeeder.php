<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating permissions...');

        // Define Permissions in Arabic
        $permissions = [
            'عرض لوحة التحكم',
            'عرض الفئات',
            'إنشاء الفئات',
            'تعديل الفئات',
            'عرض المنتجات',
            'إنشاء المنتجات',
            'تعديل المنتجات',
            'عرض تقارير المنتجات',
            'عرض عربة التسوق',
            'إدارة الفواتير',
            'عرض الفواتير',
            'إنشاء الفواتير',
        ];

        $createdCount = 0;
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            if ($perm->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("Permissions: {$createdCount} created, " . (count($permissions) - $createdCount) . " already existed");

                $this->command->info('Creating roles...');

        // Define Roles in Arabic
        $roles = [
            'admin' => 'مسؤول',
            'cashier' => 'الكاشير',
            'supervisor' => 'المشرف'
        ];

        $rolesCreatedCount = 0;
        foreach ($roles as $roleKey => $roleName) {
            $role = Role::firstOrCreate(
                ['name' => $roleKey],
                ['display_name' => $roleName]
            );

            if ($role->wasRecentlyCreated) {
                $rolesCreatedCount++;
            }

            // Assign Permissions to Roles
            if ($roleKey === 'admin') {
                $role->permissions()->sync(Permission::all()->pluck('id'));
                $this->command->info("Admin role: assigned all permissions");
            } elseif ($roleKey === 'cashier') {
                $permissions = Permission::whereIn('name', [
                    'عرض لوحة التحكم',
                    'عرض عربة التسوق',
                    'إدارة الفواتير',
                    'عرض المنتجات',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                $this->command->info("Cashier role: assigned {$permissions->count()} permissions");
            } elseif ($roleKey === 'supervisor') {
                $permissions = Permission::whereIn('name', [
                    'عرض لوحة التحكم',
                    'عرض الفئات',
                    'تعديل الفئات',
                    'عرض المنتجات',
                    'تعديل المنتجات',
                    'عرض تقارير المنتجات',
                    'عرض الفواتير',
                ])->pluck('id');
                $role->permissions()->sync($permissions);
                $this->command->info("Supervisor role: assigned {$permissions->count()} permissions");
            }
        }

        $this->command->info("Roles: {$rolesCreatedCount} created, " . (count($roles) - $rolesCreatedCount) . " already existed");
        $this->command->info('✅ Permissions and roles seeding completed!');
    }
}
