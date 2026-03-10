<?php


namespace Core\MediaCenter\Providers;
use Illuminate\Support\ServiceProvider;

class MediaCenterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot()
    {

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'media');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'media');
        $this->loadMigrationsFrom(__DIR__."/../database/migrations");
    }
}
