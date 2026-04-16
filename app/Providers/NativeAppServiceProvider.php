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
        // Run migrations for the local SQLite database used by NativePHP
        // to prevent 'no such table: jobs' errors.
        if (!\Illuminate\Support\Facades\Schema::hasTable('jobs')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                // Silently pass or log
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
