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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Omnipay\Omnipay;
use JWTAuth;

class ReceiveController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            DB::table('payment_answer_queue')->insert(
                [
                    'post' => json_encode($request),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }

        response('OK');
    }
}