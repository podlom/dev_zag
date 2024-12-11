<?php

namespace Aimix\Promotion;

use Aimix\Promotion\app\Observers\PromotionObserver;
use Aimix\Promotion\app\Models\Promotion;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/promotion.php';
    
    public function boot()
    {
        
      Promotion::observe(PromotionObserver::class);

      $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'promotion');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    
		  // Config

      $this->publishes([
          self::CONFIG_PATH => config_path('aimix/promotion.php'),
      ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'promotion'
        );

        $this->app->bind('promotion', function () {
            return new Promotion();
        });
    }
}
