<?php

namespace App\Mail;

use App\User;
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
    protected $product_group = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($product_name, $subscribe_date_until, $user_country, $product_group)
    {
        $this->product_name = $product_name;
        $this->subscribe_date_until = $subscribe_date_until;
        $this->user_country = $user_country;
        $this->product_group = $product_group;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (User::get_language($this->user_country) === User::AVAILABLE_LANG_RU) {
            return $this->view('emails.endSubscription.ru')->with([
                'product_name' => $this->product_name,
                'subscribe_date_until' => date('d.m.Y', strtotime($this->subscribe_date_until)),
                'product_group' => $this->product_group
            ])->subject('Заканчивается подписка.');
        }

        return $this->view('emails.endSubscription.en')->with([
            'product_name' => $this->product_name,
            'subscribe_date_until' => date('d.m.Y', strtotime($this->subscribe_date_until)),
            'product_group' => $this->product_group
        ])->subject('Subscription end.');
    }
}
