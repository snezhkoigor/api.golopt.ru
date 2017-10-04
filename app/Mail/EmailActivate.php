<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class EmailActivate extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $start;

    public function __construct(User $user, $start = true)
    {
        $this->user = $user;
        $this->start = $start;
    }

    public function build()
    {
        if ($this->start) {
            $country = DB::table('countries')
                ->where('name', '=', $this->user->country)
                ->first();

            return $this->view('emails.activateUser.start')->with([
                'token' => $this->user->activation['token'] ? $this->user->activation['token'] : null,
                'lang' => strtolower($country->code)
            ])->subject('Верификация аккаунта.');
        }

        return $this->view('emails.activateUser.end')->subject('Верификация аккаунта пройдена успешно.');
    }
}