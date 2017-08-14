<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:05
 */

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function get(Request $request)
    {
        $user = JWTAuth::toUser($request->token);

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $user->role->pluck('slug')
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No user.',
                'data' => null
            ]);
        }
    }
}