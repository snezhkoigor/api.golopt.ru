<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 13.08.17
 * Time: 12:28
 */

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReceiveController extends Controller
{
    public function index()
    {
        if ($_POST) {
            DB::table('payment_answer_queue')->insert(
                [
                    'post' => json_encode($_POST),
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }

        response('OK');
    }
}