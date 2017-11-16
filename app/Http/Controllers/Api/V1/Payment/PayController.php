<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 13.08.17
 * Time: 12:28
 */

namespace App\Http\Controllers\Api\V1\Payment;


use App\Dictionary;
use App\Http\Controllers\Controller;
use App\Mail\SuccessPayForProduct;
use App\Payment;
use App\Product;
use App\Rate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Omnipay\Omnipay;
use JWTAuth;

class PayController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function rules()
    {
        return [
            'payment_system' => 'required|in:' . Dictionary::PAYMENT_SYSTEM_YANDEX_MONEY . ',' . Dictionary::PAYMENT_SYSTEM_WEB_MONEY . ',' . Dictionary::PAYMENT_SYSTEM_QIWI . ',' . Dictionary::PAYMENT_SYSTEM_DEMO,
            'trade_account' => 'required',
            'broker' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'payment_system.required' => 'No payment system selected',
            'payment_system.in' => 'Wrong payment system selected',
            'trade_account.required' => 'No account number selected',
//            'trade_account.numeric' => 'Account must contain only digits',
            'broker.required' => 'No broker name',
        ];
    }

    public function demo(Request $request, $id)
    {
        $user = JWTAuth::toUser(JWTAuth::getToken());
        $product = Product::where([
            [ 'has_demo', 1 ],
            [ 'id', $id ]
        ])->first();

        $request->request->add([ 'payment_system' => Dictionary::PAYMENT_SYSTEM_DEMO ]);
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());
        if (!$user->products()->where([ [ 'type', Dictionary::PRODUCT_TYPE_DEMO ], [ 'product_id', $product->id ], [ 'user_id', $user['id'] ]])->first()) {
            if ($validator->fails() === false) {
                $current_demo = $user->products()->where([ [ 'type', Dictionary::PRODUCT_TYPE_DEMO ], [ 'user_id', $user['id'] ]])->first();
                if ($current_demo && date('Y-m-d') > $current_demo->pivot->subscribe_date_until) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Testing period is over',
                        'data' => null
                    ], 422);
                }

                $subscribe_date_until = date('Y-m-d H:i:s', strtotime('+' . $product->demo_access_days . ' DAYS'));

                if ($current_demo) {
                    $subscribe_date_until = $current_demo->pivot->subscribe_date_until;

                    $user->products()->where([['type', Dictionary::PRODUCT_TYPE_DEMO], ['user_id', $user['id']]])->detach();
                }

                $user->products()->attach(
                    $user['id'],
                    [
                        'product_id' => $product->id,
                        'trade_account' => $request->get('trade_account'),
                        'broker' => $request->get('broker'),
                        'type' => Dictionary::PRODUCT_TYPE_DEMO,
                        'active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'subscribe_date_until' => $subscribe_date_until
                    ]
                );

	            $payment = new Payment();
	            $payment->user_id = $user['id'];
	            $payment->product_id = $product->id;
	            $payment->payment_system = $request->get('payment_system');
	            $payment->amount = 0;
	            $payment->currency = Dictionary::CURRENCY_USD;
	            $payment->success = 1;
	            $payment->details = json_encode([
		            'trade_account' => $request->get('trade_account'),
		            'broker' => $request->get('broker')
	            ]);
	            $payment->save();

                $mail = new SuccessPayForProduct($product, $user['country'], true);
                Mail::to($user->email)->send($mail);

                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => null
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
            'message' => 'You have demo access already',
            'data' => null
        ], 422);
    }

    public function pay(Request $request, $id)
    {
        $result = null;

        if ($product = Product::find($id)) {
            $user = JWTAuth::toUser(JWTAuth::getToken());
            $validator = Validator::make($request->all(), $this->rules(), $this->messages());

            if ($validator->fails() === false) {
                $payment = new Payment();
                    $payment->user_id = $user['id'];
                    $payment->product_id = $product->id;
                    $payment->payment_system = $request->get('payment_system');
                    $payment->amount = $product->price;
                    $payment->currency = $product->currency;
                    $payment->success = 0;
                    $payment->details = json_encode([
                        'trade_account' => $request->get('trade_account'),
                        'broker' => $request->get('broker')
                    ]);
                    $payment->updated_at = null;
                $payment->save();

                $user_language = User::getLanguage($user['country']);

                switch ($payment->payment_system) {
	                case Dictionary::PAYMENT_SYSTEM_QIWI:
//	                	$gateway = Omnipay::create('\Omnipay\Qiwi\Gateway');
//
//	                	$gateway->setProviderId(config('payments.QIWI_ACCOUNT'));
//
//		                $response = $gateway->purchase([
//			                'amount' => number_format($product->price, 2, '.', ''),
//			                'txn_id' => $payment->id,
//			                'to' => '+79213887398',
//			                'currency' => Dictionary::CURRENCY_RUB,
//			                'description' => $product->description,
//			                'returnUrl' => 'http://goloption.com/' . $user_language . '/pay/success',
//			                'cancelUrl' => 'http://goloption.com/' . $user_language . '/pay/fail',
//			                'notifyUrl' => 'http://api.goloption.com/api/pay/receive'
//		                ])->send();
//
//		                return response()->json($response);die;
//		                $result = [
//			                'actionUrl' => $response->getRedirectUrl(),
//			                'method' => $response->getRedirectMethod(),
//			                'params' => $response->getRedirectData()
//		                ];

		                $result = [
		                	'actionUrl' => 'https://bill.qiwi.com/order/external/create.action',
			                'method' => 'GET',
			                'params' => [
			                	'from' => config('payments.QIWI_ACCOUNT'),
				                'summ' => number_format($product->price, 2, '.', ''),
				                'currency' => $payment->currency,
				                'to' => $user->calling_code . $user->phone,
				                'comm' => $product->description,
				                'txn_id' => $payment->id,
				                'successUrl' => 'http://goloption.com/' . $user_language . '/pay/success',
                                'cancelUrl' => 'http://goloption.com/' . $user_language . '/pay/fail',
                                'notifyUrl' => 'http://api.goloption.com/api/pay/receive'
			                ]
		                ];

	                	break;

                    case Dictionary::PAYMENT_SYSTEM_WEB_MONEY:
                        $gateway = Omnipay::create('\Omnipay\WebMoney\Gateway');

	                    if ($payment->currency === Dictionary::CURRENCY_RUB) {
		                    $amount = $product->price;
	                    } else {
		                    $rate = Rate::where([
			                    ['date', date('Y-m-d')],
			                    ['name', strtoupper($payment->currency) . Dictionary::CURRENCY_RUB]
		                    ])->first();

		                    if (!$rate) {
			                    return response()->json([
				                    'status' => false,
				                    'message' => 'No exchange rate for today',
				                    'data' => null
			                    ], 422);
		                    }

		                    $amount = $product->price * $rate->rate; // это в рублях
	                    }

//                        $gateway->setMerchantPurse($payment->currency === Dictionary::CURRENCY_RUB ? config('payments.WEBMONEY_RUB') : config('payments.WEBMONEY_USD'));
	                    $gateway->setMerchantPurse(config('payments.WEBMONEY_RUB'));
                        $response = $gateway->purchase([
//                            'amount' => number_format($product->price, 2, '.', ''),
                            'amount' => number_format($amount, 2, '.', ''),
                            'transactionId' => $payment->id,
//                            'currency' => $payment->currency,
                            'currency' => Dictionary::CURRENCY_RUB,
                            'testMode' => false,
                            'description' => $product->description,
                            'returnUrl' => 'http://goloption.com/' . $user_language . '/pay/success',
                            'cancelUrl' => 'http://goloption.com/' . $user_language . '/pay/fail',
                            'notifyUrl' => 'http://api.goloption.com/api/pay/receive'
                        ])->send();

                        $result = [
                            'actionUrl' => $response->getRedirectUrl(),
                            'method' => $response->getRedirectMethod(),
                            'params' => $response->getRedirectData()
                        ];

                        break;
                    case Dictionary::PAYMENT_SYSTEM_YANDEX_MONEY:
                        if ($payment->currency === Dictionary::CURRENCY_RUB) {
                            $amount = $product->price;
                        } else {
                            $rate = Rate::where([
                                ['date', date('Y-m-d')],
                                ['name', strtoupper($payment->currency) . Dictionary::CURRENCY_RUB]
                            ])->first();

                            if (!$rate) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'No exchange rate for today',
                                    'data' => null
                                ], 422);
                            }

                            $amount = $product->price * $rate->rate; // это в рублях
                        }

                        $gateway = Omnipay::create('\yandexmoney\YandexMoney\GatewayIndividual');
                        $gateway->setAccount(config('payments.YANDEX_MONEY_ACCOUNT'));
                        $gateway->setLabel($product->name);
                        $gateway->setOrderId($payment->id);
                        $gateway->setParameter('targets', $product->name);
                        $gateway->setParameter('comment', $product->description);
                        $gateway->setMethod('AC');
                        $gateway->setReturnUrl('http://goloption.com/' . $user_language . '/pay/success');
                        $gateway->setCancelUrl('http://goloption.com/' . $user_language . '/pay/fail');

                        $response = $gateway->purchase([
                        	'amount'=> $amount,
	                        'currency' => Dictionary::CURRENCY_RUB,
	                        'testMode' => false,
	                        'FormComment' => $product->description
                        ])->send();

                        $result = [
                            'actionUrl' => $response->getEndpoint(),
                            'method' => $response->getRedirectMethod(),
                            'params' => $response->getRedirectData()
                        ];

                        break;
                }

                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => $result
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
            'message' => 'No product selected for buying',
            'data' => null
        ], 422);
    }
}