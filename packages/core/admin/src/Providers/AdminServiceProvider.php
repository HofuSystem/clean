<?php


namespace Core\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Core\Admin\Models\Language;
use Core\Admin\Observers\LangObserver;

class AdminServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");
        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('test'),
        // ], 'public');
    }
}
