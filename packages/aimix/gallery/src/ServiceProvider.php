<?php

namespace Aimix\Gallery;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/gallery.php';

    public function boot()
    {
        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        
        // Routes
        $this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
        
        $this->publishes([
            self::CONFIG_PATH => config_path('gallery.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'gallery'
        );

        $this->app->bind('gallery', function () {
            return new Gallery();
        });
    }
}
