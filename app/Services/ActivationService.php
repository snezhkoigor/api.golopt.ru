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

        $this->activationRepo->create($user, true);

        Smsc::send_sms($user->calling_code . $user->phone, $user->activation['token'], 0, 0, 0, 0, config('smsc.SMSC_FROM'));
    }

    public function sendMail($user)
    {
        if ($user->active || !$this->shouldSend($user)) {
            return;
        }

        $this->activationRepo->create($user);

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