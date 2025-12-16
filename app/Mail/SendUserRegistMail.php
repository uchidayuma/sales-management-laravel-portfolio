<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserRegistMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inputs;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($inputs, $token)
    {
        $this->inputs = $inputs;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('サンプルFCへようこそ！')
                    ->markdown('emails.admin.users.user-regist');
    }
}
