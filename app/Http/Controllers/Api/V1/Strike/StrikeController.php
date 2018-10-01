<?php

namespace App\Http\Controllers\Api\V1\Strike;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StrikeController extends Controller
{
    public function getBySymbol($symbol, $type)
    {
    	$result = [];
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
	        	$result[$strike['strike']]['id'] = $strike['id'];
	        	$result[$strike['strike']]['fp'] = $strike['fp'];
	        	$result[$strike['strike']]['odr'] = $strike['odr'];
	        	$result[$strike['strike']]['expire'] = $strike['expire'];
	        	$result[$strike['strike']]['parse_date'] = $strike['parse_date'];
	        	$result[$strike['strike']]['open_interest_' . $strike['type']] = $strike['open_interest'];
	        	$result[$strike['strike']]['volume_' . $strike['type']] = $strike['volume'];
	        	$result[$strike['strike']]['premia_' . $strike['type']] = $strike['premia'];
	        	$result[$strike['strike']]['spros_1_' . $strike['type']] = $strike['spros_1'];
	        	$result[$strike['strike']]['spros_2_' . $strike['type']] = $strike['spros_2'];
	        	$result[$strike['strike']]['predlojenie_1_' . $strike['type']] = $strike['predlojenie_1'];
	        	$result[$strike['strike']]['predlojenie_2_' . $strike['type']] = $strike['predlojenie_2'];
	        	$result[$strike['strike']]['prirost_tekushiy_' . $strike['type']] = $strike['prirost_tekushiy'];
	        	$result[$strike['strike']]['prirost_predydushiy_' . $strike['type']] = $strike['prirost_predydushiy'];
	        	$result[$strike['strike']]['money_obshiy_' . $strike['type']] = $strike['money_obshiy'];
	        	$result[$strike['strike']]['money_tekushiy_' . $strike['type']] = $strike['money_tekushiy'];
	        	$result[$strike['strike']]['balance_of_day_' . $strike['type']] = $strike['balance_of_day'];
	        	$result[$strike['strike']]['is_balance_' . $strike['type']] = $strike['is_balance'];
	        }
	    }

    	return response()->json($result);
    }
}
