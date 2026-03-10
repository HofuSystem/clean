<?php


namespace Core\Workers\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    
    

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
       

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../routes/api.php');
            Route::middleware('web')
                ->group(__DIR__.'/../routes/web.php');
            Route::middleware('web')
                ->group(__DIR__.'/../routes/dashboard.php');
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    
}
