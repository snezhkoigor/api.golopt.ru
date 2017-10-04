<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Helpers\GetCountryFromIP;
use App\Http\Controllers\Controller;
use App\Mail\EmailRegister;
use App\Services\ActivationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'phone' => 'required',
            'country' => 'required|exists:countries,name',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Enter your e-mail address',
            'email.unique' => 'We have already this e-mail',
            'email.email' => 'Bad e-mail format',
            'email.max' => 'Max e-mail length is 50 characters',
            'phone.required' => 'Enter your phone number',
            'country.required' => 'Enter your country',
            'country.exists' => 'You have entered wrong country',
        ];
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $password = $request->get('password') ? $request->get('password') : User::generate_password(5);
            $callingCode = User::get_calling_code($request->get('country'));

            if (User::where('phone', User::replace_calling_code_from_phone($callingCode, $request->get('phone')))->first()) {
                $validator->getMessageBag()->add('phone', 'We have already this phone number');
            } else {
                $user = new User();
                    $user->email = $request->get('email');
                    $user->password = Hash::make($password);
                    $user->first_name = $request->get('first_name');
                    $user->last_name = $request->get('last_name');
                    $user->skype = $request->get('skype') ? $request->get('skype') : null;
                    $user->phone = User::replace_calling_code_from_phone($callingCode, $request->get('phone'));
                    $user->country = $request->get('country');
                    $user->calling_code = $callingCode;
                    $user->active = false;
                $user->save();

                if ($user) {
                    $user->assignRole('client');
                    $this->activationService->sendMail($user, false);
                    $this->activationService->sendSms($user);

                    $mail = new EmailRegister($user, $password);
                    Mail::to($user->email)->send($mail);

                    return response()->json([
                        'status' => true,
                        'message' => 'User created successfully',
                        'data' => null
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'User not created successfully',
                    'data' => null
                ], 422);
            }
        }

        return response()->json([
            'status' => false,
            'message' => $validator->errors()->getMessages(),
            'data' => null
        ], 422);
    }
}
