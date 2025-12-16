<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserOpenMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $now_date;
    public $next_month;
    public $fc;

    /**
     * Create a new message instance.
     */
    public function __construct($fc)
    {
        $this->now_date = westernYearToJapaneseYear(Carbon::now()->format('Y')) . Carbon::now()->format('m月d日');
        $this->next_month = Carbon::now()->addMonth()->format('m月');
        $this->fc = $fc;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('《重要》 【サンプルFC】本部　FC加盟店追加募集に関するご連絡')
                    ->markdown('emails.admin.users.openmail');
    }
}
