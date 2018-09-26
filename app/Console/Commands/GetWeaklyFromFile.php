<?php

namespace App\Console\Commands;

use App\Exceptions\SystemErrorException;
use App\FuturesPrice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use CSV;

class GetWeaklyFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getWeaklyFromFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get weakly options from ftp files';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
    	$file_name = file_get_contents('http://goloption.ru/Files/CME_Reports2/dataW.txt');

    	if ($file_name)
	    {
	    	$data_info_array = explode('_', $file_name);
	    	
	    	if (count($data_info_array) === 4)
		    {
		    	$csv = new \Monokakure\CSV\CSV([]);
		    	$date = $data_info_array[2] . ' ' . str_replace('-', ':', $data_info_array[3]);
//		    	$data = file_get_contents('http://goloption.ru/Files/CME_Reports2/week.csv');
			    $data =  $csv->render('http://goloption.ru/Files/CME_Reports2/week.csv');
		    }
	    }

    	var_dump($data);die;
    }
}
