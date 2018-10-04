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
	public static $need_to_parse = [
		'USD'
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
    	$data = file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js');
    	$json = json_decode($data);

    	foreach (self::$need_to_parse as $code)
	    {
		    if (property_exists($json->Valute, $code) && property_exists($json->Valute->{$code}, 'Value'))
		    {
				$info = Rate::where([
				    ['date', date('Y-m-d')],
				    ['name', $code . Dictionary::CURRENCY_RUB]
			    ])->first();

			    if ($info) {
				    $rate = $info;
			    } else {
				    $rate = new Rate();
			    }

			    $rate->name = $code . Dictionary::CURRENCY_RUB;
			    $rate->rate = (float)$json->Valute->{$code}->Value;
			    $rate->date = date('Y-m-d');
			    $rate->save();
		    }
		    else
	        {
		    	$info = Rate::where(
				    [
					    ['date', date('Y-m-d')],
					    ['name', $code . Dictionary::CURRENCY_RUB]
				    ]
			    )
				    ->first();

			    if (!$info)
			    {
				    $ratePrev = Rate::where(
					    [
						    ['name', $code . Dictionary::CURRENCY_RUB]
					    ])
					    ->orderBy('date', 'desc')
					    ->first();

				    if ($ratePrev)
				    {
					    $rate = new Rate();

					    $rate->name = $code . Dictionary::CURRENCY_RUB;
					    $rate->rate = (float) $ratePrev->rate;
					    $rate->date = date('Y-m-d');
					    $rate->save();
				    }
			    }
		    }
	    }

        return true;
    }
}
