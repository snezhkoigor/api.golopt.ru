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
        Commands\CheckPayments::class,
        Commands\GetFuturesFromCME::class
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
			->hourlyAt(7)
			->withoutOverlapping();

		$schedule->command('getFuturesFromCME')
			->hourlyAt(7)
			->withoutOverlapping();


		$schedule->command('getYahooRates')
			->twiceDaily()
			->withoutOverlapping();

		$schedule->command('sendEndSubscriptionEmail')
			->daily()
			->withoutOverlapping();

		$schedule->command('updateUserSubscriptions')
			->twiceDaily(1, 18)
			->withoutOverlapping();

		$schedule->command('checkPayments')
			->everyMinute()
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
