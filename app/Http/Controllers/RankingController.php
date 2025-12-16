<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Contact;

class RankingController extends MyController
{
    public function __construct(Contact $contact)
    {
        parent::__construct();
        $this->model = $contact;
        $this->breadcrumbs->addCrumb('<i class="fas fa-box"></i>FCランキング', '/rankings?order=sales&year=' . date('Y') . "&month=" . date('m'));
    }

    public function index(Request $request)
    {
        $carbon = new Carbon();
        // ランキングの並び項目をクエリパラメーターでコントロール
        $order = !empty($request->get('order')) ? $request->get('order') : 'sales';
        $query = [
          'year' => !empty($request->get('year')) ? $request->get('year') : $carbon->now()->format('Y'),
          'month' => $request->get('month'),
          'order' => $order,
        ];
        $queryString = http_build_query($query);
        // 月の指定がなければ年間ランキング（6月〜翌5月）
        $rankings = $this->model->rankings($order, $query['year'], $query['month']);
        // dd($rankings);

        switch ($order) {
          case 'sales':
              $this->breadcrumbs->addCrumb('売り上げランキング');
              break;

          case 'number':
              $this->breadcrumbs->addCrumb('施工件数ランキング');
              break;

          default:
              $this->breadcrumbs->addCrumb('売り上げランキング');
              break;
        }


        // 過去の西暦を取得
        $date = Carbon::now();
        $year = $date->year;
        $i = 0;
        $pastYear = [];
        for ($y = $year; $y > 2019; --$y) {
            $pastYear[$i] = $y;
            ++$i;
        }
        // 過去の西暦を取得ここまで
        //dd($pastYear);

        $breadcrumbs = $this->breadcrumbs;

        return view('share.ranking.index', compact('rankings', 'order', 'breadcrumbs', 'pastYear', 'queryString', 'query'));
    }
}
