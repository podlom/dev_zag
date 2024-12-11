<?php

namespace Aimix\Feedback;

use Illuminate\Support\Facades\View;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/feedback.php';

    public function boot()
    {
	    //dd(__DIR__);
	    
	    //Translation
		  $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'feedback');
    
	    // Migrations
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');
	    
	    // Routes
    	$this->loadRoutesFrom(__DIR__.'/routes/backpack/routes.php');
    
		  // Config
      $this->publishes([
          self::CONFIG_PATH => config_path('aimix/feedback.php'),
      ], 'config');
        
        
	    // $this->publishes([
	    //     __DIR__.'/../database/migrations/' => database_path('migrations')
	    // ], 'migrations');
      
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'feedback'
        );

        $this->app->bind('feedback', function () {
            return new Feedback();
        });
    }
}
