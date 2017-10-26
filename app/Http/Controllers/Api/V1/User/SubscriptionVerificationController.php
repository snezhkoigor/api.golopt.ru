<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionVerificationController extends Controller
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
        if ((int)$request->get('product') !== 8) {
            $validator = Validator::make($request->all(), $this->rules(), $this->messages());

            if ($validator->fails() === false) {
                $product = DB::table('product_user')
                    ->select('active', 'subscribe_date_until')
                    ->where([
                        ['product_id', '=', $request->get('product')],
                        ['trade_account', '=', $request->get('account')]
                    ])
                    ->first();

                if ($product) {
                    $product->subscribe_date_until = strtotime($product->subscribe_date_until);

                    return response()->json([
                        'status' => 1,
                        'message' => null,
                        'data' => $product
                    ]);
                }

                return response()->json([
                    'status' => 0,
                    'message' => 'This user has not this product. (ERR - 1)',
                    'data' => null
                ], 422);
            }
        } else if ((int)$request->get('product') === 8 && in_array($request->get('account'), User::getDevAccounts(), false)) {
            return response()->json([
                'status' => 1,
                'message' => null,
                'data' => [
                    'active' => 1,
                    'subscribe_date_until' => strtotime('+1 YEAR')
                ]
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'This user has not this product. (ERR - 2)',
            'data' => null
        ], 422);
    }
}