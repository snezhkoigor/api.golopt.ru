<?php

namespace App\Console\Commands;

use App\OptionParseDates;
use App\OptionStrikeCallsPuts;
use App\OptionStrikes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetWeeklyFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getWeeklyFromFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get weekly options from ftp files';

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
			    $parse_date = date(
				    'Y-m-d H:i:s',
				    strtotime($data_info_array[2] . ' ' . str_replace(['-', '.csv'], [':', ''], $data_info_array[3]))
			    );
			    $data = file_get_contents('http://goloption.ru/Files/CME_Reports2/week.csv');
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
							    if (strpos($row, 'EXPW') !== false)
							    {
								    $row_array = explode(' ', $row);
								    $expire = date('Y-m-d', strtotime(trim($row_array[2])));
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
							    $result[$pair][$expire][$row_array[0]]['parse_date_id'] = null;
							    $result[$pair][$expire][$row_array[0]]['type'] = OptionStrikes::TYPES_WEEK;
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
			
			    $parse_date_obj = OptionParseDates::query()
				    ->where('parse_date', $parse_date)
				    ->first();
			
			    if ($parse_date_obj === null)
			    {
			    	$parse_date_obj = new OptionParseDates();
				    $parse_date_obj->parse_date = $parse_date;
				    $parse_date_obj->save();

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
								    $strike['type'] .= $week_number;
								    $strike['parse_date_id'] = $parse_date_obj->id;
								    $strike_obj = OptionStrikes::updateOrCreate(
									    [
										    'symbol' => $strike['symbol'],
										    'expire' => $strike['expire'],
										    'type' => $strike['type'],
										    'strike' => $strike['strike'],
										    'parse_date_id' => $strike['parse_date_id']
									    ],
									    $strike
								    );
								
								    if ($strike_obj)
								    {
									    foreach ($strike['calls_puts'] as $option_type => $item)
									    {
										    OptionStrikeCallsPuts::updateOrCreate(
											    [
												    'strike_id' => $strike_obj->id,
												    'type'      => $option_type
											    ],
											    $item
										    );
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
}
