<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Helpers\GetCountryFromIP;
use App\Http\Controllers\Controller;
use App\Mail\EmailRegister;
use App\Services\ActivationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Countries;
use JWTAuth;

class RegisterController extends Controller
{
    protected $activationService;

    public function __construct(ActivationService $activationService)
    {
        $this->activationService = $activationService;
    }

    public function rules()
    {
        return [
            'email' => 'required|unique:users|email|max:50',
//            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Enter your e-mail address.',
            'email.unique' => 'We have already this e-mail. Try to enter another one or login, please',
            'email.email' => 'Bad e-mail format.',
            'email.max' => 'Sorry. But max e-mail length is 50 characters.',
//            'password.required' => 'Enter your password.',
        ];
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $countryFromIp = GetCountryFromIP::execute();
            $password = $request->get('password') ? $request->get('password') : User::generate_password(5);

            $user = new User();
                $user->email = $request->get('email');
                $user->password = Hash::make($password);
                $user->country = ($countryFromIp && Countries::where('cca2', $countryFromIp)->first()) ? $countryFromIp : null;
                $user->first_name = $request->get('first_name');
                $user->last_name = $request->get('first_name');
                $user->skype = $request->get('skype') ? $request->get('skype') : null;
                $user->phone = $request->get('phone') ? $request->get('phone') : null;
                $user->active = false;
            $user->save();

            if ($user) {
                $user->assignRole('client');
                $this->activationService->sendMail($user);

                $mail = new EmailRegister($user, $password);
                Mail::to($user->email)->send($mail);

                return response()->json([
                    'status' => true,
                    'message' => 'User created successfully.',
//                    'data' => [
//                        'token' => JWTAuth::attempt($request->only('email', 'password'))
//                    ]
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'User not created successfully.',
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
