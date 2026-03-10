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
        // Register the global observer for all models
        Coupon::observe(GlobalModelObserver::class);
    }
}
