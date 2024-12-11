<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
/*
        $schedule->command('backup:clean')->daily()->at('04:00');
        $schedule->command('backup:run')->daily()->at('05:00');
*/
        
        $schedule->command('sitemap:generate')->daily();
        $schedule->command('images:optimize')->twiceMonthly(1, 16, '2:00');
        $schedule->command('telegram:news')->weekdays()->everyFiveMinutes()->between('9:00', '18:00');
        $schedule->command('telegram:promotions')->weekdays()->everyFiveMinutes()->between('9:15', '18:00');
        $schedule->command('telegram:products')->weekdays()->everyFiveMinutes()->between('9:30', '18:00');
        $schedule->command('cache:refresh')->daily()->at('03:00');
        // $schedule->command('send:news')->everyWeek();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
