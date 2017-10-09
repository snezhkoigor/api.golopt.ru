<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class DownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index($id)
    {
        $user = JWTAuth::toUser(JWTAuth::getToken());

        if ($user) {
            $product = DB::table('product_user')
                ->select('products.path')
                ->join('products', 'products.id', '=', 'product_user.product_id')
                ->where([
                    [ 'product_user.product_id', '=', $id ],
                    [ 'product_user.user_id', '=', $user['id'] ]
                ])
                ->first();

            if ($product) {
                return response()->download('/var/www/sites-with-php5/api.goloption.com' . $product->path);
            }

            return response()->json([
                'status' => false,
                'message' => 'This user has not this product.',
                'data' => null
            ], 422);
        }

        return response()->json([
            'status' => false,
            'message' => 'No user.',
            'data' => null
        ], 422);
    }
}