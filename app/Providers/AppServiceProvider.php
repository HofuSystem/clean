<?php

namespace App\Providers;

use App\Observers\GlobalModelObserver;
use Core\Coupons\Models\Coupon;
use Core\Settings\Models\CoreModel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        // Model::preventLazyLoading(! app()->isProduction());
        // Register the global observer for all models
        Coupon::observe(GlobalModelObserver::class);

        // Load core package migrations in testing environment
        if ($this->app->environment('testing')) {
            $paths = glob(base_path('packages/core/*/src/database/migrations'));
            foreach ($paths as $migrationPath) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }
}
