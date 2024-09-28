<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Define Roles in Arabic
        $roles = [
            'admin' => 'مسؤول',
            'cashier' => 'الكاشير',
            'supervisor' => 'المشرف'
        ];

        foreach ($roles as $roleKey => $roleName) {
            $role = Role::create(['name' => $roleKey, 'display_name' => $roleName]);

            // Assign Permissions to Roles
            if ($roleKey === 'admin') {
                $role->permissions()->sync(Permission::all()->pluck('id'));
            } elseif ($roleKey === 'cashier') {
                $role->permissions()->sync(Permission::whereIn('name', [
                    'عرض لوحة التحكم',
                    'عرض عربة التسوق',
                    'إدارة الفواتير',
                    'عرض المنتجات',
                ])->pluck('id'));
            } elseif ($roleKey === 'supervisor') {
                $role->permissions()->sync(Permission::whereIn('name', [
                    'عرض لوحة التحكم',
                    'عرض الفئات',
                    'تعديل الفئات',
                    'عرض المنتجات',
                    'تعديل المنتجات',
                    'عرض تقارير المنتجات',
                    'عرض الفواتير',
                ])->pluck('id'));
            }
        }
    }
}
