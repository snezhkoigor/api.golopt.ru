<?php

namespace App\Http\Controllers\Api\V1\Strike;

use App\Http\Controllers\Controller;
use App\Strikes;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\NotFoundException;

class StrikeController extends Controller
{
    public function getBySymbol($symbol, $type)
    {
    	$fields = !empty($_GET['fields']) ? explode(',', $_GET['fields']) : Strikes::getDefaultFields();
    	$result = [];
    	$strikes = [];
    	$query = DB::table('strikes')
		    ->select(['strike', 'fp', 'odr', 'expire', 'parse_date', 'strikes.id', 'calls_puts.type', 'calls_puts.open_interest', 'calls_puts.volume',
	              'calls_puts.premia', 'calls_puts.spros_1', 'calls_puts.spros_2', 'calls_puts.predlojenie_1',
                  'calls_puts.predlojenie_2', 'calls_puts.prirost_tekushiy', 'calls_puts.prirost_predydushiy',
                  'calls_puts.money_obshiy', 'calls_puts.money_tekushiy', 'calls_puts.balance_of_day',
                  'calls_puts.is_balance'])
		    ->join('calls_puts', 'calls_puts.strike_id', '=', 'strikes.id')
		    ->where([
		    	['strikes.symbol', strtoupper($symbol)],
			    ['strikes.type', $type]
		    ]);

    	if (!empty($_GET['parse_date_from']) && !empty($_GET['parse_date_to']))
	    {
	    	$query->whereBetween('parse_date', [date('Y-m-d H:i:s', strtotime($_GET['parse_date_from'])), date('Y-m-d H:i:s', strtotime($_GET['parse_date_to']))]);
	    }
//	    else
//        {
//
//	    }

    	$data = $query
		    ->get()
	        ->toArray();

    	if ($data)
	    {
	        foreach ($data as $strike)
	        {
	        	$strikes[$strike->strike]['parse_date'] = $strike->parse_date;
	        	$strikes[$strike->strike]['id'] = $strike->id;
	        	$strikes[$strike->strike]['strike'] = $strike->strike;
	        	$strikes[$strike->strike]['fp'] = $strike->fp;
				
	        	if (in_array('odr', $fields))
		        {
		        	$strikes[$strike->strike]['odr'] = $strike->odr;
		        }
		        if (in_array('expire', $fields))
		        {
		        	$strikes[$strike->strike]['expire'] = $strike->expire;
		        }
		        if (in_array('open_interest_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['open_interest_' . $strike->type] = $strike->open_interest;
		        }
		        if (in_array('volume_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['volume_' . $strike->type] = $strike->volume;
		        }
		        if (in_array('premia_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['premia_' . $strike->type] = $strike->premia;
		        }
		        if (in_array('spros_1_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['spros_1_' . $strike->type] = $strike->spros_1;
		        }
		        if (in_array('spros_2_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['spros_2_' . $strike->type] = $strike->spros_2;
		        }
		        if (in_array('predlojenie_1_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['predlojenie_1_' . $strike->type] = $strike->predlojenie_1;
		        }
		        if (in_array('predlojenie_2_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['predlojenie_2_' . $strike->type] = $strike->predlojenie_2;
		        }
		        if (in_array('prirost_tekushiy_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['prirost_tekushiy_' . $strike->type] = $strike->prirost_tekushiy;
		        }
		        if (in_array('prirost_predydushiy_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['prirost_predydushiy_' . $strike->type] = $strike->prirost_predydushiy;
		        }
		        if (in_array('money_obshiy_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['money_obshiy_' . $strike->type] = $strike->money_obshiy;
		        }
		        if (in_array('money_tekushiy_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['money_tekushiy_' . $strike->type] = $strike->money_tekushiy;
		        }
		        if (in_array('balance_of_day_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['balance_of_day_' . $strike->type] = $strike->balance_of_day;
		        }
		        if (in_array('is_balance_' . $strike->type, $fields))
		        {
		        	$strikes[$strike->strike]['is_balance_' . $strike->type] = $strike->is_balance;
		        }
	        }

	        foreach ($strikes as $key => $value)
	        {
	        	$result[] = array_values($value);
	        }
	    }

	    return response()->csv($result);
    }

    public function saveFpAndOdrFromIndicator($strike_id, $fp = null, $odr = null)
    {
    	$strike = Strikes::query('id', $strike_id)->first();

    	if ($strike === null)
	    {
	    	throw new NotFoundException('Нет страйка');
	    }

	    if ($fp)
	    {
	        $strike->fp = $fp;
	    }
	    if ($odr)
	    {
	        $strike->odr = $odr;
	    }

    	$strike->save();

    	return response()->json([
            'status' => true,
            'message' => 'Данные изменены',
            'data' => null
        ], 200);
    }
}
