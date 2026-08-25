<?php

namespace App\Providers;

use App\Support\MarketplaceData;
use Illuminate\Support\Facades\View;
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
        // Share the 12 mock marketplace products with all views and components
        View::share('likhaeProducts', MarketplaceData::all());
    }
}
