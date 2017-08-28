<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Services\ChangeEmailService;
use Illuminate\Http\Request;
use JWTAuth;

class ChangeEmailController extends Controller
{
    protected $changeEmailService;

    public function __construct(ChangeEmailService $changeEmailService)
    {
        $this->middleware('jwt.auth', ['except' => ['change']]);
        $this->changeEmailService = $changeEmailService;
    }

    public function rules()
    {
        return [
            'token' => 'required|exists:user_change_emails'
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Token is required.',
            'token.exists' => 'No token in DB.'
        ];
    }

    public function change($token)
    {
        if (!empty($token) && $this->changeEmailService->getByToken($token)) {
            $user = $this->changeEmailService->change($token);

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Bad activation token.',
            'data' => null
        ], 422);
    }

    public function cancel($new_email)
    {
        $jwt_user = JWTAuth::toUser(JWTAuth::getToken());
        $user = $jwt_user
            ->where('id', $jwt_user['id'])
            ->first();

        if ($user && $new_email && ($record = $this->changeEmailService->get($user, $new_email))) {
            $this->changeEmailService->delete($record->token);

            return response()->json([
                'status' => true,
                'message' => null,
                'data' => null
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Can not find any information in Database by this credentials.',
            'data' => null
        ], 422);
    }
}
