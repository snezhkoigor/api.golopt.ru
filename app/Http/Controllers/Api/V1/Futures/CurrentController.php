<?php

namespace App\Http\Controllers\Api\V1\Futures;

use App\FuturesPrice;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CurrentController extends Controller
{
    public function rules()
    {
        return [
            'account' => 'required|exists:product_user,trade_account'
        ];
    }

    public function messages()
    {
        return [
            'account.required' => 'Trade account is required',
            'account.exists' => 'No trade account in DB'
        ];
    }

	public function get(Request $request, $account, $pair)
	{
		$date = ($request->get('date') ? $request->get('date') : date('Y-m-d'));
		if (in_array(date('w'), [0, 6]) && !$request->get('date')) {
			if (date('w') === 6) {
				$date = date('Y-m-d', strtotime('-1 DAY'));
			} else {
				$date = date('Y-m-d', strtotime('-2 DAY'));
			}
		}

		if (in_array($account, User::getDevAccounts())) {
			$future = FuturesPrice::query()
				->where([
					['pair', $pair],
			        ['date', $date]
				])
				->first();

			if ($future) {
				echo $future->price;
				die;
			}
		}

		$validator = Validator::make([ 'account' => $account ], $this->rules(), $this->messages());

		if ($validator->fails() === false) {
			$accountInfo = DB::table('product_user')
				->where([
					[ 'trade_account', '=', $account ],
					[ 'active', '=', 1 ]
				])
				->first();

			if ($accountInfo) {
				$future = FuturesPrice::query()
					->where([
						['pair', $pair],
				        ['date', $date]
					])
					->first();
	
				if ($future) {
					echo $future->price;
					die;
				}
			}

			echo 0;
			die;
		}

		echo 0;
		die;
	}
}