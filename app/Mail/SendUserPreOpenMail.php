<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendUserPreOpenMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $now_date;
    public $next_month;
    public $contract_date;
    public $update_contract_date;
    public $update_contract_subdate;
    public $update_contract_adddate;
    public $last_contract_date;
    public $fc;
    public $sub_day;
    public $myself_count;

    /**
     * Create a new message instance.
     */
    public function __construct($fc, $sub_day, $myself_count)
    {
        $this->now_date = Carbon::now()->format('Y年m月d日');
        $this->next_month = Carbon::now()->addMonth()->format('m月');
        $this->contract_date = Carbon::parse($fc['contract_date'])->format('m月d日');
        $this->update_contract_date = Carbon::now()->addDay(7)->format('Y年') . Carbon::parse($fc['contract_date'])->format('m月d日');
        $this->update_contract_subdate = Carbon::now()->format('Y年') . Carbon::parse($fc['contract_date'])->subDay()->format('m月d日');
        $this->update_contract_adddate = Carbon::now()->addDay(7)->format('Y年') . Carbon::parse($fc['contract_date'])->addDay()->format('m月d日');
        $this->last_contract_date = Carbon::now()->format('Y年') . Carbon::parse($fc['contract_date'])->format('m月d日');
        $this->fc = $fc;
        $this->sub_day = $sub_day;
        $this->myself_count = $myself_count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【重要】契約更新に関するお知らせ')
            ->markdown('emails.admin.users.preopenmail');
    }
}
