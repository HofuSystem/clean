<?php


namespace Core\Workers\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;



class WorkersServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'workers');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'workers');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");
        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('test'),
        // ], 'public');
    }
}
