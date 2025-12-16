<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRequestQuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inputs;

    /**
     * Create a new message instance.
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【サンプルFC】見積もりの依頼')
                    ->markdown('emails.admin.quotation.request');
    }
}
