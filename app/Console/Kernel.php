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
        Commands\GetUsaFromFile::class,
        Commands\GetWednesdayFromFile::class
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
			->weekdays()
			->withoutOverlapping();

		$schedule->command('getFuturesFromCME')
			->hourlyAt(17)
			->weekdays()
			->withoutOverlapping();

		$schedule->command('getYahooRates')
			->twiceDaily()
			->withoutOverlapping();

		$schedule->command('sendEndSubscriptionEmail')
			->daily()
			->withoutOverlapping();

        $schedule->command('getOptionExpirations')
			->daily()
		    ->weekdays()
			->withoutOverlapping();
	  
		$schedule->command('updateUserSubscriptions')
			->twiceDaily(1, 18)
			->withoutOverlapping();

		$schedule->command('checkPayments')
			->everyMinute()
			->withoutOverlapping();

//		$schedule->command('getWeeklyFromFile')
//			->cron('*/2 * * * * *')
//			->weekdays()
//			->withoutOverlapping();
//
//		$schedule->command('getMonthlyFromFile')
//			->cron('*/2 * * * * *')
//			->weekdays()
//			->withoutOverlapping();
//
//		$schedule->command('getUsaFromFile')
//			->cron('*/2 * * * * *')
//			->weekdays()
//			->withoutOverlapping();
//
//		$schedule->command('getWednesdayFromFile')
//			->cron('*/2 * * * * *')
//			->weekdays()
//			->withoutOverlapping();
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
