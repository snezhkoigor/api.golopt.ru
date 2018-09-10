<?php

namespace App\Console\Commands;

use App\Exceptions\SystemErrorException;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetFuturesFromCME extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getOptionExpirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get option expirations from CME website';

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
	$links = [
    		'AUD' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/37?pageSize=50',
	    	'CAD' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/48?pageSize=50',
	    	'CHF' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/86?pageSize=50',
	    	'EUR' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/58?pageSize=50',
	    	'GBP' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/42?pageSize=50',
	    	'JPY' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/69?pageSize=50',
	    	'MXN' => 'https://www.cmegroup.com/CmeWS/mvc/ProductCalendar/Options/75?pageSize=50'
	    ];

    	try
	    {
	        foreach ($links as $pair => $link)
		    {
		        $data = file_get_contents($link);
		        $json = json_decode($data, true);

		        foreach ($json as $item)
			{
				if ($item['label'] === 'Monthly Premium-Quoted Options')
				{
					foreach ($item['calendarEntries'] as $calendar)
					{
						if (!DB::table('cme_option_expire_calendar')->where([ ['contract_month' => $calendar['contractMonth']], ['pair' => $pair] ])->first())
						{
							DB::table('cme_option_expire_calendar')
								->insert([
									'pair' => $pair,
									'contract_month' => $calendar['contractMonth'],
									'settlement' => $calendar['settlement'],
									'created_at' => Carbon::now()->format('Y-m-d H:i:s')
								]);
						}
					}
				}
			}
		    }
	    }
	    catch (\Exception $e)
	    {
		    throw new SystemErrorException('Option expire calendar parse error', $e);
	    }
    }
}
