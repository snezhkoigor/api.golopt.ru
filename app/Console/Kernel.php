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
        Commands\GetForwardPointsFromFTP::class,
        Commands\GetYahooRates::class,
        Commands\SendEndSubscriptionEmail::class,
        Commands\UpdateUserSubscriptions::class,
        Commands\CheckPayments::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('getForwardPointsFromFTP')
            ->hourly()
            ->withoutOverlapping();


        $schedule->command('getYahooRates')
            ->hourly()
            ->withoutOverlapping();

        $schedule->command('sendEndSubscriptionEmail')
            ->daily()
            ->withoutOverlapping();

        $schedule->command('updateUserSubscriptions')
            ->twiceDaily(1, 18)
            ->withoutOverlapping();

        $schedule->command('checkPayments')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}