<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $notCryptedPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notCryptedPassword)
    {
        $this->notCryptedPassword = $notCryptedPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.resetPassword')->with([
            'password' => $this->notCryptedPassword,
        ])->subject('Сброс пароля на сайте.');
    }
}
