<?php

namespace App\Console\Commands;

use App\OptionParseDates;
use App\OptionStrikeCallsPuts;
use App\OptionStrikes;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetMonthlyFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getMonthlyFromFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get monthly options from ftp files';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
    	$file_name = file_get_contents('http://goloption.ru/Files/CME_Reports2/dataM.txt');

    	if ($file_name)
	    {
	    	$data_info_array = explode('_', $file_name);
	    	
	    	if (count($data_info_array) === 4)
		    {
		    	$parse_date = Carbon::createFromTimestamp(strtotime($data_info_array[2] . ' ' . str_replace(['-', '.csv'], [':', ''], $data_info_array[3])))
				    ->subHours(3)
				    ->format('Y-m-d H:i:s');

		    	$data = file_get_contents('http://goloption.ru/Files/CME_Reports2/mon.csv');
		    	$data_array = explode("\n", $data);

		    	$result = [];
		    	if (count($data_array) !== 0)
			    {
			    	$pair = '';
			    	$prefix = '';
			    	$expire = '';
			    	foreach ($data_array as $row)
				    {
				    	if ($row !== '')
					    {
					    	
					    	if (strpos($row, ';') === false)
						    {
						    	if (strpos($row, 'EXPM') !== false)
							    {
							    	$row_array = explode(' ', $row);
							    	$expire = date('Y-m-d', strtotime(trim($row_array[1])));
							    }
							    elseif (in_array(trim($row), ['CALL', 'PUTS']))
							    {
							    	$prefix = strtolower(trim($row));
							    }
							    else
							    {
							    	$pair = trim($row);
							    }
						    }
						    else
						    {
						    	$row_array = explode(';', $row);
							    $result[$pair][$expire][$row_array[0]]['symbol'] = $pair;
							    $result[$pair][$expire][$row_array[0]]['expire'] = $expire;
							    $result[$pair][$expire][$row_array[0]]['parse_date'] = $parse_date;
							    $result[$pair][$expire][$row_array[0]]['option_type'] = OptionStrikes::TYPES_MONTH;
							    $result[$pair][$expire][$row_array[0]]['strike'] = $row_array[0];
							    $result[$pair][$expire][$row_array[0]]['odr'] = 0;

							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['type'] = $prefix;
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['open_interest'] = (float) $row_array[1];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['volume'] = (float) $row_array[2];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['premia'] = (float) $row_array[3];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['spros_1'] = (float) $row_array[4];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['spros_2'] = (float) $row_array[5];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['predlojenie_1'] = (float) $row_array[6];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['predlojenie_2'] = (float) $row_array[7];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['prirost_tekushiy'] = (float) $row_array[8];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['prirost_predydushiy'] = (float) $row_array[9];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['money_obshiy'] = (float) $row_array[10];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['money_tekushiy'] = (float) $row_array[11];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['balance_of_day'] = $prefix === OptionStrikeCallsPuts::TYPES_CALL ? (float) $row_array[0] * 0.001 + (float) $row_array[3] : (float) $row_array[0] * 0.001 - (float) $row_array[3];
							    $result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['is_balance'] = false;
						    }
					    }
				    }
			    }

			    if (count($result))
			    {
				    foreach ($result as $symbol)
				    {
					    $week_number = 0;
					    foreach ($symbol as $expire_date)
					    {
						    $week_number++;
						    foreach ($expire_date as $strike)
						    {
						    	foreach ($strike['calls_puts'] as $option_type => $item)
							    {
							    	$insert = [
							            'parse_date' => $strike['parse_date'],
								        'symbol' => $strike['symbol'],
								        'expire' => $strike['expire'],
								        'option_type' => $strike['option_type'] . $week_number,
								        'strike' => $strike['strike'],
								        'type' => $item['type'],
								        'open_interest' => $item['open_interest'],
								        'volume' => $item['volume'],
								        'premia' => $item['premia'],
								        'spros_1' => $item['spros_1'],
								        'spros_2' => $item['spros_2'],
								        'predlojenie_1' => $item['predlojenie_1'],
								        'predlojenie_2' => $item['predlojenie_2'],
								        'prirost_tekushiy' => $item['prirost_tekushiy'],
								        'prirost_predydushiy' => $item['prirost_predydushiy'],
								        'money_obshiy' => $item['money_obshiy'],
								        'money_tekushiy' => $item['money_tekushiy'],
								        'balance_of_day' => $item['balance_of_day'],
								        'is_balance' => $item['is_balance'],
								    ];

							    	$exists = DB::table('option_parse')
									    ->where([
									    	['parse_date', $insert['parse_date']],
										    ['symbol', $insert['symbol']],
										    ['expire', $insert['expire']],
										    ['strike', $insert['strike']],
										    ['type', $insert['type']],
										    ['option_type', $insert['option_type']]
									    ])
									    ->first();

							    	if (!$exists)
								    {
								    	DB::table('option_parse')
										    ->insert($insert);
								    }
							    }
						    }
					    }
				    }
			    }
		    }
	    }
    }
}
