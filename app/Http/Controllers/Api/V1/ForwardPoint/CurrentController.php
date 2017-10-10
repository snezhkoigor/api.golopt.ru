<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\ForwardPoint;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CurrentController extends Controller
{
    public function rules()
    {
        return [
            'account' => 'required|exists:product_user,trade_account',
            'product' => 'required|exists:products,id'
        ];
    }

    public function messages()
    {
        return [
            'account.required' => 'Trade account is required',
            'account.exists' => 'No trade account in DB',
            'product.required' => 'Product is required',
            'product.exists' => 'No product in DB'
        ];
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $accountInfo = DB::table('product_user')
                ->where([
                    [ 'trade_account', '=', $request->get('account') ],
                    [ 'product_id', '=', $request->get('product') ],
                    [ 'active', '=', 1 ]
                ])
                ->first();

            if ($accountInfo) {
                $date = ($request->get('date') ? $request->get('date') : date('Y-m-d'));
                if (in_array(date('w'), [0, 6]) && !$request->get('date')) {
                    if (date('w') === 6) {
                        $date = date('Y-m-d', strtotime('-1 DAY'));
                    } else {
                        $date = date('Y-m-d', strtotime('-2 DAY'));
                    }
                }

                $fp = DB::table('forward_points')
                    ->select('forward_points.name', 'forward_points.fp', DB::raw('UNIX_TIMESTAMP(forward_points.updated_at) as updated_at'))
                    ->where('forward_points.date', '=', $date)
                    ->get();

                if ($fp) {
                    return response()->json([
                        'status' => 1,
                        'message' => null,
                        'data' => $fp
                    ]);
                }
            }

            return response()->json([
                'status' => 0,
                'message' => 'No forward points. (ERR - 1)',
                'data' => null
            ], 422);
        }

        return response()->json([
            'status' => 0,
            'message' => 'No forward points. (ERR - 2)',
            'data' => null
        ], 422);
    }
}