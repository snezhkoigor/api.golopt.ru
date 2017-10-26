<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailRegister extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $notCryptedPassword;

    public function __construct(User $user, $notCryptedPassword = null)
    {
        $this->user = $user;
        $this->notCryptedPassword = $notCryptedPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (User::getLanguage($this->user->country) === User::AVAILABLE_LANG_RU) {
            return $this->view('emails.registration.ru')->with([
                'email' => $this->user->email,
                'password' => $this->notCryptedPassword,
            ])->subject(config('app.name') . ' регистрация.');
        }

        return $this->view('emails.registration.en')->with([
            'email' => $this->user->email,
            'password' => $this->notCryptedPassword,
        ])->subject(config('app.name') . ' registration.');
    }
}