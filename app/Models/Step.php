<?php

namespace App\Models;

class Step extends MyModel
{
    // FCのトータルタスク計算用（本部は使わない）
    public function totalTasks()
    {
        $result = Contact::query()->selectRaw('COUNT(distinct contacts.id) AS count, step_id')
            ->where('contacts.user_id', \Auth::id())
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('steps AS s', 's.id', '=', 'contacts.step_id')
            ->whereIn('contacts.step_id', [self::STEP_APPOINT, self::STEP_ONSITE_CONFIRM, self::STEP_QUOTATION, self::STEP_RESULT, self::STEP_TRANSACTION, self::STEP_COMPLETE])
            ->first();

        return $result['count'];
    }

    public function adminViewSteps()
    {
        //FC未振り分け一覧
        $query = $this::query()->selectRaw('count(*) as count, steps.id AS step_id')
            ->where('c.status', 1)
            ->where('c.cancel_step', null)
            ->where('c.user_id', null)
            ->whereNotIn('contact_type_id', [1, 5])
            ->where('step_id', self::STEP_ASSIGN);
        $query->leftJoin('contacts AS c', 'c.step_id', '=', 'steps.id');
        $result = $query->first();
        //user_idがnullの場合[1][1]【FC未振り分け一覧】
        $tasks[1][1] = $result['count'];

        // サンプル送付リスト
        $query = Contact::query()->selectRaw('count(*) as count')
            ->whereNot('step_id', self::STEP_PAST_CUSTOMER)
            ->whereNot('step_id', self::STEP_CANCELATION)
            ->whereIn('status', [1, 3])->where('sample_send_at', null)
            ->where(function ($query) {
                $query->orWhere(function ($query1) {
                    $query1->whereIn('contact_type_id', [1, 5]);
                });
                $query->orWhere(function ($query2) {
                    $query2->whereIn('contact_type_id', [2, 3, 4, 6, 7, 8])->where('free_sample', '必要');
                });
            });
        $result = $query->first();
        $tasks[1]['sample_send'] = $result['count'];

        //本部見積もり案件一覧
        $query = $this::query()->selectRaw('count(*) as count, steps.id AS step_id')
            ->where('c.status', 1)
            ->where('c.cancel_step', null)
            ->where(function ($query) {
                $query->orWhere('user_id', 1)
                    ->orWhere('user_id', null);
            })
            ->where('quote_details', '材料のみ')
            ->whereIn('contact_type_id', [2, 6])
            ->where('c.step_id', '<=', self::STEP_RESULT)
            ->leftJoin('contacts AS c', 'c.step_id', '=', 'steps.id');
        $result = $query->first();
        //user_idがnullではない場合[1][2]【本部見積もり案件一覧】
        $tasks[1][2] = $result['count'];

        //商談結果入力街一覧
        $query = $this::query()->selectRaw('count(*) as count, steps.id AS step_id')
            ->whereIn('c.status', [1, 3])
            ->where('c.cancel_step', null)
            ->where('c.step_id', self::STEP_RESULT)
            ->where('user_id', 1);
        // ->whereIn('contact_type_id', [2, 6]);
        $query->leftJoin('contacts AS c', 'c.step_id', '=', 'steps.id');
        $result = $query->first();
        $tasks[1][3] = $result['count'];

        //部材発送待連絡待ち一覧
        $transaction_query = Contact::query()->select(\DB::raw('COUNT(*) as count'), 's.id AS step_id', 't.id')
            // $transaction_query = Contact::query()->select('contacts.id AS contact_id', 's.id AS step_id', 't.id AS transaction_id')
            ->where('contacts.status', 1)
            ->where('t.status', 1)
            ->whereNull('contacts.cancel_step')
            // ->whereIn('step_id', [self::STEP_SHIPPING, self::STEP_COMPLETE])
            ->where('step_id', self::STEP_SHIPPING)
            ->where('t.direct_shipping',  0)
            ->where(function ($query) {
                // 通常案件
                $query->orWhere(function ($query2) {
                    $query2->whereNull('t.transaction_only_shipping_date');
                    $query2->whereNull('t.transaction_only_shipping_number');
                    $query2->whereNull('t.transaction_only_shipping_id');
                });
                // サブ発注（追加発注分）
                $query->orWhere(function ($query3) {
                    $query3->whereNull('t.transaction_only_shipping_date');
                    // $query3->whereNull('t.shipping_cost');
                    // $query3->whereNotNull('contacts.shipping_date');
                });
                // 案件に紐づくかつ、全額前金の送料入力済み
                $query->orWhere(function ($query4) {
                    $query4->whereNotNull('contacts.id')
                        //   ->whereNotNull('t.shipping_cost')
                        ->where('t.prepaid', 2)
                        ->whereNull('t.transaction_only_shipping_date');
                });
            })
            ->leftJoin('steps AS s', 'contacts.step_id', '=', 's.id')
            ->leftJoin('transactions as t', 't.contact_id', '=', 'contacts.id');
        // ->groupBy('step_id');
        $result = $transaction_query->first();
        // dd($result);
        // $result = $transaction_query->get()->torray();
        // dd($transaction_query->get()->toArray());
        // dd($query->toSql());

        $tasks[9] = $result['count'];
        if (empty($tasks[9])) {
            $tasks[9] = 0;
        }
        $admin_cosutomer = Contact::where('contacts.status', 1)->where('step_id', self::STEP_SHIPPING)->where('contacts.user_id', 1)->join('quotations AS q', 'contacts.quotation_id', '=', 'q.id')->count();

        $transaction_only_fc = Transaction::select('c.*', 'c.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 'transactions.id AS transaction_id', 'transactions.address', 'transactions.consignee', 'transactions.created_at AS transaction_created_at', 'transactions.delivery_at')
            ->where('transactions.status', 1)
            ->whereNull('transactions.contact_id')
            ->whereNull('transactions.transaction_only_shipping_date')
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->count();
        // dd($transaction_only_fc);

        // FCが見積もりして、発注書を作成した顧客
        $customers_fc = Contact::selectRaw('count(*) as count, step_id')
            ->where('contacts.status', 1)
            ->where('step_id', self::STEP_SHIPPING)
            ->where('t.direct_shipping', 1)
            ->where('t.status', 1)
            ->whereNull('t.transaction_only_shipping_date')
            ->whereNull('t.transaction_only_shipping_id')
            ->whereNull('t.transaction_only_shipping_number')
            ->join('transactions AS t', 't.contact_id', '=', 'contacts.id')
            ->groupBy('step_id')
            ->count();

        // dd($tasks[9], $admin_cosutomer, $transaction_only_fc, $customers_fc);
        $tasks[9] = intval($tasks[9] + $admin_cosutomer + $transaction_only_fc + $customers_fc);
        $tasks['total'] = intval($tasks[1][1] + $tasks[1]['sample_send'] + $tasks[1][2] + $tasks[1][3] + $tasks[9]);

        return $tasks;
    }

    public function viewSteps()
    {
        // 複数枚の発注書があるので、Distinctで同一contacts.idはカウントしない
        $results = $this::selectRaw('COUNT(distinct c.id) AS count, steps.id AS step_id')
            ->whereIn('c.status', [1, 3])
            ->where('c.cancel_step', null)
            ->where('steps.id', '<', self::STEP_REPORT_COMPLETE)
            ->where('c.user_id', \Auth::id())
            ->whereIn('c.step_id', [self::STEP_APPOINT, self::STEP_ONSITE_CONFIRM, self::STEP_QUOTATION, self::STEP_RESULT, self::STEP_TRANSACTION, self::STEP_COMPLETE])
            ->leftJoin('contacts AS c', 'c.step_id', '=', 'steps.id')
            ->leftJoin('transactions as t', 't.contact_id', '=', 'c.id')
            ->groupBy('steps.id')->get();
        $tasks = [];
        foreach ($results as $key => $val) {
            $tasks[$val['step_id']] = $val['count'];
        }

        return $tasks;
    }
}
