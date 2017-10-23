<?php
/**
 * Created by PhpStorm.
 * User: igorsnezko
 * Date: 01.08.17
 * Time: 13:38
 */

namespace App\Services;


use App\Mail\EmailActivate;
use App\Repositories\ActivationRepository;
use App\Smsc;
use App\User;
use Illuminate\Support\Facades\Mail;

class ActivationService
{
    protected $activationRepo;
    private $resendAfter = 24;

    public function __construct(ActivationRepository $activationRepo)
    {
        $this->activationRepo = $activationRepo;
    }

    public function sendSms($user)
    {
        if ($user->active || !$this->shouldSend($user)) {
            return;
        }

        $code = $this->activationRepo->create($user, true);
        return Smsc::send([
            'phones' => '+' . $user->calling_code . $user->phone,
            'mes' => view('sms.activation.' . User::get_language($user->country), ['code' => $code])
        ], $code);
    }

    public function sendMail($user, $createToken = true)
    {
        if ($user->active || !$this->shouldSend($user)) {
            return;
        }

        if ($createToken) {
            $this->activationRepo->create($user);
        }

        Mail::to($user->email)->send(new EmailActivate($user));
    }

    public function activate($token)
    {
        $record = $this->activationRepo->getByToken($token);

        if ($record === null) {
            return null;
        }

        $user = User::find($record->user_id);
            $user->active = true;
        $user->save();

        $this->activationRepo->delete($token);

        Mail::to($user->email)->send(new EmailActivate($user, false));

        return $user;

    }

    public function getByToken($token)
    {
        return $this->activationRepo->getByToken($token);
    }

    private function shouldSend($user)
    {
        $record = $this->activationRepo->get($user);

        return $record === null || strtotime($record->created_at) + 60 * 60 * $this->resendAfter < time();
    }

}