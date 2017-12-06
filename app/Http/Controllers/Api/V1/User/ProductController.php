<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 16.08.17
 * Time: 15:55
 */

namespace App\Http\Controllers\Api\V1\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function rules(Request $request)
    {
        return [
            'trade_account' => 'required|' . $this->accountRulesByChanging($request) . '|numeric',
            'broker' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'trade_account.required' => 'No account number selected.',
            'trade_account.numeric' => 'Account must contain only digits.',
            'broker.required' => 'No broker name selected.',
        ];
    }

	public function accountRulesByChanging($request)
	{
		$result = '';
		$user = JWTAuth::toUser($request->get('token'));

		if ($user && $request->get('trade_account')) {
			$trade_account = $request->get('email');
			$result = DB::table('product_user')->where([ ['user_id', '<>', $user['id'] ], [ 'trade_account', $trade_account ] ])->first() ? 'unique:product_user' : '';
		}

		return $result;
	}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());
        $user = JWTAuth::toUser($request->get('token'));

        if ($id && ($userProduct = $user->products->where('id', $id)->first())) {
            if ($validator->fails() === false) {
                $userProduct->pivot->trade_account = $request->get('trade_account');
                $userProduct->pivot->broker = $request->get('broker');
                $userProduct->pivot->save();

                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => $userProduct->pivot
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->getMessages(),
                'data' => null
            ], 422);
        }

        return response()->json([
            'status' => false,
            'message' => 'No product.',
            'data' => null
        ], 422);
    }
}