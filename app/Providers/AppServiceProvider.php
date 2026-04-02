<?php

namespace App\Providers;

use App\Services\Pricing\AvailabilityChecker;
use App\Services\Pricing\LpCalculator;
use App\Services\Pricing\PapCalculator;
use App\Services\Pricing\PmpCalculator;
use App\Services\Pricing\QuoteService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // Share the Settings helper with all views
        view()->composer('*', function ($view) {
            $view->with('settings', \App\Helpers\Settings::class);
        });
    }
}
