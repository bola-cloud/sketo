<?php

namespace App\Providers;

use App\Models\CustomerReturn;
use App\Models\SupplierReturn;
use App\Observers\CustomerReturnObserver;
use App\Observers\SupplierReturnObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CustomerReturn::observe(CustomerReturnObserver::class);
        SupplierReturn::observe(SupplierReturnObserver::class);
    }
}
