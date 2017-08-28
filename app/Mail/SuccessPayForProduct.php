<?php

namespace App\Mail;

use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuccessPayForProduct extends Mailable
{
    use Queueable, SerializesModels;

    protected $product;
    protected $is_demo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Product $product, $is_demo = false)
    {
        $this->product = $product;
        $this->is_demo = $is_demo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.product.get')->with([
            'product' => $this->product,
            'is_demo' => $this->is_demo,
        ])->subject('Ссылка на продукт.');
    }
}
