<?php


namespace Core\Settings\Providers;

use Core\Settings\Rules\ValidTime;
use Core\Settings\Services\BootService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;



class SettingsServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'settings');
        $this->loadViewsFrom(__DIR__ . '/../resources', 'settings');
		$this->loadMigrationsFrom(__DIR__."/../database/migrations");
        Validator::extend('time', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $value);
        });    
        $this->app->booted(function () {
            BootService::bootConfig();
        });
    }
}
