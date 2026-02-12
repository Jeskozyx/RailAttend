<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ScanReport;
use App\Observers\ScanReportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ScanReport::observe(ScanReportObserver::class);
    }
}
