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
        $user = DB::table('user_change_emails')
            ->select('users.country')
            ->join('users', 'users.id', '=', 'user_change_emails.user_id')
            ->where('user_change_emails.token', $this->token)
            ->first();

        if ($this->start) {
            if (User::getLanguage($user->country) === User::AVAILABLE_LANG_RU) {
                return $this->view('emails.changeUserEmail.ru.start')->with([
                    'token' => $this->token,
                ])->subject('Изменение e-mail аккаунта.');
            }

            return $this->view('emails.changeUserEmail.en.start')->with([
                'token' => $this->token,
            ])->subject('Change e-mail request.');
        }

        if (User::getLanguage($user->country) === User::AVAILABLE_LANG_RU) {
            return $this->view('emails.changeUserEmail.ru.end')
                ->subject('E-mail аккаунта успешно изменен.');
        }

        return $this->view('emails.changeUserEmail.en.end')
            ->subject('Success e-mail change request.');
    }
}
