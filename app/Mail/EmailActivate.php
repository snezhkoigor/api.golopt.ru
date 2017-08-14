<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            return $this->view('emails.activateUser.start')->with([
                'token' => $this->user->activation['token'] ? $this->user->activation['token'] : null,
            ])->subject('Верификация аккаунта.');
        }

        return $this->view('emails.activateUser.end')->subject('Верификация аккаунта пройдена успешно.');
    }
}