<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = [
    'roles',
    'permissions',
    'permission_role',
    'users',
    'role_user',
    'packages',
    'vendors'
];

echo "-- SKETO CORE DATA EXPORT --\n\n";
echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

foreach ($tables as $table) {
    $rows = DB::table($table)->get();

    if ($rows->count() > 0) {
        echo "-- Table: $table\n";
        echo "TRUNCATE TABLE `$table`;\n";

        foreach ($rows as $row) {
            $array = (array) $row;
            $columns = array_keys($array);
            $values = array_values($array);

            $escapedValues = array_map(function ($value) {
                if ($value === null)
                    return 'NULL';
                return "'" . str_replace("'", "''", $value) . "'";
            }, $values);

            echo "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
        }
        echo "\n";
    }
}

echo "SET FOREIGN_KEY_CHECKS = 1;\n";
