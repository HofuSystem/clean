<?php

namespace Core\Financials\Providers;

use Illuminate\Support\ServiceProvider;

class FinancialsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/dashboard.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'financials');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
