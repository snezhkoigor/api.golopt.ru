<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 03.08.17
 * Time: 16:31
 */

namespace App\Http\Controllers\Api\V1\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => true,
            'message' => null,
            'data' => null
        ]);
    }
}