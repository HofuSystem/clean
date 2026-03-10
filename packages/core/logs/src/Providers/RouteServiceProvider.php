<?php
namespace Core\Logs\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Core\Entities\Helpers\PackageManger;

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
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    
}
