<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTransactionUpdateToFcMail extends Mailable
{
    use Queueable, SerializesModels;

    public $posts;

    /**
     * Create a new message instance.
     */
    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('サンプルFC管理システム【発注書の変更】')
                    ->markdown('emails.admin.transaction.update-to-fc');
    }
}
