<?php

namespace Aimix\aimix;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/aimix.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('aimix/aimix.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'aimix'
        );

        $this->app->bind('aimix', function () {
            return new aimix();
        });
    }
}
