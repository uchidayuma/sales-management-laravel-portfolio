<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\SendTransactionDispatchUpdateCustomerMail;
use App\Mail\SendTransactionDispatchUpdateMail;
use App\Mail\SendTransactionUpdateMail;
use App\Mail\SendTransactionUpdateToFcMail;
use App\Models\Contact;
use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\Shipping;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Config;
use App\Models\OfficeHoliday;
use App\Models\Prefecture;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use DB;

class TransactionController extends MyController
{
    public function __construct(Transaction $transaction)
    {
        parent::__construct();
        $this->model = $transaction;
        $this->breadcrumbs->addCrumb('<i class="fas fa-hourglass"></i>発注', 'transactions');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //発注
        $this->breadcrumbs->addCrumb('発注書一覧', '')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        if (isAdmin()) {
            $query = Transaction::select('transactions.*', 'transactions.id AS transaction_id', 'c.*', 'u.name AS fc_name', 'transactions.created_at AS transaction_created_at')
                ->where('transactions.status', 1)
                ->where(function ($query) {
                    // 案件に紐付く発注かつ未発送
                    $query->orWhere(function ($query) {
                        $query->where('c.step_id', '<=', self::STEP_SHIPPING);
                        // $query->where('transactions.shipping_cost', null);
                        $query->where('transactions.transaction_only_shipping_date', null);
                        $query->where('c.shipping_date', null);
                    });
                    // 案件に紐付かない案件かつ未発送
                    $query->orWhere(function ($query) {
                        $query->where('c.step_id', null);
                        $query->where('c.id', null);
                        // $query->where('transactions.shipping_cost', null);
                        $query->where('c.shipping_date', null);
                        $query->where('transactions.transaction_only_shipping_date', null);
                        $query->where('transactions.transaction_only_shipping_number', null);
                        $query->where('transactions.transaction_only_shipping_id', null);
                    });
                    // 全額前金発注書は送料あっても出す → 全額前金かつ、未発送
                    $query->orWhere(function ($query) {
                        $query->where('transactions.prepaid', 2);
                        $query->where('transactions.transaction_only_shipping_date', null);
                        $query->where('transactions.transaction_only_shipping_number', null);
                        $query->where('c.shipping_date', null);
                        $query->where('c.shipping_number', null);
                    });
                    // 分納対応
                    $query->orWhere(function ($query) {
                        $query->whereNotNull('transactions.delivery_at2');
                        $query->whereNull('transactions.shipping_date2');
                    });
                    $query->orWhere(function ($query) {
                        $query->whereNotNull('transactions.delivery_at3');
                        $query->whereNull('transactions.shipping_date3');
                    });
                })
                ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
                ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id');
        } else {
            $query = Transaction::select('transactions.*', 'transactions.id AS transaction_id', 'c.*', 'u.name AS fc_name', 'transactions.created_at AS transaction_created_at')
                ->where('transactions.user_id', \Auth::id())->where('transactions.status', 1)
                ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
                ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id');
        }
        $orderType = $request->input('orderType');

        switch ($orderType) {
            case 'ascendingOrderTime':
                // 発注日時
                $query->orderBy('transactions.created_at', 'asc');
                break;
            case 'descendingOrderTime':
                // 発注日時
                $query->orderBy('transactions.created_at', 'desc');
                break;
            case 'ascendingDeliveryPreferredDate':
                // 納品希望日昇順
                $query->orderBy('transactions.delivery_at', 'asc');
                break;
            case 'descendingDeliveryPreferredDate':
                // 納品希望日降順
                $query->orderBy('transactions.delivery_at', 'desc');
                break;
            default:
                $query->orderBy('transactions.created_at', 'desc');
        }

        $transactions = $query->get();

        return view('share.transaction.index', compact('transactions', 'breadcrumbs', 'orderType'));
    }

    /**
     * 本部用発送後の発注請書一覧.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminDispatchedIndex(Request $request)
    {
        adminOnly();
        $this->breadcrumbs->addCrumb('発注書請一覧', '');
        $breadcrumbs = $this->breadcrumbs;

        $query = Transaction::select(
            'transactions.*',
            'transactions.id AS transaction_id',
            'c.*',
            'u.name AS fc_name',
            'transactions.created_at AS transaction_created_at',
            'transactions.status AS transactions_status',
            \DB::raw('(CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date END) AS shipping_date')
        )
            ->where(function ($query) {
                // 案件に紐付く発注かつ発送済み
                $query->orWhere(function ($query) {
                    // $query->where('transactions.shipping_cost', '!=', null);
                    $query->whereNotNull('transactions.transaction_only_shipping_date');
                    $query->whereNotNull('c.shipping_date');
                    $query->whereNotNull('c.user_id');
                });
                // 案件に紐付かない案件かつ発送済み
                $query->orWhere(function ($query) {
                    $query->orWhereNotNull('transactions.transaction_only_shipping_date')->orWhereNotNull('c.shipping_date');
                });
            })
            ->where('transactions.status', 1)
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id');

        $orderType = $request->input('orderType');

        switch ($orderType) {
            case 'ascendingDeliveryDate':
                // 発送日昇順
                $query->orderBy('shipping_date', 'asc');
                break;
            case 'descendingDeliveryDate':
                // 発送日降順
                $query->orderBy('shipping_date', 'desc');
                break;
            case 'ascendingDeliveryPreferredDate':
                // 納品希望日昇順
                $query->orderBy('delivery_at', 'asc');
                break;
            case 'descendingDeliveryPreferredDate':
                // 納品希望日降順
                $query->orderBy('delivery_at', 'desc');
                break;
            default:
                $query->orderBy('shipping_date', 'desc');
        }

        $transactions = $query->paginate(200);

        return view('admin.transaction.dispatched-index', compact('transactions', 'breadcrumbs', 'orderType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($contactId = null)
    {
        // もし結果入力をしていなかったらリダイレクト
        if (!empty($contactId)) {
            $contact = Contact::find($contactId);
            if (empty($contact->step_id)) {
                return redirect(route('transaction.pending.list'))->with(['danger' => '対応する案件が存在しません。']);
            }
            if ($contact->step_id == 5) {
                return redirect(route('pending.list'))->with(['danger' => '発注書を作成する前に顧客商談結果を入力してください。']);
            }
            if (is_null($contact->quotation_id)) {
                return redirect(route('contact.show', ['id' => $contactId]))->with(['danger' => '発注書を作成する前に採用された見積書を確定してください。']);
            }
        }
        //発注
        $this->breadcrumbs->addCrumb('発注書作成', 'create/order')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $products = Product::where('status', 1)->orderBy('order_no', 'ASC')->get()->toArray();
        $free_shipping_item_ids = Config::where('key', 'free_shipping_product_id')->value('value');

        $turfs = array_filter($products, 'isTurf', ARRAY_FILTER_USE_BOTH);
        $subItems = array_filter($products, 'isSub', ARRAY_FILTER_USE_BOTH);
        $salesItems = array_filter($products, 'isSales', ARRAY_FILTER_USE_BOTH);
        $cutItems = array_filter($products, 'isCut', ARRAY_FILTER_USE_BOTH);

        if ($contactId) {
            $contact = Contact::select('u.*', 'u.pref AS fc_pref', 'u.city AS fc_city', 'u.street AS fc_street', 'u.zipcode AS fc_zipcode', 'contacts.*')->where('contacts.id', $contactId)
                ->join('users AS u', 'u.id', '=', 'contacts.user_id')->first();
            $registration = true;
        } else {
            $contact = User::select('users.name', 'users.pref', 'users.city', 'users.street', 'users.id AS user_id', 'users.pref AS fc_pref', 'users.city AS fc_city', 'users.street AS fc_street', 'users.zipcode AS fc_zipcode')->where('id', \Auth::id())->first();
            $registration = false;
        }

        $office_holiday = OfficeHoliday::whereYear('holiday', '>=', Carbon::now()->format('Y'))->orderBy('holiday', 'ASC')->get();

        $samplesData = Product::select('id')->where('name', 'LIKE', '%サンプル%')->orderBy('id', 'ASC')->get();
        $samplesArr = [];
        foreach ($samplesData as $s) {
            array_push($samplesArr, $s['id']);
        }

        // 発注書が同じ案件IDで何枚あるのかをカウント（通常が "1"）
        $transaction_count = transactionCount($contactId);
        // サブ発注書が作成できる制限数はcofig テーブルないに格納されてれている値から参照
        $limit_create_transaction = Config::where('key', 'transaction_sub_limit')->value('value');
        // サブ発注書が作成できる上限に達した場合メッセージを表示
        if ($transaction_count > $limit_create_transaction) {
            return redirect(route('report.pending'))->with('danger', '追加発注出来る上限に達しました。');
        }
        // 人工芝切り売り時に GOLF以外では出さない商品ID
        $cut_turf_invisible_ids = Config::where('key', 'transaction_cut_turf_invisible_ids')->value('value');

        // 見積もり作成済の案件一覧
        if (!$contactId) {
            $quotation_contacts = Contact::select('contacts.*')->where('contacts.user_id', \Auth::id())
                ->whereBetween('contacts.step_id', [self::STEP_RESULT, self::STEP_TRANSACTION])
                ->where('contacts.status', 1)->whereNotNull('quotation_id')
                // ->where('transaction_count', '<', $limit_create_transaction)
                ->leftJoin('transactions as t', 'contacts.id', '=', 't.contact_id')
                ->orderBy('contacts.created_at', 'DESC')->get();
        } else {
            $quotation_contacts = [];
        }
        // 送料自動計算用データ
        $shipping_price_table = Prefecture::select('r.*', 'r.id AS region_id', 'r.name AS region_name', 'prefectures.*')
            ->join('regions AS r', 'prefectures.region_id', '=', 'r.id')
            ->get();

        return view('fc.transaction.new', compact('products', 'breadcrumbs', 'turfs', 'subItems', 'salesItems', 'cutItems', 'contact', 'office_holiday', 'registration', 'samplesArr', 'transaction_count', 'cut_turf_invisible_ids', 'quotation_contacts', 'shipping_price_table', 'free_shipping_item_ids'));
    }

    /**
     * confirm a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function comfirm(Request $request)
    {
        $this->breadcrumbs->addCrumb('発注確認', 'order/comfirm');
        $breadcrumbs = $this->breadcrumbs;

        $products = Product::where('status', 1)->get()->toArray();

        $posts = $request->all();
        // dd($posts);
        if (empty($posts['pt'][0])) {
            if (!empty($posts['id'])) {
                return redirect(route('create.order', ['id' => $posts['id']]))->with(['danger' => '発注書には最低1つ以上の商品を追加してください。']);
            } else {
                return redirect(route('create.order'))->with(['danger' => '発注書には最低1つ以上の商品を追加してください。']);
            }
        }
        $posts['t']['sub_total'] = intval($this->model->calcSubTotal($posts['pt']));
        $server_total = intval($posts['t']['sub_total'] * config('app.tax_rate'));
        // totalは税込み金額
        $posts['t']['total'] = $server_total;
        // $tax = $server_total * (config('app.tax_rate'));
        $tax = floor(($posts['t']['sub_total'] + $posts['t']['shipping_cost']) * config('app.tax_rate'));

        //セット数指定の行はDBに追加しない
        $set_num = [];
        // 商品行をループしてset数を各parent_idが一致した商品に追加する
        foreach ($posts['pt'] as $pkey => $rc) {
            if (isset($rc['set_num'])) {
                foreach ($posts['pt'] as $key => $crc) {
                    if ($posts['pt'][$key]['row_id'] == $rc['parent_id']) {
                        $posts['pt'][$key]['cut_set_num'] = $rc['set_num'];
                    }
                    if (!empty($posts['pt'][$key]['parent_id'])) {
                        if ($posts['pt'][$key]['parent_id'] == $rc['parent_id']) {
                            $posts['pt'][$key]['cut_set_num'] = $rc['set_num'];
                        }
                    }
                }
                unset($posts['pt'][$pkey]);
                array_values($posts['pt']);
            }
        }

        $contact = !empty($posts['contact_id']) ? Contact::find($posts['contact_id']) : null;

        //理論上の最短納品日を取得
        $date = $this->model->getDeliveryDate($posts['transport_state']);
        $delivery_date = $date->format('Y-m-d');
        $post_date = new Carbon($posts['t']['delivery_at']);

        $delivery_flag = $delivery_date < $post_date ? true : false;

        return view('fc.transaction.comfirm', compact('posts', 'tax', 'products', 'breadcrumbs', 'contact', 'delivery_flag'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $posts = $request->all();
        $this->model->transStart();
        try {
            $posts['t']['user_id'] = \Auth::id();
            // DBに税抜き小計を入れたいので、値を入れ替え
            $posts['t']['total'] = $posts['t']['sub_total'];
            Arr::forget($posts['t'], 'sub_total');
            $posts['t']['address'] = !empty($posts['t']['address']) ?  $posts['t']['address'] : "配送先住所要確認（開発者までお知らせください）";
            $transaction_id = $this->model->insertGetId($posts['t']);

            foreach ($posts['pt'] as $key => $value) {
                $posts['pt'][$key]['transaction_id'] = $transaction_id;
                $posts['pt'][$key]['turf_cuts'] = [];
                if (!empty($posts['cut'])) {
                    foreach ($posts['cut'] as $cut) {
                        if ($value['row_id'] == $cut['parent_id']) {
                            Arr::forget($cut, 'parent_id');
                            array_push($posts['pt'][$key]['turf_cuts'], $cut);
                        }
                    }
                }
                $posts['pt'][$key]['turf_cuts'] = json_encode($posts['pt'][$key]['turf_cuts']);
                if (!empty($posts['pt'][$key]['product_id'])) {
                    if (isCutSubitem($posts['pt'][$key]['product_id'], $posts['pt'][$key]['unit'])) {
                        $posts['pt'][$key]['cut'] = 1;
                    }
                }
                // その他商品がproductテーブルにあれば紐付ける
                if (!empty($value['other_product_name'])) {
                    $value['other_product_name'] = mb_convert_kana($value['other_product_name'], 'rnas');
                    $product = Product::where('name', $value['other_product_name'])->first();
                    if (!empty($product['id'])) {
                        $posts['pt'][$key]['product_id'] = $product['id'];
                    }
                }
                Arr::forget($posts['pt'][$key], 'row_id');
                ProductTransaction::insert($posts['pt'][$key]);
                // 商品数の更新
                if (!empty($value['product_id']) && $value['unit'] === '反' && $value['cut'] == '0') {
                    Product::where('id', $value['product_id'])->decrement('stock', $value['num']);
                } elseif (!empty($value['product_id']) && $value['unit'] === '反' && $value['cut'] == '1' && $value['vertical'] > 3) {
                    Product::where('id', $value['product_id'])->decrement('stock');
                }
            }
            Contact::where('id', $posts['t']['contact_id'])->update(['step_id' => self::STEP_SHIPPING]);
            $this->model->transCommit();

            return redirect(route(!empty($posts['contact_id']) ? 'report.pending' : 'dashboard'))->with(['success' => '発注書No.' . $transaction_id . 'として発注が確定されました。本部からの発送連絡をお待ちください。']);
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }
    }

    public function skip(Request $request)
    {
        $posts = $request->all();
        Contact::where('id', $posts['contact_id'])->update(['step_id' => self::STEP_COMPLETE]);

        return redirect(route('report.pending'))->with(['success' => '発注をスキップしました。現場施工を行い、完了報告をしてください。']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //発注
        $this->breadcrumbs->addCrumb('発注書確認', '');
        $breadcrumbs = $this->breadcrumbs;

        $transactions = $this->model->preview($id);
        // dd($transactions);
        if (empty($transactions[0])) {
            return redirect(route('transactions'))->with('warning', 'この発注書は削除されたようです・・・復旧希望の場合は開発者にご連絡ください。');
        }
        if ($transactions[0]['shipping_id']) {
            $current_company = Shipping::where('id', $transactions[0]['shipping_id'])->get('transport_company')->toArray();
        } else {
            $current_company = null;
        }
        $cancelAble = $this->model->cancelAble($transactions[0]['transaction_time']);
        $products = Product::orderBy('id', 'ASC')->get()->toArray();
        $deliveries = Shipping::where('status', 1)->get();

        $transaction_id = $id;

        $transactions_table = Transaction::where('id', $id)->first();

        // FCの場合FCの住所を持ってくる。
        $userdata = User::select('users.*', 'transactions.user_id')
            ->leftJoin('transactions', 'transactions.user_id', '=', 'users.id')
            ->where('transactions.id', $id)
            ->get();

        // 発注書に表示する金額を計算（閲覧するユーザーロールとキャンセル可能かどうかで分岐）
        list($sub_total, $tax, $total) = $this->model->returnShowPrice($transactions[0]);

        $is_cancelable = $this->model->isCancelable($transactions[0]['created_at']);

        // ====>>>>> TODO開発終わったら消す <<<<<<=====
        // $cancelAble = false;

        return view('share.transaction.show', compact('transactions', 'id', 'transaction_id', 'sub_total', 'tax', 'total', 'products', 'deliveries', 'breadcrumbs', 'transactions_table', 'userdata', 'current_company', 'cancelAble'), compact('is_cancelable'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($transactionId)
    {
        //発注
        $this->breadcrumbs->addCrumb('発注書修正', '');
        $breadcrumbs = $this->breadcrumbs;

        $transactions = $this->model->preview($transactionId);
        if (empty($transactions[0]) || $transactions[0]['status'] == 2) {
            return redirect()->route('transactions')->with('warning', 'この発注書はキャンセルもしくは、削除されました');
        }
        $product_transactions = [];
        $product_transactions['turf'] = $this->model->filterProductTypeId($transactions, 1, 0);
        $product_transactions['cut_turf'] = $this->model->filterProductTypeId($transactions, 1, 1);
        $product_transactions['sub'] = $this->model->filterProductTypeId($transactions, 2, 0);
        $product_transactions['cut_sub'] = $this->model->filterProductTypeId($transactions, 2, 1);
        $product_transactions['sales'] = $this->model->filterProductTypeId($transactions, 3, 0);
        $product_transactions['other'] = $this->model->filterProductTypeId($transactions, null, 0);
        // 編集は当日中
        $dt = new Carbon($transactions[0]['transaction_time']);
        $now = new Carbon();
        $changeAble = $dt->toDateString() == $now->toDateString();
        if (!$changeAble && isFc()) {
            return redirect('/transactions')->with(['danger' => '変更可能期限を過ぎたため、発注内容の変更はお受けできません。何卒ご了承ください。']);
        }
        // キャンセルは翌日11時まで
        $cancelAble = $this->model->cancelAble($transactions[0]['transaction_time']);
        if (!$cancelAble && isFc()) {
            return redirect('/transactions')->with(['danger' => 'キャンセル可能期限を過ぎたため、キャンセルはお受けできません。何卒ご了承ください。']);
        }
        $contact = Contact::select('u.*', 'contacts.*')
            ->where('contacts.id', $transactions[0]['contact_id'])
            ->join('users AS u', 'u.id', '=', 'contacts.user_id')
            ->first();

        // 商品一覧に使う配列
        $products = Product::where('status', 1)->get()->toArray();
        $turfs = array_filter($products, 'isTurf', ARRAY_FILTER_USE_BOTH);
        $subItems = array_filter($products, 'isSub', ARRAY_FILTER_USE_BOTH);
        $salesItems = array_filter($products, 'isSales', ARRAY_FILTER_USE_BOTH);
        $cutItems = array_filter($products, 'isCut', ARRAY_FILTER_USE_BOTH);

        $products = Product::where('status', 1)->get();
        $free_shipping_item_ids = Config::where('key', 'free_shipping_product_id')->value('value');
        $editflg = true;

        $office_holiday = OfficeHoliday::whereYear('holiday', '>=', Carbon::now()->format('Y'))->orderBy('holiday', 'ASC')->get();
        $registration = !is_null($contact) ? true : false;

        $samplesData = Product::select('id')->where('name', 'LIKE', '%サンプル%')->orderBy('id', 'ASC')->get();
        $samplesArr = [];
        foreach ($samplesData as $s) {
            array_push($samplesArr, $s['id']);
        }
        // 編集時はtransaction_countを0に
        $transaction_count = 0;
        // 送料自動計算用データ
        $shipping_price_table = Prefecture::select('r.*', 'r.id AS region_id', 'r.name AS region_name', 'prefectures.*')
            ->join('regions AS r', 'prefectures.region_id', '=', 'r.id')
            ->get();

        // 人工芝切り売り時に GOLF以外では出さない商品ID
        $cut_turf_invisible_ids = Config::where('key', 'transaction_cut_turf_invisible_ids')->value('value');
        return view('fc.transaction.new', compact('editflg', 'transactions', 'transactionId', 'product_transactions', 'products', 'turfs', 'subItems', 'salesItems', 'cutItems', 'contact', 'breadcrumbs', 'office_holiday', 'cancelAble', 'changeAble', 'registration', 'samplesArr', 'transaction_count', 'cut_turf_invisible_ids', 'free_shipping_item_ids', 'shipping_price_table'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $posts = $request->all();
        $this->model->transStart();
        try {
            // direct_shipping が存在しない場合 direct_shippingに0を代入(チェックボックスに何も入っていないと direct_shipping 自体がpostされない為)
            // dd(empty($posts['t']['direct_shipping']) && $posts['t']['address_type'] != '4');
            $posts['t']['direct_shipping'] = $posts['t']['address_type'] == '4' ? 1 : 0;
            // DBに税抜き小計を入れたいので、値を入れ替え
            $posts['t']['total'] = $posts['t']['sub_total'];
            Arr::forget($posts['t'], 'sub_total');
            $this->model->where('id', $id)->update($posts['t']);
            ProductTransaction::where('transaction_id', $id)->delete();
            //セット数指定の行はDBに追加しない
            $set_num = [];
            // 商品行をループしてset数を各parent_idが一致した商品に追加する
            foreach ($posts['pt'] as $pkey => $rc) {
                if (isset($rc['set_num'])) {
                    foreach ($posts['pt'] as $key => $crc) {
                        if ($posts['pt'][$key]['row_id'] == $rc['parent_id']) {
                            $posts['pt'][$key]['cut_set_num'] = $rc['set_num'];
                        }
                        if (!empty($posts['pt'][$key]['parent_id'])) {
                            if ($posts['pt'][$key]['parent_id'] == $rc['parent_id']) {
                                $posts['pt'][$key]['cut_set_num'] = $rc['set_num'];
                            }
                        }
                    }
                    unset($posts['pt'][$pkey]);
                    array_values($posts['pt']);
                }
            }
            // カットメニューにもcut_set_numを入れる
            if (!empty($posts['cut'])) {
                foreach ($posts['cut'] as $key => $rc) {
                    $parent_array = myArrayFilter($posts['pt'], 'row_id', $rc['parent_id']);
                    $posts['cut'][$key]['cut_set_num'] = !empty($parent_array['cut_set_num']) ? $parent_array['cut_set_num'] : 1;
                }
            }

            foreach ($posts['pt'] as $key => $value) {
                // 副資材バラ売りなら1にする
                if (!empty($value['is_cut']) && empty($value['parent_id'])) {
                    if ($value['is_cut'] == 1) {
                        $posts['pt'][$key]['cut'] = 1;
                    }
                }
                $posts['pt'][$key]['transaction_id'] = $id;
                $posts['pt'][$key]['turf_cuts'] = [];
                // カット人工芝に付属するカットメニュー
                if (!empty($posts['cut'])) {
                    foreach ($posts['cut'] as $cut) {
                        if ($value['row_id'] === $cut['parent_id']) {
                            Arr::forget($cut, 'parent_id');
                            array_push($posts['pt'][$key]['turf_cuts'], $cut);
                        }
                    }
                }
                $posts['pt'][$key]['turf_cuts'] = json_encode($posts['pt'][$key]['turf_cuts']);
                if (isset($value['other_product_name'])) {
                    $posts['pt'][$key]['other_product_price'] = $value['unit_price'];
                    $posts['pt'][$key]['unit_price'] = $value['unit_price'];
                } else {
                    // 自由記述商品でなければ、その時の単価をproduct_transactions.unit_priceに入れる
                    $posts['pt'][$key]['unit_price'] = $value['unit_price'];
                }
                // カット人工芝の処理
                if (!empty($value['horizontal']) && !empty($value['vertical'])) {
                    $posts['pt'][$key]['num'] = $value['area'];
                    $posts['pt'][$key]['cut'] = 1;
                }
                if (!empty($posts['pt'][$key]['product_id']) && !empty($posts['pt'][$key]['unit'])) {
                    if (isCutSubitem($posts['pt'][$key]['product_id'], $posts['pt'][$key]['unit'])) {
                        $posts['pt'][$key]['cut'] = 1;
                    }
                }
                // 人工芝に付随するカットメニューはinsertしない
                if (empty($value['parent_id'])) {
                    Arr::forget($posts['pt'][$key], 'row_id');
                    Arr::forget($posts['pt'][$key], 'parent_id');
                    Arr::forget($posts['pt'][$key], 'is_cut');
                    Arr::forget($posts['pt'][$key], 'area');
                    Arr::forget($posts['pt'][$key], 'price');
                    ProductTransaction::insert($posts['pt'][$key]);
                }
            } //foreach

            $this->model->transCommit();
            //メール用のデータ
            $posts['t']['id'] = $id;
            $posts['user'] = User::find($posts['t']['user_id'])->toArray();
            // 本部が変更した場合のみ、FCにメールを送信
            if (isAdmin() && isAllowEmailUser($posts['user']['id']) && !\App::environment('circleci')) {
                Mail::to($posts['user']['email'])->send(new SendTransactionUpdateToFcMail($posts));
                if (!empty($posts['user']['email2'])) {
                    Mail::to($posts['user']['email2'])->send(new SendTransactionUpdateToFcMail($posts));
                }
                if (!empty($posts['user']['email3'])) {
                    Mail::to($posts['user']['email3'])->send(new SendTransactionUpdateToFcMail($posts));
                }
            }
            if (!\App::environment('circleci')) {
                $email = 'turf@shintou-s.jp';
                Mail::to($email)->send(new SendTransactionUpdateMail($posts));
            }

            return redirect("/transactions/$id")->with(['success' => '発注書を更新しました。']);
        } catch (\Throwable $th) {
            \Log::debug(print_r($th->getMessage(), true));
            $this->model->transRollback();
            return redirect('/transactions')->with(['warning' => '発注書更新に失敗しました。']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $transaction_id = $request->input('transaction_id');
        $transaction = Transaction::find($transaction_id);
        Transaction::where('id', $transaction_id)->update(['status' => 2]);

        // もし発注書が1枚もなければSTEPを戻す
        if (!$this->model->where('contact_id', $transaction->contact_id)->where('status', 1)->exists()) {
            Contact::where('id', $transaction->contact_id)->update(['step_id' => self::STEP_TRANSACTION]);
        }

        transactionDestroyToSlack(\Auth::user(), $transaction_id, "発注書のみ直接削除");

        return redirect(route('dashboard'))->with('success', '発注書を削除しました。');
    }

    public function delete($id)
    {
        $transaction = $this->model->where('id', $id)->first();
        $today = Carbon::now();
        $createDate = Carbon::parse($transaction['created_at']);

        //キャンセル期限の判別
        $isFuture = $today->gte($createDate);
        $dateDiff = $today->diffInDays($createDate);
        $time = $today->hour;
        if ($isFuture && $dateDiff >= 1 && $time > 10) {
            return redirect('/transactions')->with(['danger' => '変更可能期限を過ぎたため、発注内容の変更ならびにキャンセルはお受けできません。何卒ご了承ください。']);
        }

        $this->model->where('id', $id)->update(['status' => 2]);
        // もし発注書が1枚もなければSTEPを戻す
        $transaction = Transaction::find($id);
        if (!$this->model->where('contact_id', $transaction->contact_id)->where('status', 1)->exists()) {
            Contact::where('id', $transaction->contact_id)->update(['step_id' => self::STEP_TRANSACTION]);
        }
        transactionDestroyToSlack(\Auth::user(), $id, "発注書のみ直接削除");

        return redirect(route('dashboard'))->with('success', '発注をキャンセルしました');
    }

    public function dispatchPending()
    {
        // アクセスできるのは本部だけ
        adminOnly();
        $this->breadcrumbs->addCrumb('FC発送待ち案件一覧', '');
        $breadcrumbs = $this->breadcrumbs;

        $contacts = Contact::select('contacts.*', 'contacts.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 'u.pref AS user_pref', 'u.city AS user_city', 'u.street AS user_street', 't.id AS transaction_id', 't.address', 't.consignee', 't.created_at AS transaction_created_at', 't.delivery_at', 't.direct_shipping')
            ->where('contacts.status', 1)
            ->whereIn('step_id', [self::STEP_SHIPPING, self::STEP_COMPLETE])
            ->where('t.status', 1)
            ->where('t.direct_shipping', 0)
            ->leftJoin('users AS u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('transactions AS t', 't.contact_id', '=', 'contacts.id')
            ->where(function ($query) {
                // 通常案件
                $query->orWhere(function ($query) {
                    $query->whereNull('t.transaction_only_shipping_date')
                        ->whereNull('contacts.shipping_date');
                    // ->whereNull('t.shipping_cost');
                });
                // 案件に紐づくかつ、全額前金の送料入力済み
                $query->orWhere(function ($query) {
                    $query->whereNotNull('contacts.id')
                        //    ->whereNotNull('t.shipping_cost')
                        ->where('t.prepaid', 2)
                        ->whereNull('contacts.shipping_date')
                        ->whereNull('t.transaction_only_shipping_date');
                });
                // 第2納品日や第3納品希望日が残っている案件
                $query->orWhere(function ($query) {
                    $query->whereNull('t.shipping_date2')->whereNotNull('t.delivery_at2');
                });
                $query->orWhere(function ($query) {
                    $query->whereNull('t.shipping_date3')->whereNotNull('t.delivery_at3');
                });
            })
            ->get()->toArray();

        $transactions = Transaction::select('c.*', 'c.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 'transactions.id AS transaction_id', 'transactions.address', 'transactions.consignee', 'transactions.created_at AS transaction_created_at', 'transactions.delivery_at')
            ->where('transactions.status', 1)
            ->where('transactions.contact_id', null)
            ->where('transactions.transaction_only_shipping_date', null)
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->get()->toArray();

        // 本部が見積もりして、商談成立した顧客
        $customers = Contact::where('contacts.status', 1)
            ->where('step_id', self::STEP_SHIPPING)
            ->where('contacts.user_id', 1)
            ->where('q.status', 1)
            ->join('quotations AS q', 'contacts.quotation_id', '=', 'q.id')
            ->get();

        // FCが見積もりして、発注書を作成した顧客(ダイレクト発送)
        $customersFc = Contact::select('contacts.*', 't.delivery_at', 't.delivery_at2', 't.delivery_at3', 't.id AS transaction_id', 't.created_at AS transaction_created_at')
            ->where('contacts.status', 1)
            // ->where('step_id', self::STEP_SHIPPING)
            ->where('t.direct_shipping', 1)
            ->where('t.status', 1)
            ->where(function ($query) {
                $query->orWhere(function ($query) {
                    $query->whereNull('t.transaction_only_shipping_date');
                    $query->whereNull('contacts.shipping_date');
                    // $query->whereNull('t.shipping_cost');
                });
                // 第2納品日や第3納品希望日が残っている案件
                $query->orWhere(function ($query) {
                    $query->whereNull('t.shipping_date2')->whereNotNull('t.delivery_at2');
                });
                $query->orWhere(function ($query) {
                    $query->whereNull('t.shipping_date3')->whereNotNull('t.delivery_at3');
                });
            })
            ->join('transactions AS t', 't.contact_id', '=', 'contacts.id')
            ->get();

        //dd($contacts);
        $contacts = array_merge($contacts, $transactions);
        $deliveries = Shipping::where('status', 1)->get();

        return view('admin.transaction.dispatch-pending', compact('contacts', 'deliveries', 'breadcrumbs', 'customers', 'customersFc'));
    }

    // 全額前金発注の送料のみ確定
    public function shippingCostUpdate(Request $request)
    {
        adminOnly();
        $posts = $request->all();
        $this->model->where('id', $posts['transaction_id'])->update(['shipping_cost' => $posts['shipping_cost']]);

        return redirect(route('transactions.show', ['id' => $posts['transaction_id']]))->with('success', '送料を確定しました！');
    }

    public function dispatchStore(Request $request)
    {
        adminOnly();
        $posts = $request->all();
        // dd($posts);
        $posts['shipping_number'] = mb_convert_kana($posts['shipping_number'], 'n');
        // 記号を半角カンマに統一
        $posts['shipping_number'] = str_replace(['，', '、', '､'], ',', $posts['shipping_number']);

        $this->model->transStart();
        try {
            $this->model->dispatch($posts);
            $this->model->dispatchSendEmail($posts);
            $this->model->transCommit();
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }
        //dd($posts);
        //顧客への発送の場合
        if (empty($posts['transaction_id']) || $posts['direct_shipping'] == '1') {
            $message = '顧客に商品発送連絡を行いました';
        } else {
            $message = 'FCに部材発注連絡を行いました';
        }

        if (\App::environment('circleci') || empty($posts['transaction_id'])) {
            return redirect(route('dashboard'))->with('success', $message);
        } else {
            return redirect(route('dashboard', ['redirecturl' => route('transactions.download', ['id' => $posts['transaction_id']])]))->with('success', $message);
        }
    }

    public function shippingUpdate(Request $request)
    {
        adminOnly();
        $posts = $request->all();
        //dd($posts);
        $posts['shipping_number'] = mb_convert_kana($posts['shipping_number'], 'n');
        // 記号を半角カンマに統一
        $posts['shipping_number'] = str_replace(['，', '、', '､'], ',', $posts['shipping_number']);
        if (empty($posts['direct_shipping'])) {
            $posts['direct_shipping'] = 0;
        }
        $this->model->transStart();
        try {
            $this->model->dispatch($posts);

            // 本部が発送した場合の処理
            if (empty($posts['transaction_id']) && !\App::environment('circleci')) {
                $customer = Contact::select('contacts.*', 's.transport_company', 's.trakking_url')->where('contacts.id', $posts['contact_id'])->join('shippings AS s', 'contacts.shipping_id', '=', 's.id')->first();
                $numbers = explode(',', $customer['shipping_number']);
                $url = $customer['trakking_url'];
                if ($posts['shipping_id'] == '1') {
                    foreach ($numbers as $key => $value) {
                        $url .= '&GNPNO' . intval($key + 1) . '=' . $value;
                    }
                } else {
                    $url .= $posts['shipping_number'];
                }
                $customer['url'] = $url;
                Mail::to($customer['email'])->send(new SendTransactionDispatchUpdateCustomerMail($customer));

                return redirect(route('dashboard'))->with('success', '発送情報を更新してFCに連絡を行いました');
            }
            // 本部が発送した場合の処理ここまで
            $transaction = $this->model->shippingMail($posts);
            $transaction['number'] = !empty($transaction['delivery_at2']) ? $posts['number'] : 1;
            // 分納対応 $post['number'] === 2ならdispatch_message2を使う, 3も同様
            if ($posts['number'] == 2) {
                $transaction['dispatch_message'] = $transaction['dispatch_message2'];
            } elseif ($posts['number'] == 3) {
                $transaction['dispatch_message'] = $transaction['dispatch_message3'];
            }
            $fc = User::where('id', $posts['userid'])->first();

            // [送料更新]更新完了の通知をFCへ送る機能を追加
            if (isAllowEmailUser($posts['userid'])) {
                Mail::to($fc['email'])->send(new SendTransactionDispatchUpdateMail($transaction));
                $email = 'bcc@shintou-s.jp';
                Mail::to($email)->send(new SendTransactionDispatchUpdateMail($transaction));
                if (!empty($fc['email2'])) {
                    Mail::to($fc['email2'])->send(new SendTransactionDispatchUpdateMail($transaction));
                }
                if (!empty($fc['email3'])) {
                    Mail::to($fc['email3'])->send(new SendTransactionDispatchUpdateMail($transaction));
                }
            }
            $this->model->transCommit();
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }

        return redirect(route('dashboard'))->with('success', '発送情報を更新してFCに連絡を行いました');
    }

    public function dispatched()
    {
        $this->breadcrumbs->addCrumb('発送連絡一覧', '');
        $breadcrumbs = $this->breadcrumbs;
        $query = $this->model->query();
        $query->select(
            'transactions.*',
            'transactions.id AS transaction_id',
            'transactions.created_at AS transaction_created_at',
            'c.*',
            'c.id AS contact_id',
            's.trakking_url',
            's.transport_company',
            'u.id AS fc_id',
            'u.name AS fc_name',
            's2.trakking_url AS trakking_url2',
            's2.transport_company AS transport_company2',
            's3.trakking_url AS trakking_url3',
            's3.transport_company AS transport_company3',
            \DB::raw('(CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date END) AS shipping_date'),
            \DB::raw('(CASE WHEN transactions.transaction_only_shipping_number IS NULL THEN c.shipping_number ELSE transactions.transaction_only_shipping_number END) AS shipping_number'),
            \DB::raw('(CASE WHEN transactions.transaction_only_shipping_id IS NULL THEN c.shipping_id ELSE transactions.transaction_only_shipping_id END) AS shipping_id')
        )
            // ->orWhere('c.step_id', '>=', self::STEP_COMPLETE)
            // 案件番号の存在しない案件
            ->where(function ($query) {
                $query->whereNotNull('transactions.transaction_only_shipping_date');
                if (isFc()) {
                    $query->where('transactions.user_id', \Auth::id());
                }
            })
            ->orWhere(function ($query) {
                $query->where('transactions.user_id', \Auth::id())
                    ->where('transactions.contact_id', null)
                    // ->whereNotNull('transactions.shipping_cost')
                    ->whereNotNull('transactions.transaction_only_shipping_date');
            })
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->leftJoin('shippings AS s', 's.id', '=', 'transactions.transaction_only_shipping_id')
            ->leftJoin('shippings AS s2', 's2.id', '=', 'transactions.shipping_id2')
            ->leftJoin('shippings AS s3', 's3.id', '=', 'transactions.shipping_id3')
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->orderBy('shipping_date', 'DESC');
        $dispatches = $query->paginate(50);

        // dd($dispatches);

        return view('share.transaction.dispatched', compact('dispatches', 'breadcrumbs'));
    }

    public function download($id)
    {
        $pdf = app('dompdf.wrapper');

        $transactions = $this->model->preview($id);
        // 請求書に表示する金額を計算
        list($sub_total, $tax, $total_price) = $this->model->returnShowPrice($transactions[0]);
        $products = Product::orderBy('id', 'ASC')->get()->toArray();
        // FCの場合FCの住所を持ってくる。
        $userdata = User::select('users.*', 'transactions.user_id')
            ->leftJoin('transactions', 'transactions.user_id', '=', 'users.id')
            ->where('transactions.id', $id)
            ->get();

        // カット陳の行は別にカウント
        if (!$transactions[0])  \App::abort(404);
        // foreach ($transactions as $q) {
        //     if ($q['cut'] == 1) {
        //         foreach ($q['turf_cuts'] as $cut) {
        //             if ($cut['unit'] == 'm') {
        //                 $cut_set_num = !is_null($q['cut_set_num']) ? $q['cut_set_num'] : 1;
        //                 $total = $total + floatval($cut['num']) * intval($cut['unit_price'] * $cut_set_num);
        //                 $total_length = $total_length + floatval($cut['num']) * $cut_set_num;
        //             }
        //         }
        //     }
        // }
        if (!$transactions[0])  \App::abort(404);
        // 設定ファイルではなく、コントローラー内で設定しないといけないので注意！
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true, 'setMargin' => '5mm', 'isRemoteEnabled' => true, 'enable_font_subsetting' => true])->loadView('share.transaction.pdf', ['transactions' => $transactions, 'userdata' => $userdata, 'sub_total' => $sub_total, 'tax' => $tax, 'total_price' => $total_price, 'products' => $products, 'id' => $id]);

        return $pdf->stream('発注書No' . $id . '.pdf');
    }


    public function refreshToken()
    {
        $refresh = User::find(1, ['freee_refresh_token']);
        $result = $this->model->refreshFreeeToken($refresh->freee_refresh_token);
    }

    //Todo: 6/15
    //Freeeエラー6/12: 行が2つ以上の場合は金額を0円にすることはできません。
    //金額がなにをさすのか??ここで「行が2つ以上」とは、$bodyのinvoice_contentsのことで間違いないか
    //金額が$totalや$subtotalを指すとしたら、なぜ0になってしまっているのか??
    public function createInvoice($fromMonth = null, $toMonth = null)
    {
        $refresh = User::find(1, ['freee_refresh_token']);
        $this->model->refreshFreeeToken($refresh->freee_refresh_token);
        $admin = User::find(1, ['freee_access_token', 'freee_user_id']);
        $access_token = $admin->freee_access_token;
        $company_id = $admin->freee_user_id;

        $authorization = "Authorization:Bearer $access_token";

        //期間指定 実行されるときは1日なので$fromは一ヶ月前を、$toには1日前の日付を指定
        $now = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('-1 day'));
        if (!is_null($fromMonth) && !is_null($toMonth)) {
            $now = date('Y-m-d');
            $from = date('Y-m-d', strtotime($fromMonth));
            $to = date('Y-m-d', strtotime($toMonth));
        }

        //Todo WIP:各FCの請求書データをループ処理/金額n>0のものだけ次のステップへ
        $fcs = User::where('role', 2)->whereIn('status', [1, 3])->get(['id', 'name', 'company_name', 'freee_user_id', 'invoice_payments_type']);
        $invoices = $this->model->getInvoices($company_id, $access_token, $from, $to);

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
            //期間を指定してあるFCのすべての請求書を取得
            $transactions = Transaction::select('transactions.*', \DB::raw($when . ' AS shiping_date'))
                ->where('transactions.user_id', $fc->id)->where('transactions.status', 1)
                ->where(function ($query) use ($fc) {
                    $query->orWhereNotNull('transactions.transaction_only_shipping_date');
                    $query->orWhereNotNull('c.shipping_date');
                })
                // 発送日が最も遅い発送タイミングで絞る (第3納品希望日があればそれを発送日にする以下第2納品希望日も同じ)
                ->whereBetween(\DB::raw($when), [$from, $to])
                ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
                ->orderBy('shipping_date', 'ASC')
                ->get();
            // その月の請求書の合計金額をfcごとに計算 n>0のものだけ次のステップへ
            $subTotal = $transactions->sum('total');
            $vat = intval(round($subTotal * config('app.tax_rate')));
            // $vat = round($subTotal * config('app.tax_rate'), 1, PHP_ROUND_HALF_UP);
            $total = ($subTotal + $vat);

            $fcId = $fc['freee_user_id'];
            // 請求先が別に設定されていたらそのIDに請求
            $billing_fc_id = is_null($fc['freee_billing_user_id']) || $fc['freee_billing_user_id'] === $fc['freee_user_id'] ? $fcId : $fc['freee_billing_user_id'];
            //請求書の詳細を計算
            $listOfContents = [];
            $order = 0;
            // 発注書1枚1枚を請求書にまとめる
            for ($t = 0; $t < count($transactions); ++$t) {
                $transactionId = $transactions[$t]['id'];
                $products = ProductTransaction::select('product_transactions.*', 'products.product_type_id')
                    ->where('transaction_id', $transactionId)->leftJoin('products', 'products.id', '=', 'product_transactions.product_id')->get()->toArray();
                $count_products = count($products);
                $contents = [];
                // 全額前金なら発注書Noと商品は請求書に入れない！
                if ($transactions[$t]->prepaid != 2) {
                    $content = $this->model->createTextRow($order, $transactions[$t]);
                    array_push($listOfContents, $content);
                    ++$order;
                    // TODO 発注書Noのテキスト行
                    // ループ商品1行ごとの処理==================
                    for ($i = 0; $i < count($products); ++$i) {
                        $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, null, $transactions[$t]->prepaid);
                        array_push($listOfContents, $contents);
                        ++$order;
                        // 人工芝に付随するカットがあれば追加
                        if (!empty($products[$i]['turf_cuts'])) {
                            foreach ($products[$i]['turf_cuts'] as $cut) {
                                $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, $cut, $transactions[$t]->prepaid);
                                array_push($listOfContents, $contents);
                                sleep(1);
                                ++$order;
                            }
                        }
                        if ($products[$i]['cut_set_num'] > 1) {
                            // 1つ目↑で行を作っているので、スタートの値が2
                            for ($multi_set = 2; $multi_set <= $products[$i]['cut_set_num']; ++$multi_set) {
                                $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, null, $transactions[$t]->prepaid);
                                array_push($listOfContents, $contents);
                                ++$order;
                                // 人工芝に付随するカットがあれば追加
                                if (!empty($products[$i]['turf_cuts'])) {
                                    foreach ($products[$i]['turf_cuts'] as $cut) {
                                        $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, $cut, $transactions[$t]->prepaid);
                                        array_push($listOfContents, $contents);
                                        sleep(1);
                                        ++$order;
                                    }
                                }
                            }
                        }
                    } //商品1行ごとの処理
                    if (!empty($transactions[$t]['discount'])) {
                        $discount_row = $this->model->createInvoiceDiscountRow($order, $t, $transactionId, $transactions[$t]['discount'], 'large');
                        array_push($listOfContents, $discount_row);
                        ++$order;
                    }
                    // 特別割引カラムに値があれば追加
                    if (!empty($transactions[$t]['special_discount'])) {
                        $discount_row = $this->model->createInvoiceDiscountRow($order, $t, $transactionId, $transactions[$t]['special_discount'], 'special');
                        array_push($listOfContents, $discount_row);
                        ++$order;
                    }
                    $shipping_row = $this->model->createInvoiceShippingRow($order, $transactionId);
                    array_push($listOfContents, $shipping_row);
                    ++$order;
                    // 半額前金なら半額分の割引を入れる
                    if ($transactions[$t]->prepaid == 1) {
                        $shipping_row = $this->model->createInvoiceHalfPaymentRow($order, $transactions[$t]);
                        array_push($listOfContents, $shipping_row);
                        ++$order;
                    }

                    // ループ商品1行ごとの処理ここまで＝＝＝＝＝＝＝＝＝
                } // if($transactions[$t]->prepaid != 2)
            } // 発注書1枚1枚を請求書にまとめる for文ここまで

            // 最後にHP掲載料を追加
            // 請求なしなら最終行を追加しない
            $lastitem = $fc->invoice_payments_type == '0' ? null : $this->model->createUsageFeeRow($fc, $order);
            if (!is_null($lastitem)) {
                array_push($listOfContents, $lastitem);
            }
            \Log::debug(print_r('=====ListOfContents=====', true));
            \Log::debug(print_r($listOfContents, true));
            $body = [
                'template_id' => config('services.freeeaccounting.template_id'),
                'company_id' => $company_id,
                'partner_id' => $fc->freee_user_id,
                'billing_partner_id' => $billing_fc_id,
                'partner_display_name' => !empty($fc->company_name) ? $fc->company_name : $fc->name,
                'partner_title' => !empty($fc->company_name) ? '御中' : '様',
                'invoice_layout' => 'carried_forward_envelope_classic',
                'invoice_status' => 'issue',
                "tax_entry_method" => "out",
                "withholding_tax_entry_method" => "out",
                'tax_fraction' => 'omit',
                'sub_total' => $subTotal,
                'total_vat' => $vat,
                'total_amount' => $total,
                'booking_date' => date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y'))),
                'billing_date' => date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y'))),
                'payment_date' => date('Y-m-25'),
                'lines' => $listOfContents,
                'notes' => '',
            ];

            if ($listOfContents != []) {
                \Log::debug(print_r('請求書発行', true));
                \Log::debug(print_r($fcId, true));
                $ch = curl_init();
                $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
                curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/iv/invoices');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

                $result = curl_exec($ch);
                \Log::debug(print_r($result, true));
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
            }
            // } else {
            //     \Log::debug(print_r('========請求書発行澄み！=========', true));
            // }
        } // foreach
    }
    // FCIDと期間を指定して請求書を発行する機能
    public function createSingleInvoice($fcId, $fromMonth, $toMonth)
    {
        $refresh = User::find(1, ['freee_refresh_token']);
        $this->model->refreshFreeeToken($refresh->freee_refresh_token);
        $admin = User::find(1, ['freee_access_token', 'freee_user_id']);
        $access_token = $admin->freee_access_token;
        $company_id = $admin->freee_user_id;

        $authorization = "Authorization:Bearer $access_token";

        //期間指定 実行されるときは1日なので$fromは一ヶ月前を、$toには1日前の日付を指定
        $now = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-1 month'));
        $to = date('Y-m-d', strtotime('-1 day'));
        if (!is_null($fromMonth) && !is_null($toMonth)) {
            $now = date('Y-m-d');
            $from = date('Y-m-d', strtotime($fromMonth));
            $to = date('Y-m-d', strtotime($toMonth));
        }

        //Todo WIP:各FCの請求書データをループ処理/金額n>0のものだけ次のステップへ
        $fc = User::find($fcId);
        $invoices = $this->model->getInvoices($company_id, $access_token, $from, $to);
        // $transactions = Transaction::select('transactions.*', \DB::raw('(CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date END) AS shipping_date'))
        //     ->where('transactions.user_id', $fc->id)->where('transactions.status', 1)
        //     // 削除済み請求書でも発送日が入っていればカウントする
        //     // ->where(function ($query) use ($fc) {
        //     //     $query->orWhereNotNull('transactions.transaction_only_shipping_date');
        //     //     $query->orWhereNotNull('c.shipping_date');
        //     // })
        //     ->whereBetween(\DB::raw('CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date END'), [$from, $to])
        //     ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
        //     ->orderBy('shipping_date', 'ASC')
        //     ->get();
        //期間を指定してあるFCのすべての請求書を取得
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
        $transactions = Transaction::select('transactions.*', \DB::raw($when . ' AS shipping_date'))
            ->where('transactions.user_id', $fc->id)->where('transactions.status', 1)
            ->where(function ($query) use ($fc) {
                $query->orWhereNotNull('transactions.transaction_only_shipping_date');
                $query->orWhereNotNull('c.shipping_date');
            })
            // 発送日が最も遅い発送タイミングで絞る (第3納品希望日があればそれを発送日にする以下第2納品希望日も同じ)
            ->whereBetween(\DB::raw($when), [$from, $to])
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->orderBy('shipping_date', 'DESC')
            ->get();
        $subTotal = $transactions->sum('total');
        $vat = intval(round($subTotal * config('app.tax_rate')));
        // $vat = round(($subTotal * config('app.tax_rate')), 1, PHP_ROUND_HALF_UP);
        $total = ($subTotal + $vat);
        // dd($subTotal, $vat, $total);

        $fcId = $fc['freee_user_id'];
        //請求書の詳細を計算
        $listOfContents = [];
        $order = 0;
        // 発注書1枚1枚を請求書にまとめる
        for ($t = 0; $t < count($transactions); ++$t) {
            $transactionId = $transactions[$t]['id'];
            $products = ProductTransaction::select('product_transactions.*', 'products.product_type_id')
                ->where('transaction_id', $transactionId)->leftJoin('products', 'products.id', '=', 'product_transactions.product_id')->get()->toArray();
            $count_products = count($products);
            $contents = [];
            // 全額前金なら発注書Noと商品は請求書に入れない！
            if ($transactions[$t]->prepaid != 2) {
                $content = $this->model->createTextRow($order, $transactions[$t]);
                array_push($listOfContents, $content);
                ++$order;
                // TODO 発注書Noのテキスト行
                // ループ商品1行ごとの処理==================
                for ($i = 0; $i < count($products); ++$i) {
                    $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, null, $transactions[$t]->prepaid);
                    array_push($listOfContents, $contents);
                    ++$order;
                    // 人工芝に付随するカットがあれば追加
                    if (!empty($products[$i]['turf_cuts'])) {
                        foreach ($products[$i]['turf_cuts'] as $cut) {
                            $contents = $this->model->createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, $cut, $transactions[$t]->prepaid);
                            array_push($listOfContents, $contents);
                            sleep(1);
                            ++$order;
                        }
                    }
                } //商品1行ごとの処理
                if (!empty($transactions[$t]['discount'])) {
                    $discount_row = $this->model->createInvoiceDiscountRow($order, $t, $transactionId, $transactions[$t]['discount'], 'large');
                    array_push($listOfContents, $discount_row);
                    ++$order;
                }
                // 特別割引カラムに値があれば追加
                if (!empty($transactions[$t]['special_discount'])) {
                    $discount_row = $this->model->createInvoiceDiscountRow($order, $t, $transactionId, $transactions[$t]['special_discount'], 'special');
                    array_push($listOfContents, $discount_row);
                    ++$order;
                }
                $shipping_row = $this->model->createInvoiceShippingRow($order, $transactionId);
                array_push($listOfContents, $shipping_row);
                ++$order;
                // 半額前金なら半額分の割引を入れる
                if ($transactions[$t]->prepaid == 1) {
                    $shipping_row = $this->model->createInvoiceHalfPaymentRow($order, $transactions[$t]);
                    array_push($listOfContents, $shipping_row);
                    ++$order;
                }
                // ループ商品1行ごとの処理ここまで＝＝＝＝＝＝＝＝＝
            } // if($transactions[$t]->prepaid != 2)
        } // 発注書1枚1枚を請求書にまとめる for文ここまで

        // 最後にHP掲載料を追加
        // 請求なしなら最終行を追加しない
        $lastitem = $fc->invoice_payments_type == '0' ? null : $this->model->createUsageFeeRow($fc, $order);
        if (!is_null($lastitem)) {
            array_push($listOfContents, $lastitem);
        }
        // \Log::debug(print_r('=====ListOfContents=====', true));
        // \Log::debug(print_r($listOfContents, true));
        $body = [
            'template_id' => config('services.freeeaccounting.template_id'),
            'company_id' => $company_id,
            'partner_id' => $fc->freee_user_id,
            'partner_display_name' => !empty($fc->company_name) ? $fc->company_name : $fc->name,
            'partner_title' => !empty($fc->company_name) ? '御中' : '様',
            'partner_contact_info' => null,
            'invoice_layout' => 'carried_forward_envelope_classic',
            'invoice_status' => 'issue',
            "tax_entry_method" => "out",
            "withholding_tax_entry_method" => "out",
            'tax_fraction' => 'omit',
            'sub_total' => $subTotal,
            'total_vat' => $vat,
            'total_amount' => $total,
            'booking_date' => date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y'))),
            'billing_date' => date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y'))),
            'payment_date' => date('Y-m-25'),
            'lines' => $listOfContents,
            'notes' => '',
        ];

        // if (!$is_invoice_exist) {
        if ($listOfContents != []) {
            \Log::debug(print_r('請求書発行！', true));
            \Log::debug(print_r($fcId, true));
            $ch = curl_init();
            $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
            curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/iv/invoices');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

            $result = curl_exec($ch);
            \Log::debug(print_r($result, true));
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
        }
    }

    public function screenUpload(Request $request, $state = '発注書類作成', $dir = 'create')
    {
        $posts = $request->all();
        $img = $posts['image'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $carbon = new Carbon();
        $filename = $state . ' ' . $carbon->now()->format('Y-m-d_H:i:s') . ' user_id : ' . \Auth::id();

        \Storage::disk('s3')->putFileAs("/transactions/screenshots/$dir", $img, $filename);
    }

    public function ajaxUpdate(Request $request)
    {
        $posts = $request->all();
        if (is_null($posts['id']) || is_null($posts['column'] || is_null($posts['value']))) {
            return false;
        }
        \Log::debug(print_r($posts, true));
        $this->model->where('id', $posts['id'])->update([$posts['column'] => $posts['value']]);

        return response()->json([$posts['column'] => $posts['value']]);
    }

    public function ajaxIsskip($id)
    {
        $transaction = Transaction::where('contact_id', $id)->where('transactions.status', 1)
            ->whereIn('c.step_id', [7, 8, 9, 10, 11])->whereNot('c.step_id', 99)->where('c.status', 1)
            ->join('contacts AS c', 'transactions.contact_id', '=', 'c.id')->first();

        $result = is_null($transaction) ? true : false;

        return response()->json(['result' => $result]);
    }

    public function getDeliveryDays($day)
    {
        return $this->model->getDeliveryDate($day);
    }

    public function adminSettings()
    {
        $this->breadcrumbs->addCrumb('送料設定', '')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $turfs = Product::where('product_type_id', 1)->where('status', 1)->get();
        $free_shipping_item_id = Config::where('key', 'free_shipping_product_id')->value('value');

        $prefectures = Prefecture::select('r.*', 'prefectures.*', 'r.name AS region_name')->whereNot('prefectures.name', '')
            ->join('regions AS r', 'prefectures.region_id', '=', 'r.id')
            ->orderBy('prefectures.id', 'ASC')
            ->get()->toArray();
        $regions = Region::select('regions.*', 'p.charter_shipping_price', DB::raw('GROUP_CONCAT(p.name SEPARATOR ", ") as prefecture_names'))
            ->join('prefectures AS p', 'regions.id', '=', 'p.region_id')
            ->groupBy('regions.id')
            ->orderBy('regions.id', 'ASC')->get()->toArray();
        foreach ($regions as $key => $r) {
            if ($r['prefecture_names']) {
                $regions[$key]['prefectures'] = explode(', ', $r['prefecture_names']);
            }
        }
        // dd($prefectures);
        // dd($regions);

        return view('admin.transaction.shipping-price', compact('breadcrumbs', 'regions', 'prefectures', 'turfs', 'free_shipping_item_id'));
    }

    public function adminSettingsUpdate(Request $request)
    {
        $posts = $request->all();
        // update regions table shipping_prices
        foreach ($posts['r'] as $key => $region) {
            $region_id = $key;
            Region::where('id', $region_id)->update([
                'small_shipping_price' => $region['small_shipping_price'],
                'large_shipping_price' => $region['large_shipping_price'],
                'extra_large_shipping_price' => $region['extra_large_shipping_price'],
                'extra_large_shipping_price2' => $region['extra_large_shipping_price2'],
                'extra_large_shipping_price3' => $region['extra_large_shipping_price3'],
            ]);
        }
        // update prefecture_table where region_id charter_shipping_price
        // dd($posts['rcharter']);
        /*
        foreach ($posts['rcharter'] as $key => $charter_shipping_price) {
            $region_id = $key;
            // リージョン内でその他の県がある場合は、その他の県の送料を更新する
            if($region_id == '4') {
                // 関東なら茨城、栃木、埼玉、新潟以外を更新
                Prefecture::where('region_id', 4)->whereNotIn('id',[8,9,11,15])->update(['charter_shipping_price' => $charter_shipping_price]);
            } else {
                Prefecture::where('region_id', $region_id)->update(['charter_shipping_price' => $charter_shipping_price]);
            }
        }
        */
        // update prefectures table charter_shipping_price
        // dd($posts['p']);
        foreach ($posts['p'] as $key => $charter_shipping_price) {
            $prefecture_id = $key;
            Prefecture::where('id', $prefecture_id)->update([
                'charter_shipping_price' => $charter_shipping_price,
            ]);
        }

        return redirect()->route('transactions.admin.shipping-price')->with('success', '送料の更新が完了しました。');
    }
}
