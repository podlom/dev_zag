<?php

namespace Aimix\Currency;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/currency.php';
    
    public function boot()
    {
      $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'currency');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    
		  // Config

      $this->publishes([
          self::CONFIG_PATH => config_path('aimix/currency.php'),
      ], 'config');
    }
    
    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'currency'
        );

        $this->app->bind('currency', function () {
            return new Currency();
        });
    }
}
