<?php


namespace Core\Users\Providers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Core\Users\Middleware\MayLogin;
use Core\Users\Middleware\SingleUserAllowed;

class UsersServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'users');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'users');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");
    }

}
