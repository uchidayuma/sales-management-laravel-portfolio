<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserNotificateMail extends Mailable
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
        return $this->subject('サンプルFC管理システムより未対応案通知があります。')
                    ->markdown('emails.admin.notifications/user');
    }
}
