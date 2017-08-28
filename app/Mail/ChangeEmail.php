<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ChangeEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;
    protected $start;

    public function __construct($token, $start = true)
    {
        $this->token = $token;
        $this->start = $start;
    }

    public function build()
    {
        if ($this->start) {
            return $this->view('emails.changeUserEmail.start')->with([
                'token' => $this->token,
            ])->subject('Изменение e-mail аккаунта.');
        }

        return $this->view('emails.changeUserEmail.end')
            ->subject('E-mail аккаунта успешно изменен.');
    }
}
