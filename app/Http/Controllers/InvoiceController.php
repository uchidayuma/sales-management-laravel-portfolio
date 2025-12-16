<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class InvoiceController extends MyController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(isFc()){
            return redirect('/');
        }
        $this->breadcrumbs->addCrumb('月別請求書', '')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $date = Carbon::now();
        $currentYear = $date->year;
        $currentMonth = $date->month;
        $year = $request->get('year');
        $month = $request->get('month');
        $query = [
          'year' => $request->get('year'),
          'month' => $month,
        ];
        // dd($currentYear);
        if(empty($query['year']) || empty($query['month'])){
            $query = [
            //   'year' => $currentMonth === 1 ? $currentYear - 1 : $currentYear,
              'year' => $currentYear,
              'month' => $date->month,
            ];
            $year = $query['year'];
            $month = $query['month'];
        }
        $queryString = http_build_query($query);

        // 過去の西暦を取得
        $i = 0;
        $pastYear = [];
        for ($y = $currentYear; $y > 2019; --$y) {
            $pastYear[$i] = $y;
            ++$i;
        }
        $fcs = User::whereIn('status', [1,3])->where('role', 2)->orderBy('id', 'DESC')->get();
        $invoices = [];
        $profit = 0;
        $when = '(
            CASE
                WHEN (transactions.shipping_date3 IS NULL)
                THEN
                    CASE
                        WHEN (transactions.shipping_date2 IS NULL)
                        THEN transactions.transaction_only_shipping_date
                        ELSE transactions.shipping_date2 
                    END
                ELSE transactions.shipping_date3 END)';
        foreach ($fcs as $fc) {
            $invoices[$fc['id']]['user'] = $fc;
            //期間を指定してあるFCのすべての請求書を取得
            $invoices[$fc['id']]['transactions'] = Transaction::select('transactions.*', \DB::raw('(CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date  END) AS shipping_date'))
            ->where('transactions.user_id', $fc['id'])
            ->where('transactions.status', 1)
            /* 一旦コメントアウト
            ->where(function ($query) {
                // 基本的にはstatusが1のものを持ってくる
                $query->orWhere(function ($query) {
                    $query->where('transactions.status', 1);
                });
                // 例外としてstatus = 2 かつ shipping_date がある物
                $query->orWhere(function ($query) {
                    $query->where('transactions.status', 2);
                    $query->where('c.shipping_date', '!=' , null);
                });
                // 例外としてstatus = 2 かつ transaction_only_shipping_date がある物
                $query->orWhere(function ($query) {
                    $query->where('transactions.status', 2);
                    $query->where('transactions.transaction_only_shipping_date', '!=' , null);
                });
            })
            */
            // ->whereBetween(
            //     \DB::raw(
                    
            //         'CASE 
            //             WHEN transactions.transaction_only_shipping_date IS NULL 
            //             THEN c.shipping_date 
            //             ELSE transactions.transaction_only_shipping_date  
            //         END'
            //     ), 
            //     ["$query[year]-$query[month]-01", "$query[year]-$query[month]-31"]
            // )
            ->whereBetween(\DB::raw($when), ["$query[year]-$query[month]-01", "$query[year]-$query[month]-31"])
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->orderBy('user_id', 'ASC')
            ->get();
            // その月の請求書の合計金額をfcごとに計算 n>0のものだけ次のステップへ
            if(!empty($invoices[$fc['id']]['transactions'][0])){
                foreach($invoices[$fc['id']]['transactions'] AS $t){
                    $profit += intval($t['total']);
                    \Log::debug($t['total']);
                }
            }
        }
        // 過去の西暦を取得ここまで
        return view('admin.invoice.index', compact('breadcrumbs', 'invoices', 'profit', 'queryString', 'year', 'month', 'pastYear'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
