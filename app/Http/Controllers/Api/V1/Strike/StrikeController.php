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
    	$answer = '';
    	$strikes = [];
    	$data = DB::table('strikes')
		    ->select(['strike', 'fp', 'odr', 'expire', 'parse_date', 'strikes.id', 'calls_puts.type', 'calls_puts.open_interest', 'calls_puts.volume',
	              'calls_puts.premia', 'calls_puts.spros_1', 'calls_puts.spros_2', 'calls_puts.predlojenie_1',
                  'calls_puts.predlojenie_2', 'calls_puts.prirost_tekushiy', 'calls_puts.prirost_predydushiy',
                  'calls_puts.money_obshiy', 'calls_puts.money_tekushiy', 'calls_puts.balance_of_day',
                  'calls_puts.is_balance'])
		    ->join('calls_puts', 'calls_puts.strike_id', '=', 'strikes.id')
		    ->where([
		    	['strikes.symbol', strtoupper($symbol)],
			    ['strikes.type', $type]
		    ])
		    ->get()
	        ->toArray();

    	if ($data)
	    {
	        foreach ($data as $strike)
	        {
	        	$strikes[$strike->strike]['id'] = $strike->id;
	        	$strikes[$strike->strike]['strike'] = $strike->strike;
	        	$strikes[$strike->strike]['fp'] = $strike->fp;
	        	$strikes[$strike->strike]['odr'] = $strike->odr;
	        	$strikes[$strike->strike]['expire'] = $strike->expire;
	        	$strikes[$strike->strike]['parse_date'] = $strike->parse_date;
	        	$strikes[$strike->strike]['open_interest_' . $strike->type] = $strike->open_interest;
	        	$strikes[$strike->strike]['volume_' . $strike->type] = $strike->volume;
	        	$strikes[$strike->strike]['premia_' . $strike->type] = $strike->premia;
	        	$strikes[$strike->strike]['spros_1_' . $strike->type] = $strike->spros_1;
	        	$strikes[$strike->strike]['spros_2_' . $strike->type] = $strike->spros_2;
	        	$strikes[$strike->strike]['predlojenie_1_' . $strike->type] = $strike->predlojenie_1;
	        	$strikes[$strike->strike]['predlojenie_2_' . $strike->type] = $strike->predlojenie_2;
	        	$strikes[$strike->strike]['prirost_tekushiy_' . $strike->type] = $strike->prirost_tekushiy;
	        	$strikes[$strike->strike]['prirost_predydushiy_' . $strike->type] = $strike->prirost_predydushiy;
	        	$strikes[$strike->strike]['money_obshiy_' . $strike->type] = $strike->money_obshiy;
	        	$strikes[$strike->strike]['money_tekushiy_' . $strike->type] = $strike->money_tekushiy;
	        	$strikes[$strike->strike]['balance_of_day_' . $strike->type] = $strike->balance_of_day;
	        	$strikes[$strike->strike]['is_balance_' . $strike->type] = $strike->is_balance;
	        }
	        
	        foreach ($strikes as $key => $value)
	        {
	            $answer .= implode(';', array_values($value)) . "\n";
	        }
	    }

    	echo $answer;
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
