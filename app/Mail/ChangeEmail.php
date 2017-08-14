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
            $record = DB::table('user_change_emails')->where('user_id', $this->user->id)->first();

            return $this->view('emails.changeUserEmail.start')->with([
                'token' => $record->token ? $record->token : null,
            ])->subject('Изменение e-mail аккаунта.');
        }

        return $this->view('emails.changeUserEmail.end')
            ->subject('E-mail аккаунта успешно изменен.');
    }
}
