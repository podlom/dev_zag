<?php

namespace Aimix\Review;

use Aimix\Review\app\Observers\ReviewObserver;
use Aimix\Review\app\Models\Review;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/review.php';
    
    public function boot()
    {
        Review::observe(ReviewObserver::class);

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'review');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    
		  // Config

      $this->publishes([
          self::CONFIG_PATH => config_path('aimix/review.php'),
      ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'review'
        );

        $this->app->bind('review', function () {
            return new Review();
        });
    }
}
