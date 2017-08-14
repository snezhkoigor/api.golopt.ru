<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 13.08.17
 * Time: 12:28
 */

namespace App\Http\Controllers\Api\V1\Payment;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayController extends Controller
{
    public function pay(Request $request)
    {
        $result = null;

        if (!empty($request->paymentSystem) && !empty($request->invoiceId)) {
            $invoice = DB::connection('oldMysql')
                ->table('payment')
                ->where('_invoce', '=', $request->invoiceId)
                ->first();

            if (!empty($request->name)) {
                DB::connection('oldMysql')
                    ->table('users')
                    ->where('id', '=', $invoice->_id_user)
                    ->update([
                        'name' => $request->name
                    ]);
            }

            switch ($request->paymentSystem) {
                case 'WM':
                    DB::connection('oldMysql')
                        ->table('payment')
                        ->where('_invoce', '=', $request->invoiceId)
                        ->update([
                            '_payment_type' => 'Web Money',
                            '_fee' => 0,
                            '_amount' => 1
                        ]);

                    $gateway = Omnipay::create('\Omnipay\WebMoney\Gateway');
                    $gateway->setMerchantPurse('Z229902436381');

                    $response = $gateway->purchase([
                        'amount' => '1.00',
                        'transactionId' => $request->invoiceId,
                        'currency' => 'USD',
                        'testMode' => false,
                        'description' => $request->formComment,
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
                case 'YM':
                    DB::connection('oldMysql')
                        ->table('payment')
                        ->where('_invoce', '=', $request->invoiceId)
                        ->update([
                            '_payment_type' => 'Yandex.Money',
                            '_fee' => round(($invoice->_amount*0.5)/100, 2)
                        ]);

                    $gateway = Omnipay::create('\yandexmoney\YandexMoney\GatewayIndividual');
                    $gateway->setAccount('41001310031527');
                    $gateway->setLabel($invoice->_comment);
                    $gateway->setPassword('CH+/mBSKzhlKvoX8uKG56att');
                    $gateway->setOrderId($request->invoiceId);
                    $gateway->setMethod('PC');
                    $gateway->setReturnUrl('http://cmeinfo.vlevels.ru/success');
                    $gateway->setCancelUrl('http://cmeinfo.vlevels.ru/payment');

                    $response = $gateway->purchase(['amount' => $invoice->_amount, 'currency' => 'RUB', 'testMode' => false, 'FormComment' => $request->formComment])->send();

                    $result = [
                        'actionUrl' => $response->getEndpoint(),
                        'method' => $response->getRedirectMethod(),
                        'params' => $response->getRedirectData()
                    ];

                    break;

                case 'MC':
                case 'VISA':
                    DB::connection('oldMysql')
                        ->table('payment')
                        ->where('_invoce', '=', $request->invoiceId)
                        ->update([
                            '_payment_type' => 'Yandex.Money',
                            '_fee' => round(($invoice->_amount*0.5)/100, 2)
                        ]);

                    $gateway = Omnipay::create('\yandexmoney\YandexMoney\GatewayIndividual');
                    $gateway->setAccount('41001310031527');
                    $gateway->setLabel($invoice->_comment);
                    $gateway->setPassword('CH+/mBSKzhlKvoX8uKG56att');
                    $gateway->setOrderId($request->invoiceId);
                    $gateway->setMethod('AC');
                    $gateway->setReturnUrl('http://cmeinfo.vlevels.ru/success');
                    $gateway->setCancelUrl('http://cmeinfo.vlevels.ru/payment');

                    $response = $gateway->purchase(['amount' => $invoice->_amount, 'currency' => 'RUB', 'testMode' => false, 'FormComment' => $request->formComment])->send();

                    $result = [
                        'actionUrl' => $response->getEndpoint(),
                        'method' => $response->getRedirectMethod(),
                        'params' => $response->getRedirectData()
                    ];

                    break;
            }
        }

        return $result;
    }
}