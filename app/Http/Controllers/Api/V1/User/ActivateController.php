<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 14:03
 */

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Services\ActivationService;

class ActivateController extends Controller
{
    protected $activationService;

    public function rules()
    {
        return [
            'token' => 'required|exists:user_activations'
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Token is required.',
            'token.exists' => 'No token in DB.'
        ];
    }

    public function __construct(ActivationService $activationService)
    {
        $this->activationService = $activationService;
    }

    public function activate($token)
    {
        if (!empty($token) && $this->activationService->getByToken($token)) {
            $user = $this->activationService->activate($token);

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
}