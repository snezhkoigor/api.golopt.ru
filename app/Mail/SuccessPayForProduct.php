<?php

namespace App\Mail;

use App\Product;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuccessPayForProduct extends Mailable
{
    use Queueable, SerializesModels;

    protected $product;
    protected $is_demo;
    protected $user_country;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Product $product, $user_country, $is_demo = false)
    {
        $this->product = $product;
        $this->user_country = $user_country;
        $this->is_demo = $is_demo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (User::get_language($this->user_country) === User::AVAILABLE_LANG_RU) {
            return $this->view('emails.product.ru.get')->with([
                'product' => $this->product,
                'is_demo' => $this->is_demo,
            ])->subject('Ссылка на продукт.');
        }

        return $this->view('emails.product.en.get')->with([
            'product' => $this->product,
            'is_demo' => $this->is_demo,
        ])->subject('Product link.');
    }
}
