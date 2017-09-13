<?php

namespace App\Mail;

use App\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function build()
    {
        return $this->from($this->feedback->email)
            ->to(config('mail.feedback_to.address'), config('mail.feedback_to.name'))
            ->text('emails.feedback.administrator')
            ->with([
                'text' => $this->feedback->text
            ])
            ->subject('Обратная связь.');
    }
}