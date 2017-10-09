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
            'payment_system' => 'required|in:' . Dictionary::PAYMENT_SYSTEM_YANDEX_MONEY . ',' . Dictionary::PAYMENT_SYSTEM_WEB_MONEY . ',' . Dictionary::PAYMENT_SYSTEM_DEMO,
            'trade_account' => 'required|numeric',
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
                        'subscribe_date_until' => date('Y-m-d H:i:s', strtotime('+' . $product->demo_access_days . ' DAYS'))
                    ]
                );

                $mail = new SuccessPayForProduct($product, true);
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

                switch ($payment->payment_system) {
                    case Dictionary::PAYMENT_SYSTEM_WEB_MONEY:
                        if ($payment->currency === Dictionary::CURRENCY_RUB) {
                            $amount = $product->price;
                        } else {
                            $rate = Rate::where([
                                ['date', date('Y-m-d')],
                                ['name', strtoupper($payment->currency) . Dictionary::CURRENCY_RUB]
                            ])->first();

                            $amount = $product->price * $rate->rate;
                        }

                        $gateway = Omnipay::create('\Omnipay\WebMoney\Gateway');
                        $gateway->setMerchantPurse('Z229902436381');

                        $response = $gateway->purchase([
                            'amount' => number_format($amount, 2),
                            'transactionId' => $payment->id,
                            'currency' => $payment->currency,
                            'testMode' => true,
                            'description' => $product->description,
                            'returnUrl' => 'http://cmeinfo.vlevels.ru/success',
                            'cancelUrl' => 'http://cmeinfo.vlevels.ru/payment',
                            'notifyUrl' => 'http://api.vlevels.ru/merchant/webmoney.php'
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

                            $amount = $product->price * $rate->rate;
                        }

                        $gateway = Omnipay::create('\yandexmoney\YandexMoney\GatewayIndividual');
                        $gateway->setAccount('4100324863876');
                        $gateway->setLabel($product->name);
                        $gateway->setOrderId($payment->id);
//                        $gateway->setMethod('PC');
//                        $gateway->setReturnUrl('http://cmeinfo.vlevels.ru/success');
//                        $gateway->setCancelUrl('http://cmeinfo.vlevels.ru/payment');
                        $gateway->setParameter('targets', $product->name);
                        $gateway->setParameter('comment', 'test');

                        $response = $gateway->purchase(['amount' => $amount, 'currency' => Dictionary::CURRENCY_RUB, 'testMode' => true, 'FormComment' => $product->description])->send();

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