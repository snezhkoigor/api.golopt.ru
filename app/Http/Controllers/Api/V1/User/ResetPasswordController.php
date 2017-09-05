<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Helpers\GetCountryFromIP;
use App\Http\Controllers\Controller;
use App\Mail\EmailRegister;
use App\Mail\ResetPassword;
use App\Services\ActivationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Countries;
use JWTAuth;

class ResetPasswordController extends Controller
{
    public function rules()
    {
        return [
            'email' => 'required|exists:users|email'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Enter your e-mail address',
            'email.exists' => 'Can not find this e-mail',
            'email.email' => 'Bad e-mail format'
        ];
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $user = User::where('email', $request->get('email'))->first();

            if ($user) {
                $password = User::generate_password(5);
                $user->password = Hash::make($password);
                $user->save();

                $mail = new ResetPassword($password);
                Mail::to($user->email)->send($mail);

                return response()->json([
                    'status' => true,
                    'message' => 'Reset success',
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No user',
                'data' => null
            ], 422);
        }

        return response()->json([
            'status' => false,
            'message' => $validator->errors()->getMessages(),
            'data' => null
        ], 422);
    }
}
