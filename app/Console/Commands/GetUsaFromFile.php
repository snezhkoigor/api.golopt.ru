<?php

namespace App\Console\Commands;

use App\CallsPuts;
use App\Strikes;
use Illuminate\Console\Command;

class GetUsaFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getUsaFromFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get usa options from ftp files';

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
    	$file_name = file_get_contents('http://goloption.ru/Files/CME_Reports2/dataA.txt');

    	if ($file_name)
	    {
	    	$data_info_array = explode('_', $file_name);
	    	
	    	if (count($data_info_array) === 4)
		    {
		    	$parse_date = explode('.', $data_info_array[2])[2] . '-' . explode('.', $data_info_array[2])[1] . '-' . explode('.', $data_info_array[2])[0] . ' ' . str_replace(['-', '.csv'], [':', ''], $data_info_array[3]);
		    	$data = file_get_contents('http://goloption.ru/Files/CME_Reports2/amer.csv');
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
						    	$result[$pair][$expire][$row_array[0]]['expw'] = $expire;
						    	$result[$pair][$expire][$row_array[0]]['parse_date'] = $parse_date;
						    	$result[$pair][$expire][$row_array[0]]['type'] = Strikes::TYPES_AMER;
						    	$result[$pair][$expire][$row_array[0]]['strike'] = $row_array[0];
						    	$result[$pair][$expire][$row_array[0]]['fp'] = 0;
						    	$result[$pair][$expire][$row_array[0]]['odr'] = 0;

						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['type'] = $prefix;
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['open_interest'] = (int)$row_array[1];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['volume'] = (int)$row_array[2];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['premia'] = (int)$row_array[3];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['spros_1'] = (int)$row_array[4];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['spros_2'] = (int)$row_array[5];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['predlojenie_1'] = (int)$row_array[6];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['predlojenie_2'] = (int)$row_array[7];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['prirost_tekushiy'] = (int)$row_array[8];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['prirost_predydushiy'] = (int)$row_array[9];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['money_obshiy'] = (int)$row_array[10];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['money_tekushiy'] = (int)$row_array[11];
						    	$result[$pair][$expire][$row_array[0]]['calls_puts'][$prefix]['balance_of_day'] = $prefix === CallsPuts::TYPES_CALL ? (float)$row_array[0]*0.001 + (float)$row_array[3] : (float)$row_array[0]*0.001 - (float)$row_array[3];
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
					        	$strike['type'] .= $week_number;

					        	$strike_obj = Strikes::updateOrCreate([
					        		'symbol' => $strike['symbol'],
							        'expw' => $strike['expw'],
							        'type' => $strike['type'],
							        'strike' => $strike['strike']
						        ], $strike);

					        	if ($strike_obj)
						        {
						        	foreach ($strike['calls_puts'] as $option_type => $item)
							        {
							            CallsPuts::updateOrCreate([
							                'strike_id' => $strike_obj->id,
									        'type' => $option_type
								        ], $item);
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
