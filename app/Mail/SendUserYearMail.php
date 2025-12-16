<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserYearMail extends Mailable
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
        return $this->subject('【重要】ブランド使用料の件')
                    ->markdown('emails.admin.users.yearmail');
    }
}
