<?php

namespace App\Providers;

use App\Services\Pricing\AvailabilityChecker;
use App\Services\Pricing\LpCalculator;
use App\Services\Pricing\PapCalculator;
use App\Services\Pricing\PmpCalculator;
use App\Services\Pricing\QuoteService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PmpCalculator::class);
        $this->app->singleton(LpCalculator::class);
        $this->app->singleton(PapCalculator::class);
        $this->app->singleton(AvailabilityChecker::class);
        $this->app->singleton(QuoteService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
