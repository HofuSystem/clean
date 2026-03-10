<?php
namespace Core\Logs\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Core\Entities\Helpers\PackageManger;

class LogsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
	 public function register(){
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
 	}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'logs');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'logs');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");

        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('test'),
        // ], 'public');
    }
}
