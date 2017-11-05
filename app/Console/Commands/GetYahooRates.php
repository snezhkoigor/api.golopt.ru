<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 21.07.17
 * Time: 22:05
 */

namespace App\Console\Commands;

use App\Dictionary;
use App\Rate;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class GetYahooRates extends Command
{
	public static $central_bank_rates_associations = [
		Dictionary::CURRENCY_USD => 'R01235'
	];
	public static $central_bank_rates_uri = 'http://www.cbr.ru/currency_base/dynamics.aspx?VAL_NM_RQ={currency}&date_req1={from}&date_req2={to}&rt=1&mode=1';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getYahooRates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get rates from Yahoo website';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    foreach (self::$central_bank_rates_associations as $major => $currency) {
		    $uri = str_replace(
			    ['{from}', '{to}', '{currency}'],
			    [date('Y-m-d'), date('Y-m-d'), $currency],
			    self::$central_bank_rates_uri
		    );
		    $content = @file_get_contents($uri);

		    $rates = [];
		    if (!empty($content)) {
			    preg_match('~<table.*?class=\"data\">.*?<tbody>(.*)<\/tbody>.*?</table>~is', $content, $matches);

			    if (!empty($matches[1])) {
				    preg_match_all('~<tr>(.*?)<\/tr>~is', $matches[1], $items);

				    if (!empty($items[1]) && count($items[1])) {
					    foreach ($items[1] as $item) {
						    preg_match_all('~<td>(.*?)<\/td>~is', $item, $calendar);

						    if (!empty($calendar[1]) && count($calendar[1]) && !empty($calendar[1][2])) {
							    $rates[$major] = (float) str_replace(',', '.', $calendar[1][2]);
						    }
					    }
				    }
			    }
		    }

		    if (!empty($rates))
		    {
			    foreach ($rates as $major_symbol => $exchange_rate)
			    {
				    if (!empty($exchange_rate))
				    {
					    $info = Rate::where([
						    ['date', date('Y-m-d')],
						    ['name', $major_symbol . Dictionary::CURRENCY_RUB]
					    ])->first();

					    if ($info) {
						    $rate = $info;
					    } else {
						    $rate = new Rate();
					    }

					    $rate->name = $major_symbol . Dictionary::CURRENCY_RUB;
					    $rate->rate = (float)$exchange_rate;
					    $rate->date = date('Y-m-d');
					    $rate->save();
				    }
			    }
		    } else {
			    $info = Rate::where([
				    ['date', date('Y-m-d')],
				    ['name', $major_symbol . Dictionary::CURRENCY_RUB]
			    ])->first();

			    if (!$info) {
				    $ratePrev = Rate::where([
					    ['name', $major_symbol . Dictionary::CURRENCY_RUB]
				    ])->orderBy('date', 'desc')->first();

				    if ($ratePrev)
				    {
					    $rate = new Rate();

					    $rate->name = $major_symbol . Dictionary::CURRENCY_RUB;
					    $rate->rate = (float) $ratePrev->rate;
					    $rate->date = date('Y-m-d');
					    $rate->save();

					    Log::warning('Установили предыдущий курс валют.');
				    } else {
					    Log::warning('Не смогли получить курсы валют.');
				    }
			    }
		    }
	    }

        return true;
    }
}