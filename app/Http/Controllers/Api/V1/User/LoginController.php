<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;

        try {
	        $credentials['email'] = strtolower($credentials['email']);
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password',
                    'data' => null
                ], 422);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create token',
                'data' => null
            ], 500);
        }

        $user = JWTAuth::toUser($token);

        if ($user->active === 1) {
            return response()->json([
                'status' => true,
                'message' => null,
                'data' => compact('token')
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Activate your e-mail, please',
            'data' => null
        ], 422);
    }
}
