<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EndSubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $product_name = null;
    protected $subscribe_date_until = null;
    protected $user_country = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($product_name, $subscribe_date_until, $user_country)
    {
        $this->product_name = $product_name;
        $this->subscribe_date_until = $subscribe_date_until;
        $this->user_country = $user_country;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->user_country === 'Russia') {
            return $this->view('emails.endSubscription.en')->with([
                'product_name' => $this->product_name,
                'subscribe_date_until' => date('d.m.Y', strtotime($this->subscribe_date_until)),
            ])->subject('Заканчивается подписка.');
        }

        return $this->view('emails.endSubscription.en')->with([
            'product_name' => $this->product_name,
            'subscribe_date_until' => date('d.m.Y', strtotime($this->subscribe_date_until)),
        ])->subject('Subscription end.');
    }
}
