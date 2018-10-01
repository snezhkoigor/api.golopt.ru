<?php

namespace App\Http\Controllers\Api\V1\Strike;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StrikeController extends Controller
{
    public function getBySymbol($symbol, $type)
    {
    	$data = DB::table('strikes')
		    ->select(['strike', 'fp', 'odr', 'calls_puts.type', 'calls_puts.open_interest', 'calls_puts.volume',
	              'calls_puts.premia', 'calls_puts.spros_1', 'calls_puts.spros_2', 'calls_puts.predlojenie_1',
                  'calls_puts.predlojenie_2', 'calls_puts.prirost_tekushiy', 'calls_puts.prirost_predydushiy',
                  'calls_puts.money_obshiy', 'calls_puts.money_tekushiy', 'calls_puts.balance_of_day',
                  'calls_puts.is_balance'])
		    ->join('calls_puts', 'calls_puts.strike_id', '=', 'strikes.id')
		    ->where([
		    	['symbol', strtoupper($symbol)],
			    ['type', $type]
		    ])
		    ->get()
	        ->toArray();

    	return response()->json($data);
    }
}
