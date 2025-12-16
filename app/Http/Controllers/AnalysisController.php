<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AnalysisRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Config;
use App\Models\Contact;
use App\Models\Prefecture;
use App\Models\User;
use Carbon\Carbon;

class AnalysisController extends MyController
{
    public function __construct(Contact $contact)
    {
        parent::__construct();
        $this->model = $contact;
        $this->breadcrumbs->addCrumb('<i class="fas fa-chart-bar"></i>データ分析', '');
    }

    public function index(AnalysisRequest $request)
    {
        if (isFc()) {
            return \Response::make('Unauthorized', 401);
        }
        $this->breadcrumbs->addCrumb('FC別問い合わせ件数');
        $breadcrumbs = $this->breadcrumbs;

        $carbon = new Carbon();
        $query_string = [
          'order' => !empty($request->get('order')) ? $request->get('order') : 'DESC',
          'fcs' => !empty($request->get('fcs')) ? $request->get('fcs') : [],
          'prefs' => !empty($request->get('prefs')) ? $request->get('prefs') : [],
          'type' => !empty($request->get('type')) ? $request->get('type') : 1,
          'display' => !empty($request->get('display')) ? $request->get('display') : 'yearmonth',
          'start' => !empty($request->get('start')) ? $request->get('start') :  $carbon->copy()->subYear()->format('Y-m'),
          'end' => !empty($request->get('end')) ? $request->get('end') :  $carbon->copy()->now()->format('Y-m'),
          'startyear' => !empty($request->get('startyear')) ? $request->get('startyear') :  $carbon->copy()->subYear()->format('Y'),
          'endyear' => !empty($request->get('endyear')) ? $request->get('endyear') :  $carbon->copy()->now()->format('Y'),
        ];
        $data['start'] = $query_string['start'];
        $data['end'] = $query_string['end'];

        $data['period'] = monthDiff($query_string['start'], $query_string['end']) + 1;
        $data['users'] = [];
        // 選択したデータ・タイプ別に取得データ分岐
        switch ($query_string['type']) {
            // 依頼件数
            case '1':
                $data['users'] = $this->model->analysisRequests($query_string);
                break;
            // 受注件数
            case '2':
                $data['users'] = $this->model->analysisTransactions($query_string);
                break;
            // お問い合わせ件数
            case '3':
                $data['users'] = $this->model->analysisContacts($query_string);
                break;
            default:
                $data['users'] = $this->model->analysisRequests($query_string);
                break;
        }
        // 2行目に来る合計データの生成
        $data['sums'] = $this->model->analysisSums($query_string, $data);
        $users = User::whereIn('status', [1,3,4])->orderBy('id', 'ASC')->get();
        $prefectures = Prefecture::all();
        // dd($data);
        return view('admin.analysis.index', compact('breadcrumbs', 'query_string', 'users', 'prefectures', 'data'));
    }

    public function contacts(AnalysisRequest $request)
    {
        if (isFc()) {
            return \Response::make('Unauthorized', 401);
        }
        $this->breadcrumbs->addCrumb('本部問い合わせ件数');
        $breadcrumbs = $this->breadcrumbs;

        $data = [];
        $carbon = new Carbon();
        $query_string = [
          'order' => !empty($request->get('order')) ? $request->get('order') : 'DESC',
          'type' => !empty($request->get('type')) ? $request->get('type') : 1,
          'display' => !empty($request->get('display')) ? $request->get('display') : 'yearmonth',
          'start' => !empty($request->get('start')) ? $request->get('start') :  $carbon->copy()->subYear()->format('Y-m'),
          'end' => !empty($request->get('end')) ? $request->get('end') :  $carbon->copy()->now()->format('Y-m'),
          'startyear' => !empty($request->get('startyear')) ? $request->get('startyear') :  $carbon->copy()->subYear()->format('Y'),
          'endyear' => !empty($request->get('endyear')) ? $request->get('endyear') :  $carbon->copy()->now()->format('Y'),
        ];
        $data['start'] = $query_string['start'];
        $data['end'] = $query_string['end'];

        $data['period'] = monthDiff($query_string['start'], $query_string['end']) + 1;
        $data['prefectures'] = ["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
        "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
        "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
        "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
        "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
        "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
        "熊本県","大分県","宮崎県","鹿児島県","沖縄県"];
        // // 2行目に来る合計データの生成
        $data['contacts'] = $this->model->adminContactsAnalysis($query_string, $data);
        // $users = User::whereIn('status', [1,3,4])->orderBy('id', 'ASC')->get();
        // $prefectures = Prefecture::all();
        // dd($data);
        return view('admin.analysis.contacts', compact('breadcrumbs', 'query_string', 'data'));
    }

    public function fcIndex(AnalysisRequest $request)
    {
        $this->breadcrumbs->addCrumb('データ分析');
        $breadcrumbs = $this->breadcrumbs;

        $customers = \DB::table('contacts AS c')->select('c.id', 'c.city', 'c.age', 'c.where_find',
          'q.sub_total', 'p.name AS product_name', 'p.product_type_id')
          ->distinct('c.id')
          ->where('c.user_id', \Auth::id())
          ->where(function ($query) {
                $query->orWhere('p.product_type_id', 1)->orWhere('p.product_type_id', null);
          })
          ->leftJoin('quotations AS q', 'c.quotation_id', '=', 'q.id')
          ->leftJoin('product_quotations AS pq', 'q.id', '=', 'pq.quotation_id')
          ->leftJoin('products AS p', 'p.id', '=', 'pq.product_id')
          ->orderBy('c.id')->get();
        foreach($customers as $key => $c) {
            $city = dripCity($c->city);
            $customers[$key]->city = $city; 
//dd($customers[$key]);
	}
           //dd($customers);
        return view('fc.analysis.index', compact('customers'));
    }

    public function contactDetail(AnalysisRequest $request)
    {
        if (isFc()) {
            return \Response::make('Unauthorized', 401);
        }
        $this->breadcrumbs->addCrumb('お問い合わせ詳細');
        $breadcrumbs = $this->breadcrumbs;

        $data = [];
        $carbon = new Carbon();
        $query_string = [
          'order' => !empty($request->get('order')) ? $request->get('order') : 'DESC',
          'type' => !empty($request->get('type')) ? $request->get('type') : 'contact_detail_ages',
          'display' => !empty($request->get('display')) ? $request->get('display') : 'yearmonth',
          'start' => !empty($request->get('start')) ? $request->get('start') :  $carbon->copy()->subYear()->format('Y-m'),
          'end' => !empty($request->get('end')) ? $request->get('end') :  $carbon->copy()->now()->format('Y-m'),
          'startyear' => !empty($request->get('startyear')) ? $request->get('startyear') :  $carbon->copy()->subYear()->format('Y'),
          'endyear' => !empty($request->get('endyear')) ? $request->get('endyear') :  $carbon->copy()->now()->format('Y'),
        ];
        // configテーブルのkeyに'contact_detail'と付くものを取得
        $list = Config::where('key', 'like', 'contact_detail%')->get()->toArray();
        foreach ($list as $key => $l) {
            $list[$key]['values'] = collect(explode(',', $l['value']));
        }
        $data['start'] = $query_string['start'];
        $data['end'] = $query_string['end'];

        $data['period'] = monthDiff($query_string['start'], $query_string['end']) + 1;
        $data['prefectures'] = ["北海道","青森県","岩手県","宮城県","秋田県","山形県","福島県",
        "茨城県","栃木県","群馬県","埼玉県","千葉県","東京都","神奈川県",
        "新潟県","富山県","石川県","福井県","山梨県","長野県","岐阜県",
        "静岡県","愛知県","三重県","滋賀県","京都府","大阪府","兵庫県",
        "奈良県","和歌山県","鳥取県","島根県","岡山県","広島県","山口県",
        "徳島県","香川県","愛媛県","高知県","福岡県","佐賀県","長崎県",
        "熊本県","大分県","宮崎県","鹿児島県","沖縄県"];
        // // 2行目に来る合計データの生成
        // $users = User::whereIn('status', [1,3,4])->orderBy('id', 'ASC')->get();
        // $prefectures = Prefecture::all();
        // dd($data);
        return view('admin.analysis.contactdetail', compact('breadcrumbs', 'query_string', 'data', 'list'));
    }

    public function ajaxGetContactDetail(Request $request)
    {
        $carbon = new Carbon();
        $query_string = [
        //   'order' => !empty($request->get('order')) ? $request->get('order') : 'DESC',
          'type' => !empty($request->get('type')) ? $request->get('type') : 'contact_detail_ages',
          'display' => !empty($request->get('display')) ? $request->get('display') : 'yearmonth',
          'start' => !empty($request->get('start')) ? $request->get('start') :  $carbon->copy()->subYear()->format('Y-m'),
          'end' => !empty($request->get('end')) ? $request->get('end') :  $carbon->copy()->now()->format('Y-m'),
          'startyear' => !empty($request->get('startyear')) ? $request->get('startyear') :  $carbon->copy()->subYear()->format('Y'),
          'endyear' => !empty($request->get('endyear')) ? $request->get('endyear') :  $carbon->copy()->now()->format('Y'),
        ];

        \Log::debug(print_r($query_string, true));
        $list = Config::where('key', $query_string['type'])->first();
        $list->values =collect(explode(',', $list->value));
        $contacts = [];

        switch ($query_string['type']) {
            case 'contact_detail_ages':
                $contacts = $this->model->analysisContactDetailAges($query_string);
                break;
            case 'contact_detail_turf_purpose':
                $contacts = $this->model->analysisContactDetailSum($query_string, 'use_application', $list);
                break;
            case 'contact_detail_where_find':
                $contacts = $this->model->analysisContactDetailSum($query_string, 'where_find', $list);
                break;
            case 'contact_detail_sns':
                $contacts = $this->model->analysisContactDetailSum($query_string, 'sns', $list);
                break;
            default:
                $contacts = $this->model->analysisContactDetailAges($query_string);
                break;
        }
        \Log::debug(print_r($contacts, true));

        return response()->json([
            'contacts' => $contacts,
            'list' => $list,
            'query_string' => $query_string,
        ]);
    }
}
