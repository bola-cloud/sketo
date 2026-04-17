<?php

namespace App\Providers;

use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Force queue to sync so NativePHP doesn't look for jobs table and throw 500s
        \Illuminate\Support\Facades\Config::set('queue.default', 'sync');

        // Run migrations for the local SQLite database used by NativePHP
        if (!\Illuminate\Support\Facades\Schema::hasTable('jobs')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                // Log the exact database error so we can fix SQLite incompatibilities
                \Illuminate\Support\Facades\Log::error('NativePHP Migration Error: ' . $e->getMessage());
                @file_put_contents(storage_path('logs/nativephp_migration_error.txt'), $e->getMessage());
            }
        }

        // Cleanup corrupted "Local Offline Admin" if it exists, or push seed if empty
        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $corruptUser = \App\Models\User::where('name', 'Local Offline Admin')->first();
            if ($corruptUser) {
                \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
            } elseif (\App\Models\User::count() === 0) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('NativePHP Seeder Error: ' . $e->getMessage());
                }
            }
        }

        Window::open()
            ->width(1280)
            ->height(800)
            ->title('Sketo - Advanced POS System');
            // ->url('https://www.cashier.infinitsmart.com/'); // Removed to ensure it loads the LOCAL offline-capable version!
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
