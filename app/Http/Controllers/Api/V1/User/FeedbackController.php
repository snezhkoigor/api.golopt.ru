<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Mail\FeedbackEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class FeedbackController extends Controller
{
    public function rules()
    {
        return [
            'email' => 'required|email|max:50',
            'text' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Enter your e-mail address',
            'email.email' => 'Bad e-mail format',
            'email.max' => 'Max e-mail length is 50 characters',
            'text.required' => 'Enter your question for us',
        ];
    }

    public function question(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails() === false) {
            $user = User::where('email', $request->get('email'))->first();

            $feedback = new Feedback();
                $feedback->user_id = ($user) ? $user->id : null;
                $feedback->email = $request->get('email');
                $feedback->text = $request->get('text');
            $feedback->save();

            if ($feedback) {
                $mail = new FeedbackEmail($feedback, $user->country);
                Mail::to($feedback->email)->send($mail);

                return response()->json([
                    'status' => true,
                    'message' => 'Feedback question created successfully',
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Feedback question not created successfully',
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
