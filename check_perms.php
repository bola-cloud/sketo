<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "--- ALL PERMISSIONS ---\n";
foreach (Permission::all() as $p) {
    echo $p->name . "\n";
}

echo "\n--- OWNER ROLE PERMISSIONS ---\n";
$role = Role::where('name', 'owner')->first();
if ($role) {
    foreach ($role->permissions as $p) {
        echo $p->name . "\n";
    }
} else {
    echo "Owner role not found\n";
}
