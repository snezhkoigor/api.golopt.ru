<?php

namespace App\Console\Commands;

use App\Exceptions\SystemErrorException;
use App\FuturesPrice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetFuturesFromCME extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getFuturesFromCME';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get futures price from CME website';

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
     *
     * @throws \Exception
     */
    public function handle()
    {
	$month = [
		// январь
		1 => 'FEB',
		// февраль
		2 => 'FEB',
		// март
		3 => 'MAY',
		// апрель
		4 => 'MAY',
		// май
		5 => 'MAY',
		// июнь
		6 => 'AUG',
		// июль
		7 => 'AUG',
		// август
		8 => 'AUG',
		// сентябрь
		9 => 'DEC',
		// октябрь
		10 => 'DEC'
		// ноябрь
		11 => 'DEC'
		// декабрь
		11 => 'MAR'
	    ];

    	$current = $month[(int)date('n')] . ' ' . date('Y');
	    
	$not_use_months = ['GOLD', 'CLI'];

    	$links = [
    		'AUD' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/37/G?pageSize=50',
	    	'CAD' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/48/G?pageSize=50',
	    	'CHF' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/86/G?pageSize=50',
	    	'EUR' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/58/G?pageSize=50',
	    	'GBP' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/42/G?pageSize=50',
	    	'JPY' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/69/G?pageSize=50',
	    	'MXN' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/75/G?pageSize=50',
		'GOLD' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/437/G?pageSize=50',
		'CLI' => 'https://www.cmegroup.com/CmeWS/mvc/Quotes/Future/425/G?pageSize=50'
	    ];

    	try
	    {
	        foreach ($links as $pair => $link)
		    {
		        $data = file_get_contents($link);
		        $json = json_decode($data, true);
		        
		        $price = 0;
			$exp_month = '';
		        foreach ($json['quotes'] as $item)
		        {
		        	if (!in_array($pair, $not_use_months) && $item['expirationMonth'] === $current)
			        {
					$exp_month = $item['expirationMonth'];
			        	$price = $item['last'];
			        	break;
			        }
				elseif (in_array($pair, $not_use_months) && $item['last'] !== '-' && (float)$item['last'])
			        {
					$exp_month = $item['expirationMonth'];
			        	$price = $item['last'];
					break;
			        }
		        }

		        $future = FuturesPrice::query()
			        ->where([
			            ['pair', $pair],
				        ['date', Carbon::today()->format('Y-m-d')]
			        ])
			        ->first();
	
		        if ($future === null)
		        {
		            $future = new FuturesPrice();
			        $future->price = $price;
			        $future->pair = $pair;
				$future->month = $exp_month;
			        $future->date = Carbon::today()->format('Y-m-d');
				$future->updated_at = Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s');
		        }
		        else
		        {
		            $future->price = $price;
				$future->month = $exp_month;
				$future->updated_at = Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s');
		        }

		        $future->save();
		    }
	    }
	    catch (\Exception $e)
	    {
		    throw new SystemErrorException('Future parse error', $e);
	    }
    }
}
