<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $notCryptedPassword;
    protected $user_country;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notCryptedPassword, $user_country)
    {
        $this->notCryptedPassword = $notCryptedPassword;
        $this->user_country = $user_country;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (User::getLanguage($this->user_country) === User::AVAILABLE_LANG_RU) {
            return $this->view('emails.resetPassword.ru')->with([
                'password' => $this->notCryptedPassword,
            ])->subject('Сброс пароля.');
        }

        return $this->view('emails.resetPassword.en')->with([
            'password' => $this->notCryptedPassword,
        ])->subject('Reset password.');
    }
}
