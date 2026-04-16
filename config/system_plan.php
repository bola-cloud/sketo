<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default System Plan
    |--------------------------------------------------------------------------
    |
    | This value determines the active pricing tier for this standalone installation.
    | Accepted values: 'basic', 'professional', 'enterprise'.
    | Defaulting to 'enterprise' ensures existing live systems do not lose access
    | to any features if the .env variable is not explicitly set.
    |
    */

    'active_plan' => env('SYSTEM_PLAN', 'enterprise'),

    /*
    |--------------------------------------------------------------------------
    | Plan Features Matrix
    |--------------------------------------------------------------------------
    |
    | Define the features available for each tier.
    |
    */

    'plans' => [
        'basic' => [
            'pos' => true,
            'products' => true,
            'purchases' => true,
            'shifts' => true,
            'invoices' => true,
            'reports_basic' => true,
        ],

        'professional' => [
            'pos' => true,
            'products' => true,
            'purchases' => true,
            'shifts' => true,
            'invoices' => true,
            'reports_basic' => true,
            
            // Professional additions
            'users' => true,
            'clients' => true,
            'suppliers' => true,
            'returns' => true,
            'reports_advanced' => true,
            'exports' => true,
        ],

        'enterprise' => [
            'pos' => true,
            'products' => true,
            'purchases' => true,
            'shifts' => true,
            'invoices' => true,
            'reports_basic' => true,
            'users' => true,
            'clients' => true,
            'suppliers' => true,
            'returns' => true,
            'reports_advanced' => true,
            'exports' => true,

            // Enterprise additions
            'treasury' => true,
            'audit_logs' => true, // quantity_updates, transfers
            'theme_customization' => true,
            'cloud_sync' => true,
        ]
    ],

];
