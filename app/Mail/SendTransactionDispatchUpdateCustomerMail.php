<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTransactionDispatchUpdateCustomerMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【サンプルFC】商品発送情報変更のお知らせ')
                    ->markdown('emails.admin.transaction.dispatch-update-customer');
    }
}
