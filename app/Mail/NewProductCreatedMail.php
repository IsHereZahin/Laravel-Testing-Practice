<?php

namespace App\Mail;

use App\Models\Products;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProductCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The products instance.
     *
     * @var \App\Models\Products
     */
    public $products;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Products  $products
     * @return void
     */
    public function __construct(Products $products)
    {
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.product_created');
    }
}
