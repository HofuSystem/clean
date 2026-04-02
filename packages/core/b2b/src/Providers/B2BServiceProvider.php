<?php


namespace Core\B2B\Providers;

use Illuminate\Support\ServiceProvider;


class B2BServiceProvider extends ServiceProvider
{


    protected $observers = [
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'b2b');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'b2b');
        $this->loadMigrationsFrom(__DIR__ . "/../database/migrations");
        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('test'),
        // ], 'public');
    }
}
