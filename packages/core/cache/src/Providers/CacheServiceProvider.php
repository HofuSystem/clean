<?php
namespace Core\Cache\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Core\Entities\Helpers\PackageManger;

class CacheServiceProvider extends ServiceProvider
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


        $langPath = __DIR__ . '/../lang';
        $viewsPath = __DIR__ . '/../resources/views';
        $migrationsPath = __DIR__ . '/../database/migrations';
        if (is_dir($langPath)) { $this->loadTranslationsFrom($langPath, 'cache'); }
        if (is_dir($viewsPath)) { $this->loadViewsFrom($viewsPath, 'cache'); }
        if (is_dir($migrationsPath)) { $this->loadMigrationsFrom($migrationsPath); }
        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('test'),
        // ], 'public');
    }
}
