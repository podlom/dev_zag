<?php

namespace Dan\UploadImage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class UploadImageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // What need install. (composer dump-autoload -o)
        $this->publishes([
            __DIR__ . '/../config/upload-image.php' => $this->app->configPath() . '/' . 'upload-image.php',
        ], 'config');

        // Copy js to resources/assets/js Need file to elixir.
        $this->publishes([__DIR__ . '/../public/' => base_path('resources/assets/js')]);

        // Routing
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/upload-image.php', 'upload-image');

        $this->app->bind('upload-image', function (Container $app) {
            $config = $app['config']['upload-image']['image-settings'];

            return new UploadImage($config);
        });
    }
}