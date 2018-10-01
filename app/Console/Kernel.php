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
        Commands\GetFuturesFromCME::class,
		Commands\GetOptionExpirations::class,
        Commands\GetWeeklyFromFile::class,
        Commands\GetMonthlyFromFile::class,
        Commands\GetUsaFromFile::class
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
			->hourlyAt(17)
			->withoutOverlapping();

		$schedule->command('getYahooRates')
			->twiceDaily()
			->withoutOverlapping();

		$schedule->command('sendEndSubscriptionEmail')
			->daily()
			->withoutOverlapping();

	    	$schedule->command('getOptionExpirations')
			->daily()
			->withoutOverlapping();
	  
		$schedule->command('updateUserSubscriptions')
			->twiceDaily(1, 18)
			->withoutOverlapping();

		$schedule->command('checkPayments')
			->everyMinute()
			->withoutOverlapping();

		$schedule->command('getWeeklyFromFile')
			->everyMinute()
			->withoutOverlapping();
		
		$schedule->command('getMonthlyFromFile')
			->everyMinute()
			->withoutOverlapping();
		
		$schedule->command('getUsaFromFile')
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
