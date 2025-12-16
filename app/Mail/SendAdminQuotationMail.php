<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAdminQuotationMail extends Mailable
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
        $send = $this->subject('【サンプルFC】材料見積もり書の送付')->markdown('emails.admin.quotation.send');
        foreach ($this->inputs['q'] as $key => $val) {
            $this->attachFromStorageDisk('s3', '/quotations/'.$this->inputs['contact_id'].'/見積書No.'.$val.'.pdf');
        }
        $attachFiles = \Storage::disk('s3')->files('/quotations/'.$this->inputs['contact_id'].'/files');
        foreach ($attachFiles as $file) {
            $this->attachFromStorageDisk('s3', $file);
        }

        return $send;
    }
}
