<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PriceChangeLog;
use App\Models\Product;
use Carbon\Carbon;

class SlackController extends MyController
{
    public function __construct(Contact $contact)
    {
        parent::__construct();
        $this->model = $contact;
    }

    public function getNewContact(Request $request){
        // クエリパラメータ付けると指定の日付で取れる ?date=2021-02-26
        if($request->get('date')){
            $date = $request->get('date');
            $yesterdayCarbon = new Carbon($request->get('date'));
            $yesterday = $yesterdayCarbon->subDay();
            $yesterdayStr = $yesterdayCarbon->format('Y年m月d日');
        }else{
            $date = Carbon::yesterday()->format('Y-m-d');
            $yesterday = Carbon::yesterday();
            $yesterdayStr = $yesterday->format('Y年m月d日');
        }
        $contactModel = new Contact();
        $userModel = new User();
        /* 新規メール*/
        $mailCnt = $contactModel->slackEmail($yesterday);
        
        /* 協力店受注 */
        list($transactionsCount, $transactionsData) = $contactModel->slackTransactions($yesterday);
        $transactionsData = json_encode($transactionsData, JSON_UNESCAPED_UNICODE);

        /* FC依頼 */
        list($contactsCount, $contactsData) = $contactModel->slackFcAssign($yesterday);
        $contactsData = json_encode($contactsData, JSON_UNESCAPED_UNICODE);

        /* エリア開放メール */
        $sends = $userModel->areaOpenEmailSends(Carbon::yesterday()->format('Y-m-d'));
        $send_list = '';
        foreach($sends as $s){
            $send_list = $send_list . '  FCID=' . $s['id'] . ' ' . $s['company_name']. "\n" . ' ';
        }

        $ch = curl_init();
        $header = ['Content-type: application/json'];
        curl_setopt($ch, CURLOPT_URL, config('app.daily_report_url'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // $body = json_encode("$yesterdayStr の問い合わせ状況を報告いたします。, 新規メール + TEL問い合わせ $mailCnt 件, 協力店受注  $transactionsCount 件 \n $transactionsData, FC依頼 $contactsCount 件 \n $contactsData", JSON_UNESCAPED_UNICODE);
        $body = json_encode("$yesterdayStr の問い合わせ状況を報告いたします。, 新規メール + TEL問い合わせ $mailCnt 件, 協力店受注  $transactionsCount 件 \n $transactionsData, FC依頼 $contactsCount 件 \n $contactsData \n ■エリア開放メール送信FC \n $send_list", JSON_UNESCAPED_UNICODE);
        $body = str_replace ( ',', '\n', $body );
        $target = ['[', ']', '\"'];
        $body = str_replace ( $target, '', $body );
        curl_setopt($ch, CURLOPT_POSTFIELDS,"{'text': $body }");

        $result = curl_exec($ch);
        \Log::debug('========毎日報告結果==========');
        \Log::debug(print_r($result, true));
    }

    // 1時間に1度h価格のh監視を行う
    public function getPriceChangeLog()
    {
        $changeLogs = PriceChangeLog::where('change_time', '>=', Carbon::now()->subHour())->get();

        foreach($changeLogs AS $log){
            $item = Product::find($log['product_id']);
            $change_time = Carbon::parse($log['change_time'])->format('Y年m月d日 H時i分');
            $field_name = convertPriceFieldName($log['field_name']);
            $message = $item['name'] . 'の' . $field_name . 'が' . $change_time . 'に' . $log['old_value'] . '円から' . $log['new_value'] . '円に変更されました。';
            $channel = \App::environment('production') ? '#system-dev-team' : '#sample-dev';
            toSlack(config('services.slack.web_hook_url'), $channel, $message);
        }
    }
}
