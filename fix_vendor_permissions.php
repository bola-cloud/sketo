<?php
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Initializing Permissions...\n";

$permissions = [
    'view-products',
    'create-products',
    'edit-products',
    'delete-products',
];

foreach ($permissions as $name) {
    Permission::firstOrCreate(['name' => $name]);
}

echo "Assigning Permissions to Roles...\n";

$roles = ['admin', 'owner'];
$allPerms = Permission::all();

foreach ($roles as $roleName) {
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        // Use Laratrust's syncPermissions
        $role->syncPermissions($allPerms);
        echo "Synchronized permissions for: $roleName\n";
    }
}
