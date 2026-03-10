<?php


namespace Core\PaymentGateways\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;



class PaymentGatewaysServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'payment-gateways');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'payment-gateways');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");
    }
}
