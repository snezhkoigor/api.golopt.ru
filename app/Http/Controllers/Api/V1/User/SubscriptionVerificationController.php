<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
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
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $product = DB::table('product_user')
                ->join('users', 'product_user.user_id', '=', 'users.id')
                ->select('product_user.active', 'product_user.subscribe_date_until')
                ->where([
                    ['product_user.product_id', '=', $request->get('product')],
                    ['users.trade_account', '=', $request->get('account')]
                ])
                ->first();

            if ($product) {
                $product->subscribe_date_until = strtotime($product->subscribe_date_until);

                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => $product
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'This user has not this product.',
                'data' => null
            ], 422);
        }

        return response()->json([
            'status' => false,
            'message' => $validator->errors()->getMessages(),
            'data' => null
        ], 422);
    }
}