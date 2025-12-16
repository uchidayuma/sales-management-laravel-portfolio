<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Models\Config;
use App\Models\Quotation;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends MyController
{
    public function index()
    {
        $user = \Auth::user();

        //退会済ユーザーがログインしようとした際の処理
        $status = \Auth::user()->status;
        if($status === 2){
            //404ページを返す
            return \App::abort(404);
        }

        $dates = [];

        $dates['from'] = date('Y-m-d', strtotime('first day of this month'));
        $dates['to'] = date('Y-m-d', strtotime('last day of this month'));
        $dates['from_last_month'] = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, 1));
        $dates['to_last_month'] = date('Y-m-d', mktime(0, 0, 0, date('m'), 0));
        // dd($dates['to_last_month']);
        $dates['today'] = date('Y-m-d');
        $dates['yesterday'] = date('Y-m-d', strtotime('-1day'));
        $dates['day_before_yesterday'] = date('Y-m-d', strtotime('-2day'));

        $days_of_month = [];
        for ($i = $dates['from']; $i <= $dates['to']; $i = date('Y-m-d', strtotime($i.'+1 day'))) {
            $days_of_month[] = [$i];
        }

        $days = call_user_func_array('array_merge', $days_of_month);

        //dd($days);

        if (isAdmin()) {
            //新規問い合わせ件数を取得、chartにセットする変数を準備
            $contactTotal = Contact::where('status', 1)->whereBetween('created_at', [$dates['from'], $dates['to']])->count();
            //dd($contactTotal); 総件数
            $contactLastMonthTotal = Contact::where('status', 1)->whereBetween('created_at', [$dates['from_last_month'], $dates['to_last_month']])->count();
            //dd($contactLastMonthTotal);

            for ($i = 0; $i < count($days); ++$i) {
                $input = date($days[$i]);
                if ($input == $dates['today']) {
                    $countYesterday = Contact::where('status', 1)->whereDate('created_at', $dates['yesterday'])->count();
                    $countDayBeforeYesterday = Contact::where('status', 1)->whereDate('created_at', $dates['day_before_yesterday'])->count();
                }
                $single = Contact::where('status', 1)->whereDate('created_at', $input)->count();
                $completeArray[$i] = $single;
            }

            $avg = round($contactTotal / count($days), 1);

            //新規発注件数を取得、chartにセットする変数を準備
            $transactionTotal = Transaction::where('status', 1)->whereBetween('created_at', [$dates['from'], $dates['to']])->count();
            $transactionLastMonthTotal = Transaction::where('status', 1)->whereBetween('created_at', [$dates['from_last_month'], $dates['to_last_month']])->count();
            // dd($transactionLastMonthTotal);

            for ($i = 0; $i < count($days); ++$i) {
                $input = date($days[$i]);
                if ($input == $dates['today']) {
                    $transactionYesterday = Transaction::where('status', 1)->whereDate('created_at', $dates['yesterday'])->count();
                    $transactionDayBeforeYesterday = Transaction::where('status', 1)->whereDate('created_at', $dates['day_before_yesterday'])->count();
                }
                $transaction = Transaction::where('status', 1)->whereDate('created_at', $input)->count();
                $completeTransactionArray[$i] = $transaction;
            }
            //dd($completeTransactionArray);

            $avgTransaction = round($transactionTotal / count($days), 1);

            //人工芝の在庫7種類
            $stocks = Product::select('name', 'stock')->where('product_type_id', 1)->where('status', 1)->orderBy('id', 'DESC')->get()->toArray();

            //訪問見積もり
            $visitCustomers = Contact::where('status', 1)->whereIn('contact_type_id', [3, 7])->where('step_id', 1)->where('cancel_step', null)->orderBy('created_at', 'DESC')->get();

            //図面見積もり
            $drawingCustomers = Contact::where('status', 1)->whereIn('contact_type_id', [2, 6])->where('step_id', 1)->where('cancel_step', null)->orderBy('created_at', 'DESC')->get();

            //発注書対応ステップ
            $contactShippings = Contact::where('status', 1)->where('step_id', 7)->where('cancel_step', null)->orderby('created_at', 'DESC')->get();

            // 依頼日から日が経っているのに、案件詳細ページが見られていない案件一覧
            $leave_day = Config::where('key', 'leave_alone_days')->first();
            $assign_day = new Carbon();
            $leave_alone_list_query = Contact::query()->select('contacts.*', 'u.name AS user_name')->where('contacts.status', 1)        
                ->whereNull('fc_confirmed_at')->whereNotNull('user_id')->whereNotNull('fc_assigned_at')
                ->whereRaw('fc_assigned_at + INTERVAL ' . $leave_day['value'] . ' DAY <= NOW()');
                // ->whereRaw('fc_assigned_at > contacts.created_at + INTERVAL ' . $leave_day['value'] . ' DAY');
            if( isProduction() ){
                $leave_alone_list_query->where('contacts.created_at', '>', '2021-10-29');
            }
            // dd($leave_alone_list_query);
            $leave_alone_list = $leave_alone_list_query->join('users as u', 'u.id', '=', 'contacts.user_id')->orderBy('contacts.created_at', 'DESC')->get();
            // dd($leave_alone_list);

            /* 訪問見積もり、図面見積もりの面積が100m2以上
            OR
            施工見積もりの面積が100m2以上
            OR
            材料見積もりの面積が100m2以上 */
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $sixmonths_ago = Carbon::now()->subMonth(6)->format('Y-m-d');
            $large_contacts = Contact::select('contacts.id', 'contacts.surname', 'contacts.name', 'contacts.company_name', 'contacts.vertical_size', 'contacts.horizontal_size', 'contacts.square_meter', 'contacts.created_at', 'contacts.desired_product',
            'u.name AS user_name', 'contacts.quotation_id AS quotation_id', 'contacts.status AS quotation_type',
            // desired_productがないっぽい
                DB::raw('CASE
                    WHEN vertical_size IS NULL THEN square_meter
                    ELSE CAST(vertical_size AS DECIMAL(10,2)) * CAST(horizontal_size AS DECIMAL(10,2))
                END AS area')
             )
                ->where('contacts.status', 1)->whereNull('cancel_step')->whereNull('q.id')
                ->whereBetween('step_id', [1, 6])->whereBetween('contacts.created_at', [$sixmonths_ago, $now])
                ->where(function ($query) {
                    // 訪問見積もり、図面見積もりの面積が100m2以上
                    $query->where(function ($query2) {
                        $query2->orWhereRaw('CAST(vertical_size AS DECIMAL(10,2)) * CAST(horizontal_size AS DECIMAL(10,2)) >= 100');
                        $query2->orWhere('square_meter', '>=', 100);
                        $query2->where(function ($query3) {
                            $query3->orwhere('contact_type_id', 2)->orwhere('contact_type_id', 3)->orwhere('contact_type_id', 6)->orwhere('contact_type_id', 7);
                        });
                    });
                    // 施工見積もりの面積が100m2以上
                    $query->where(function ($query2) {
                        $query2->orWhereRaw('CAST(vertical_size AS DECIMAL(10,2)) * CAST(horizontal_size AS DECIMAL(10,2)) >= 100');
                        $query2->orWhere('square_meter', '>=', 100);
                        $query2->where(function ($query3) {
                            $query3->orwhere('contact_type_id', 2)->orwhere('contact_type_id', 3)->orwhere('contact_type_id', 6)->orwhere('contact_type_id', 7);
                        });
                    });
                })
                ->leftJoin('quotations AS q', 'q.contact_id', '=', 'contacts.id')
                ->join('users AS u', 'u.id', '=', 'contacts.user_id')
                ->groupBy('contacts.id')
                ->orderBy('contacts.created_at', 'DESC');
// dd($large_contacts->get());

            //施工見積もりの面積が100m2以上
            // $large_quotations = Quotation::select('contacts.id', 'contacts.surname', 'contacts.name', 'contacts.company_name', 'contacts.vertical_size', 'contacts.horizontal_size', 'contacts.square_meter', 'contacts.created_at', 'contacts.desired_product',
            // 'u.name AS user_name', 'quotations.id AS quotation_id', 'quotations.type AS quotation_type',
            //     DB::raw('SUM(pq.num) as area'),
            // )
            // // $large_quotations = Contact::select('q.id AS quotation_id', DB::raw('SUM(pq.num) as area'))
            //     ->where('contacts.status', 1)
            //     ->where('quotations.status', 1)->where('quotations.type', 0)
            //     ->where('p.product_type_id', 1)
            //     ->where('pq.status', 1)
            //     // ->whereBetween('step_id', [1, 6])->whereBetween('contacts.created_at', [$sixmonths_ago, $now])
            //     ->join('contacts', 'quotations.contact_id', '=', 'contacts.id')
            //     ->leftJoin('product_quotations AS pq', 'pq.id', '=', 'quotations.id')
            //     ->join('products AS p', 'p.id', '=', 'pq.product_id')
            //     ->join('users AS u', 'u.id', '=', 'contacts.user_id')
            //     ->groupBy('pq.quotation_id')
            //     ->havingRaw('area >= 100')
            //     ->orderBy('contacts.created_at', 'DESC');
            //     $large_quotations = null;
            //     // $large_quotations = Quotation::select('quotations.id', DB::raw('SUM(pq.num) as area'))
            $large_quotations = Quotation::select('contacts.id', 'contacts.surname', 'contacts.name', 'contacts.company_name', 'contacts.vertical_size', 'contacts.horizontal_size', 'contacts.square_meter', 'contacts.created_at', 'contacts.desired_product',
            'u.name AS user_name', 'quotations.id AS quotation_id', 'quotations.type AS quotation_type',
                DB::raw('SUM(pq.num) as area'),
            )
                ->where('quotations.status', 1)->where('quotations.type', 0)
                ->where('p.product_type_id', 1)
                ->where('pq.status', 1)
                ->whereBetween('step_id', [1, 6])->whereBetween('contacts.created_at', [$sixmonths_ago, $now])
                ->join('contacts', 'quotations.contact_id', '=', 'contacts.id')
                ->leftJoin('product_quotations AS pq', 'pq.quotation_id', '=', 'quotations.id')
                ->join('products AS p', 'p.id', '=', 'pq.product_id')
                ->join('users AS u', 'u.id', '=', 'contacts.user_id')
                ->groupBy('pq.quotation_id')
                ->havingRaw('area >= 100')
                ->orderBy('contacts.created_at', 'DESC');

            // 材料見積もりの面積が100m2以上
            $large_material_quotations = DB::table('quotations AS q')->select('contacts.id', 'contacts.surname', 'contacts.name', 'contacts.company_name', 'contacts.vertical_size', 'contacts.horizontal_size', 'contacts.square_meter', 'contacts.created_at', 'contacts.desired_product',
            'u.name AS user_name', 'q.id AS quotation_id', 'q.type AS quotation_type',
                    DB::raw('SUM(CASE WHEN pqm.cut = 0 AND p.product_type_id = 1 THEN (p.horizontal * p.vertical * pqm.num) WHEN pqm.cut = 1 AND p.product_type_id = 1 THEN pqm.num ELSE 0 END) AS area'),
                )
                ->where('q.status', 1)->where('q.type', 1)->whereNotNull('contacts.quotation_id')
                ->whereBetween('step_id', [1, 6])->whereBetween('contacts.created_at', [$sixmonths_ago, $now])
                ->where('p.product_type_id', 1)
                ->where('pqm.status', 1)
                ->join('contacts', 'q.contact_id', '=', 'contacts.id')
                ->leftJoin('product_quotation_materials AS pqm', 'pqm.quotation_id', '=', 'q.id')
                ->join('products AS p', 'p.id', '=', 'pqm.product_id')
                ->join('users AS u', 'u.id', '=', 'contacts.user_id')
                ->groupBy('pqm.quotation_id')
                ->havingRaw('area >= 100')
                ->orderBy('contacts.created_at', 'DESC');

                // dd($large_material_quotations->get());

            $large_contacts = $large_contacts->union($large_quotations)->union($large_material_quotations)->orderBy('created_at', 'DESC')->get();
            // dd($large_contacts);

            foreach ($large_contacts as $key => $value) {
                // 見積書がある場合は、見積書で最も多く使われている商品名を取得
                if(!is_null($value->quotation_id)){
                    // dd($value);
                    $large_contacts[$key]['most_product'] = Quotation::getMostProductName($value->quotation_id, $value->quotation_type);
                }
            }
            // dd($large_contacts);

            return view('welcome',
              compact('user', 'stocks', 'visitCustomers', 'drawingCustomers', 'contactShippings', 'contactTotal', 'completeArray', 'avg', 'countYesterday', 'countDayBeforeYesterday', 'contactLastMonthTotal', 'transactionTotal', 'transactionLastMonthTotal', 'transactionYesterday', 'transactionDayBeforeYesterday', 'completeTransactionArray', 'avgTransaction', 'leave_alone_list', 'large_contacts'));
        }

        if (isFC()) {
            //未対応 案件一覧
            $newContacts = Contact::where('user_id', $user->id)->where('status', 1)->where('step_id', self::STEP_APPOINT)->orderby('created_at', 'ASC')->orderby('created_at', 'DESC')->get();

            //図面見積もり依頼リスト
            $drawingCustomers = Contact::where('user_id', $user->id)->where('status', 1)->where('step_id', self::STEP_QUOTATION)->whereIn('contact_type_id', [2, 6])->orderby('created_at', 'DESC')->get();
            //訪問見積もり依頼リスト
            $visitCustomers = Contact::where('user_id', $user->id)->where('status', 1)->where('step_id', self::STEP_APPOINT)->whereIn('contact_type_id', [3, 7])->orderby('created_at', 'DESC')->get();

            //今月の売り上げと順位
            $sales = Transaction::whereBetween('created_at', [$dates['from'], $dates['to']])->select(DB::raw('sum(total) as total'), 'user_id', 'contact_id')->groupBy('user_id')->orderBy('total', 'desc')->get()->toArray();

            $count_sales = count($sales);
            $extracted_contact_ids = array_column($sales, 'contact_id');
            $rankingList = Contact::where('user_id', $user->id)->where('status', 1)->whereIn('id', $extracted_contact_ids)->orderby('created_at', 'DESC')->paginate(5);
            $user_order = array_column($sales, 'user_id');
            //もしログインFCが配列の中にあったら
            $rank = array_search($user->id, $user_order);
            if ($rank === false) {
                $find_fc_order = '';
            } else {
                $find_fc_order = array_search($user->id, $user_order) + 1; //最上位は配列のトップで[0] のため1を足す
            }
            //今月の売上を配列から検索
            foreach ($sales as $s) {
                if ($s['user_id'] == $user->id) {
                    $sales_this_month = $s['total'];
                //dd($sales_this_month);
                } else {
                    $sales_this_month = 0;
                }
            }
            if (empty($sales_this_month)) {
                $sales_this_month = null;
            }
            // 施工数関係のランキング
            $works = Contact::select(DB::raw('count(id) as total'), 'user_id')->whereBetween('updated_at', [$dates['from'], $dates['to']])->where('step_id', self::STEP_REPORT_COMPLETE)->groupBy('user_id')->orderBy('total', 'DESC')->get();
            foreach ($works as $key => $rc) {
                if ($rc['user_id'] == $user->id) {
                    $works['count'] = $rc['total'];
                    $works['rank'] = $key + 1;
                    break;
                }
            }

            $notifications = UserNotification::select('user_notifications.*', 'user_notifications.created_at AS notificate_date', 'c.*', 'nt.name AS type_name')
            ->where('user_notifications.user_id', \Auth::id())
            ->join('notification_types AS nt', 'nt.id', '=', 'user_notifications.notification_type')
            ->join('contacts AS c', 'c.id', '=', 'user_notifications.contact_id')
            ->orderBy('user_notifications.created_at', 'DESC')
            ->take(5);

            //新着未読お知らせ5件
            $articles = Article::where('articles.status', 1)
                // 既読FCののみ排除 サブクエリ遣わないとむりぽ
                ->whereNotIn('articles.id', function($query){
                    $query->select('article_reads.article_id')
                    ->from('article_reads')
                    ->where('user_id', \Auth::id())
                    ->get();
                })
                ->orderBy('articles.published_at', 'DESC')->orderBy('articles.created_at', 'DESC')
                ->limit(5)->groupBy('articles.id')->get();

            return view('welcome', compact('user', 'newContacts', 'drawingCustomers', 'visitCustomers', 'rankingList', 'notifications', 'sales_this_month', 'count_sales', 'works', 'find_fc_order', 'articles'));
        }

        return view('welcome', compact('user'));
    }
}
