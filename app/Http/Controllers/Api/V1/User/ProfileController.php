<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:05
 */

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Services\ChangeEmailService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class ProfileController extends Controller
{
    protected $changeEmailService;

    public function __construct(ChangeEmailService $changeEmailService)
    {
        $this->middleware('jwt.auth');
        $this->changeEmailService = $changeEmailService;
    }

    public function rules(Request $request)
    {
        return [
            'email' => 'required|' . $this->emailRulesByChanging($request) . '|email|max:50',
            'phone' => 'numeric',
            'old_password' => $this->oldPasswordRulesByChanging($request),
            'new_password' => $this->newPasswordRulesByChanging($request)
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Enter your e-mail address.',
            'email.unique' => 'We have already this e-mail. Try to enter another one.',
            'email.email' => 'Bad e-mail format.',
            'email.max' => 'Sorry. But max e-mail length is 50 characters.',
            'new_password.required' => 'Enter your new password.',
            'old_password.required' => 'Enter your current password.'
        ];
    }

    public function newPasswordRulesByChanging($request)
    {
        return $request->get('old_password') ? 'required' : '';
    }

    public function oldPasswordRulesByChanging($request)
    {
        return $request->get('new_password') ? 'required' : '';
    }

    public function emailRulesByChanging($request)
    {
        $result = '';
        $user = JWTAuth::toUser($request->get('token'));

        if ($user && $request->get('email') !== $user->email) {
            $email = $request->get('email');
            $result = User::where('email', '=', $email)->exists() ? 'unique:users' : '';
        }

        return $result;
    }

    public function status(Request $request)
    {
        $user = JWTAuth::toUser($request->get('token'));

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $user
                    ->where('id', $user['id'])
                    ->first()
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No user.',
            'data' => null
        ]);
    }

    public function profile(Request $request)
    {
        $user = JWTAuth::toUser($request->get('token'));

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => null,
                'data' => $user
                    ->with([
                        'activation',
                        'role',
                        'activeChangeEmailRequests',
                        'changeEmailRequests',
                        'products',
                        'payments'
                    ])
                    ->where('id', $user['id'])
                    ->first()
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No user.',
            'data' => null
        ]);
    }

    public function update(Request $request)
    {
        $user = JWTAuth::toUser($request->token);

        if ($user) {
            $validator = Validator::make($request->all(), $this->rules($request), $this->messages());
            if (($request->get('new_password') || $request->get('old_password')) && !Hash::check($request->get('old_password'), $user->password)) {
                $validator->getMessageBag()->add('old_password', 'You have entered wrong current password.');
            } else if ($validator->fails() === false) {
                $user->skype = $request->get('skype') ? $request->get('skype') : null;
                $user->first_name = $request->get('first_name') ? $request->get('first_name') : null;
                $user->last_name = $request->get('last_name') ? $request->get('last_name') : null;
                $user->phone = $request->get('phone') ? $request->get('phone') : null;

                if ($request->get('new_password')) {
                    $user->password = Hash::make($request->get('new_password'));
                }

                if ($request->get('email') !== $user->email) {
                    $this->changeEmailService->sendMail($user, $request->get('email'));
                }

                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => null,
                    'data' => $user
                        ->with([
                            'activation',
                            'activeChangeEmailRequests',
                            'changeEmailRequests',
                            'products',
                            'payments'
                        ])
                        ->where('id', $user['id'])
                        ->first()
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->getMessages(),
                'data' => null
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No user.',
            'data' => null
        ]);
    }
}