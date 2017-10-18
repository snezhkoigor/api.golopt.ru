<?php

namespace App\Mail;

use App\Feedback;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $feedback;
    protected $user_country;

    public function __construct(Feedback $feedback, $user_country)
    {
        $this->feedback = $feedback;
        $this->user_country = $user_country;
    }

    public function build()
    {
        return $this->from($this->feedback->email)
            ->to(config('mail.feedback_to.address'), config('mail.feedback_to.name'))
            ->text('emails.feedback.administrator')
            ->with([
                'text' => $this->feedback->text
            ])
            ->subject(User::get_language($this->user_country) === User::AVAILABLE_LANG_RU ? 'Обратная связь.' : 'Feedback');
    }
}