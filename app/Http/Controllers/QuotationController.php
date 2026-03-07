<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminQuotationRequest;
use App\Http\Requests\CreateQuotationRequest;
use App\Mail\SendAdminQuotationMail;
use App\Models\Contact;
use App\Models\Product;
use App\Models\ProductQuotation;
use App\Models\ProductQuotationMaterial;
use App\Models\Quotation;
use App\Models\SameCustomerContact;
use App\Models\User;
use App\Models\CustomerActivity; // Added import
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Mail;

class QuotationController extends MyController
{
    public function __construct(Quotation $quotation)
    {
        parent::__construct();
        $this->model = $quotation;
        $this->breadcrumbs->addCrumb('<i class="fas fa-hourglass"></i>見積もり', 'quotations');
    }

    public function index()
    {
        $this->breadcrumbs->addCrumb('見積もり一覧');
        $breadcrumbs = $this->breadcrumbs;

        if (isAdmin()) {
            $quotations = $this->model->select('quotations.*', 'quotations.id AS quotation_id', 'quotations.user_id AS quotation_user_id', 'c.user_id', 'c.id AS contact_id', 'u.*')
                ->where('quotations.status', 1)
                ->leftjoin('contacts AS c', 'c.id', '=', 'quotations.contact_id')
                ->join('users AS u', 'c.user_id', '=', 'u.id')
                ->orderBy('quotations.id', 'DESC')
                ->get();
        } else {
            $quotations = $this->model->select('c.*', 'c.id AS contact_id', 'quotations.client_name', 'quotations.id AS quotation_id', 'quotations.user_id AS quotation_user_id')
                ->where('quotations.user_id', \Auth::id())
                ->where('quotations.status', 1)
                ->leftjoin('contacts AS c', 'c.id', '=', 'quotations.contact_id')
                ->orderBy('quotations.id', 'DESC')
                ->get();
        }

        return view('share.quotation.index', compact('breadcrumbs', 'quotations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id, $copyId = null)
    {
        $contact = Contact::find($id);
        $quotation_type = 0;
        $is_copy = is_null($copyId) ? 0 : 1;

        if (isAdmin() && $contact->step_id != '5' && $contact->quote_details != '材料のみ') {
            if ($contact->contact_type_id != '2') {
                return redirect(route('unassigned.list'))->with('warning', '管理者は材料のみの問い合わせにしか見積もりができません！');
            } elseif ($contact->contact_type_id != '6') {
                return redirect(route('unassigned.list'))->with('warning', '管理者は材料のみの問い合わせにしか見積もりができません！');
            }
        }

        $base_quotation = [];
        if (!is_null($copyId)) {
            $quotation_type = $this->model->getField(['id' => $copyId], 'type');

            if ($quotation_type == 0) {
                $base_quotation = $this->model->getNomalQuotation($copyId);
            } elseif ($quotation_type == 1) {
                $base_quotation = $this->model->getMaterialQuotation($copyId);
            }
        }
        $allProducts = Product::where('status', 1)->orderBy('order_no', 'ASC')->get()->toArray();
        $turfs = array_filter($allProducts, 'isTurf', ARRAY_FILTER_USE_BOTH);
        $subItems = array_filter($allProducts, 'isSub', ARRAY_FILTER_USE_BOTH);
        $cutItems = array_filter($allProducts, 'isCut', ARRAY_FILTER_USE_BOTH);

        // パンクズの分岐
        if (($contact->contact_type_id == '2' && $contact->quote_details == '材料のみ') || ($contact->contact_type_id == '6' && $contact->quote_details == '材料のみ')) {
            $this->breadcrumbs->addCrumb('材料購入見積もり作成', '/new/'.$id)->setLastItemWithHref(true);
        } else {
            $this->breadcrumbs->addCrumb('見積もり作成', '/new/'.$id)->setLastItemWithHref(true);
        }
        $breadcrumbs = $this->breadcrumbs;
        $fc = $contact->user;

        $quotation_tax_option = User::where('id',\Auth::id())->select('quotation_tax_option')->first();

        $products = Product::where('status', 1)->whereIn('product_type_id', [1, 2])->get();
        // アクティブな同一顧客調べる
        $same_customer_model = new SameCustomerContact();
        $same_user_exists = $same_customer_model->activeSameCustomer($contact->id);
        if ($same_user_exists && !is_null($contact['main_user_id']) && $contact['user_id'] != $contact['main_user_id'] && isFc()) {
            return redirect()->route('quotations.index')->with('warning', '他FC対応中のため、見積書の作成はできません');
        }

        return view('share.quotation.create', compact('products', 'quotation_type', 'is_copy', 'allProducts', 'breadcrumbs', 'contact', 'id', 'fc', 'base_quotation', 'turfs', 'subItems', 'cutItems', 'quotation_tax_option'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuotationRequest $request)
    {
        $posts = $request->all();
        
        $now_step_id = nowStepId($posts['id']);
        // step_idが 5 以上(商談の結果入力以降のステップ)だった場合step_idを更新しない
        $should_be_update = ($now_step_id  <= self::STEP_RESULT) ? true : false;
        
        if(empty($posts['pq'][0])){
            return redirect(route('quotations.create', ['id' => $posts['id']]))->with(['danger' => '見積もりには最低1つ以上の商品を追加してください。']);
        }
        if(is_null($posts['q']['sub_total'])){
            return redirect(route('quotations.create', ['id' => $posts['id']]))->with(['danger' => 'すべての行の金額を埋めてください。']);
        }
        $posts['q']['user_id'] = \Auth::id();

        $contacts = new Contact();
        $data = Contact::find($posts['q']['contact_id']);
        $sameCustomers = $contacts->findSameCustomer($data, $data['id']);

        $this->model->transStart();
        try {
            //dd($posts);
            $quotation_id = $this->model->insertGetId($posts['q']);

            // INTENTIONAL FLAW: Code Duplication. This logic should be in an event listener or service.
            $activity = new CustomerActivity();
            $activity->customer_id = $posts['q']['contact_id'];
            $activity->activity_type = '見積もり作成'; // Hardcoded string
            $activity->description = '見積もりNo.' . $quotation_id . 'を作成しました。';
            $activity->related_id = $quotation_id;
            $activity->related_type = 'quotation';
            $activity->user_id = \Auth::id();
            $activity->save();

            // 自由記述が入ると、VALUEの値がずれてインサートエラーになるので、ループインサートしゃーなし！
            foreach ($posts['pq'] as $key => $value) {
                $posts['pq'][$key]['quotation_id'] = $quotation_id;
                ProductQuotation::insert($posts['pq'][$key]);
            }
            if (isAdmin()) {
                // 本部が見積もりした場合
                if ($should_be_update){
                    Contact::where('id', $posts['q']['contact_id'])->update(['user_id' => 1, 'step_id' => self::STEP_RESULT]);
                }
                if (config('app.env') != 'circleci') {
                    $this->model->pdfToS3($quotation_id);
                }
            } else {
                if ($should_be_update){
                    Contact::where('id', $posts['q']['contact_id'])->update(['step_id' => self::STEP_RESULT]);
                }
            }
            //本部見積もりをアップデート順にするため、contactsテーブルのupdated_atを更新
            if (!empty($posts['id'])) {
                Contact::where('id', $posts['id'])->update(['updated_at' => Carbon::now()]);
            } else {
                Contact::where('id', $posts['q']['contact_id'])->update(['updated_at' => Carbon::now()]);
            }

            if ($data['contact_type_id'] == 3 || $data['contact_type_id'] == 7){
                $contacts->setMainFc($sameCustomers, $posts['q']);
            }

            $this->model->transCommit();

            if ($posts['add'] == '1') {
                return redirect('/quotations/new/'.$posts['q']['contact_id'].'/'. ($posts['add-copy'] == '1' ? $quotation_id : ''))->with(['success' => '見積書No. '. $quotation_id . 'として新しい見積もりを作成しました。続けて見積書を作成してください。']);
            } else {
                // ログインが本部かつコピー元の見積書を作成したのが本部ではない場合の処理
                if (isAdmin() && !empty($posts['row_userid']) && $posts['row_userid'] !== '1') {
                    return redirect('quotations')->with(['success' => '見積書No. '. $quotation_id . 'として新しい見積もりを作成しました。顧客に見積書を提出しましょう。']);
                } elseif (isAdmin()) {
                    return redirect(route('quotations.admin.needs'))->with(['success' => '見積書No. '. $quotation_id . 'として新しい見積もりを作成しました。顧客に見積書を提出しましょう。']);
                } else {
                    return redirect('quotations')->with(['success' => '見積書No. '. $quotation_id . 'として新しい見積もりを作成しました。顧客に見積書を提出しましょう。']);
                }
            }
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }
    }

    public function materialStore(Request $request)
    {
        $posts = $request->all();
        if(empty($posts['pqm'][0])){
            return redirect(route('quotations.create', ['id' => $posts['id']]))->with(['danger' => '見積もりには最低1つ以上の商品を追加してください。']);
        }
        $posts['q']['user_id'] = \Auth::id();

        $contacts = new Contact();
        $data = Contact::find($posts['q']['contact_id']);
        $sameCustomers = $contacts->findSameCustomer($data, $data['id']);

        $this->model->transStart();
        // dd($posts);
        try {
            $quotation_id = $this->model->insertGetId($posts['q']);

            // INTENTIONAL FLAW: Code Duplication. Identical logic repeated.
            $activity = new CustomerActivity();
            $activity->customer_id = $posts['q']['contact_id'];
            $activity->activity_type = '見積もり作成';
            $activity->description = '見積もりNo.' . $quotation_id . 'を作成しました。';
            $activity->related_id = $quotation_id;
            $activity->related_type = 'quotation';
            $activity->user_id = \Auth::id();
            $activity->save();
            if( is_null($quotation_id) ){
                throw new Exception("見積書が作成できませんでした。開発者に案件Noと共にお知らせください");
            }
            //セット数指定の行はDBに追加しない
            $set_num = [];
            foreach ($posts['pqm'] as $key => $value){
                if(isset($posts['pqm'][$key]['set_num'])){
                    $set_num[$posts['pqm'][$key]['parent_id']] = $posts['pqm'][$key]['set_num'];
                    unset($posts['pqm'][$key]);
                    array_values($posts['pqm']);
                }
            }
            // 材料見積もりの1行1行を追加
            $sub_total = 0;
            foreach ($posts['pqm'] as $key => $value) {
                $posts['pqm'][$key]['quotation_id'] = $quotation_id;
                $posts['pqm'][$key]['turf_cuts'] = [];
                if (!empty($posts['cut'])) {
                    foreach ($posts['cut'] as $cut) {
                        if ($value['row_id'] == $cut['parent_id']) {
                            Arr::forget($cut, 'parent_id');
                            // カット賃の備考欄は削除
                            if($cut['memo'] === 'undefined' || $cut['product_id'] == 71){
                                Arr::forget($cut, 'memo');
                            }
                            array_push($posts['pqm'][$key]['turf_cuts'], $cut);
                            // ↓ カットはここでサーバーサイド集計
                            $sub_total += $cut['num'] * $cut['unit_price'] * $set_num[$posts['pqm'][$key]['row_id']];
                        }
                    }
                }
                $posts['pqm'][$key]['turf_cuts'] = json_encode($posts['pqm'][$key]['turf_cuts']);
                // 切り売り人工芝の場合は1にする
                if (!empty($posts['pqm'][$key]['vertical']) && !empty($posts['pqm'][$key]['horizontal'])) {
                    $posts['pqm'][$key]['cut'] = 1;
                    $posts['pqm'][$key]['cut_set_num'] = $set_num[$posts['pqm'][$key]['row_id']];
                }
                if (empty($posts['pqm'][$key]['is_cut'])) {
                    Arr::forget($posts['pqm'][$key], 'is_cut');
                    Arr::forget($posts['pqm'][$key], 'parent_id');
                    Arr::forget($posts['pqm'][$key], 'row_id');
                    // サーバーサイド集計答え合わせ
                    $cut_set_num = !empty($posts['pqm'][$key]['cut_set_num']) ? $posts['pqm'][$key]['cut_set_num'] : 1;
                    $sub_total += $posts['pqm'][$key]['num'] * $posts['pqm'][$key]['unit_price'] * $cut_set_num;
                    ProductQuotationMaterial::insert($posts['pqm'][$key]);
                }
            }
            $posts['q']['sub_total'] = $sub_total;
            $posts['q']['total'] = $sub_total + intval(round($sub_total * config('app.tax_rate')));
            if (isAdmin()) {
                // 本部が見積もりした場合
                Contact::where('id', $posts['q']['contact_id'])->update(['user_id' => 1, 'step_id' => self::STEP_RESULT]);
                if (config('app.env') != 'circleci') {
                    $this->model->pdfToS3($quotation_id, $posts['q']['sub_total'], $posts['q']['total']);
                }
            } else {
                Contact::where('id', $posts['q']['contact_id'])->update(['step_id' => self::STEP_RESULT]);
            }
            //本部見積もりをアップデート順にするため、contactsテーブルのupdated_atを更新
            if (!empty($posts['id'])) {
                Contact::where('id', $posts['id'])->update(['updated_at' => Carbon::now()]);
            } else {
                Contact::where('id', $posts['q']['contact_id'])->update(['updated_at' => Carbon::now()]);
            }

            if ($data['contact_type_id'] == 3 || $data['contact_type_id'] == 7){
                $contacts->setMainFc($sameCustomers, $posts['q']);
            }

            $this->model->transCommit();
            if ($posts['add'] == '1') {
                return redirect('/quotations/new/'.$posts['q']['contact_id'].'/'. ($posts['add-copy'] == '1' ? $quotation_id : ''))->with(['success' => '見積書No. '. $quotation_id . 'として新しい見積もりを作成しました。続けて見積書を作成してください。']);
            } else {
                if (isAdmin()) {
                    return redirect(route('quotations.admin.needs'))->with(['success' => '見積書No. '. $quotation_id .'として新しい見積もりを作成しました。顧客に見積書を提出しましょう。']);
                } else {
                    return redirect('quotations')->with(['success' => '見積書No. '. $quotation_id .'として新しい見積もりを作成しました。顧客に見積書を提出しましょう。']);
                }
            }
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }
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
        $this->breadcrumbs->addCrumb('見積もり確認', $id);
        $breadcrumbs = $this->breadcrumbs;
        $products = Product::orderBy('id', 'ASC')->get()->toArray();

        $quotation_type = $this->model->getField(['id' => $id], 'type');

        if ($quotation_type == 0) {
            $quotations = $this->model->getNomalQuotation($id);
        } elseif ($quotation_type == 1) {
            $quotations = $this->model->getMaterialQuotation($id);
        }

        if(empty($quotations[0])){
            return \App::abort(404);
        }

        if ((\Auth::id() != $quotations[0]['user_id']) && isFc()) {
            return redirect()->route('quotations.index')->with('warning', '他FCの案件にはアクセスできません。');
        }

        $contact = Contact::where('id', $quotations[0]['contact_id'])->first();
        if ($contact['user_id'] == $contact['main_user_id']){
            $isMainFc = true;
        } else {
            $isMainFc = false;
        }

        // dd($quotations);
        return view('share.quotation.show', compact('breadcrumbs', 'quotations', 'products', 'isMainFc'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->breadcrumbs->addCrumb('見積もり編集');
        $breadcrumbs = $this->breadcrumbs;

        // $products = Product::orderBy('id', 'ASC')->get();
        $products = Product::where('status', 1)->get()->toArray();
        $turfs = array_filter($products, 'isTurf', ARRAY_FILTER_USE_BOTH);
        $subItems = array_filter($products, 'isSub', ARRAY_FILTER_USE_BOTH);
        $cutItems = array_filter($products, 'isCut', ARRAY_FILTER_USE_BOTH);

        $quotation_type = $this->model->getField(['id' => $id], 'type');

        // 編集時には、作成したFCの消費税計算（切り捨て切り上げなど）の値を持ってくる。
        $quotation_tax_option = Quotation::select('users.quotation_tax_option')
            ->where('quotations.id',$id)
            ->leftJoin('users', 'quotations.user_id', '=', 'users.id')
            ->first();

        if ($quotation_type == 0) {
            $quotations = $this->model->getNomalQuotation($id);
        } elseif ($quotation_type == 1) {
            $quotations = $this->model->getMaterialQuotation($id);
        }

        if (empty($quotations[0] || $quotation[0]['status'] == 2)) {
            return redirect()->route('quotations.index')->with('warning', 'この見積書は削除されました');
        }
        // dd($quotation);
        if ((\Auth::id() != $quotations[0]['user_id']) && isFc()) {
            return redirect()->route('quotations.index')->with('warning', '他FCの案件にはアクセスできません。');
        }
        if (!is_null($quotations[0]['main_user_id']) && $quotations[0]['user_id'] != $quotations[0]['main_user_id'] && isFc()){
            return redirect()->route('quotations.index')->with('warning', '他FC対応中のため、見積書の作成はできません');
        }

        return view('share.quotation.edit', compact('breadcrumbs', 'quotation_type', 'quotations', 'products', 'turfs', 'subItems', 'cutItems', 'quotation_tax_option'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(CreateQuotationRequest $request)
    {
        $posts = $request->all();
        $this->model->transStart();
        if(empty($posts['pq'][0])){
            return redirect(route('quotations.edit', ['id' => $posts['q']['id']]))->with(['danger' => '見積もりには最低1つ以上の商品を追加してください。']);
        }
        try {
            // $posts['q']['id'] = $id;
            $pqModel = new ProductQuotation();
            $beforePproductQuotations = $pqModel->where('quotation_id', $posts['q']['id'])->where('status', 1)->get()->toArray();
            $addProducts = $pqModel->addProducts($posts['pq'], $posts['q']['id']);
            $removeProducts = $pqModel->removeProducts($beforePproductQuotations, $posts['pq']);
            // 値引きを消したらnull追加
            if (empty($posts['q']['discount'])) {
                $posts['q']['discount'] = null;
            }
            if( is_null($posts['q']['id']) ){
                throw new Exception("見積書を更新できませんでした。開発者に案件Noと共にお知らせください");
            }
            $this->model->where('id', $posts['q']['id'])->update($posts['q']);
            // 削除処理
            foreach ($removeProducts as $value) {
                $pqModel->where('id', $value['id'])->update(['status' => 2]);
            }
            // 追加処理
            foreach ($addProducts as $value) {
                $pqModel->insert($value);
            }
            $pqModel->update($posts['pq']);
            $contact = Contact::find($posts['q']['contact_id']);
            if (isAdmin() && $contact['user_id'] == 1) {
                // 本部が見積もりした場合
                if (config('app.env') != 'circleci') {
                    $this->model->pdfToS3($posts['q']['id']);
                }
            } 
            $this->model->transCommit();

            return redirect('/quotations/'.$posts['q']['id'])->with(['success' => '見積もりを修正しました。']);
        } catch (Exception $e) {
            \Log::debug('==============quotation update error==========');
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();

            return redirect('/quotations')->with(['warning' => '見積もりを修正できませんでした。時間をおいて再度お試しください。']);
        }
    }

    public function materialUpdate(CreateQuotationRequest $request)
    {
        $posts = $request->all();
        if(empty($posts['pqm'][0])){
            return redirect(route('quotations.edit', ['id' => $posts['q']['id']]))->with(['danger' => '見積もりには最低1つ以上の商品を追加してください。']);
        }

        $this->model->transStart();
        try {
            $pqmModel = new ProductQuotationMaterial();
            $beforePproductQuotations = $pqmModel->where('quotation_id', $posts['q']['id'])->where('status', 1)->get()->toArray();
            $addProducts = $pqmModel->addProducts($posts['pqm'], $posts['q']['id']);
            $removeProducts = $pqmModel->removeProducts($beforePproductQuotations, $posts['pqm']);
            
            $set_num = [];
            foreach ($posts['pqm'] as $key => $value){
                if(isset($posts['pqm'][$key]['set_num'])){
                    $set_num[$posts['pqm'][$key]['parent_id']] = $posts['pqm'][$key]['set_num'];
                    unset($posts['pqm'][$key]);
                    $posts['pqm'] = array_values($posts['pqm']);
                }
            }

            // 値引きを消したらnull追加
            if (empty($posts['q']['discount'])) {
                $posts['q']['discount'] = null;
            }
            $this->model->where('id', $posts['q']['id'])->update($posts['q']);
            // 削除処理
            foreach ($removeProducts as $value) {
                $pqmModel->where('id', $value['id'])->update(['status' => 2]);
            }
            // 追加処理
            foreach ($posts['pqm'] as $key => $value) {
                $posts['pqm'][$key]['quotation_id'] = $posts['q']['id'];
                $posts['pqm'][$key]['turf_cuts'] = [];
                if (!empty($posts['cut'])) {
                    foreach ($posts['cut'] as $cut) {
                        if ($value['row_id'] == $cut['parent_id']) {
                            Arr::forget($cut, 'parent_id');
                            array_push($posts['pqm'][$key]['turf_cuts'], $cut);
                        }
                    }
                }
                $posts['pqm'][$key]['turf_cuts'] = json_encode($posts['pqm'][$key]['turf_cuts']);
                if (empty($posts['pqm'][$key]['is_cut'])) {
                    Arr::forget($posts['pqm'][$key], 'is_cut');
                    Arr::forget($posts['pqm'][$key], 'parent_id');
                    // 切り売り人工芝の場合は1にする
                    if (!empty($posts['pqm'][$key]['vertical']) && !empty($posts['pqm'][$key]['horizontal'])) {
                        $posts['pqm'][$key]['cut'] = 1;
                        $posts['pqm'][$key]['cut_set_num'] = $set_num[$posts['pqm'][$key]['row_id']];
                    }
                    Arr::forget($posts['pqm'][$key], 'row_id');
                    ProductQuotationMaterial::insert($posts['pqm'][$key]);
                }
            }
            $pqmModel->update($posts['pqm']);
            $this->model->transCommit();
            if (isAdmin() && config('app.env') != 'circleci') {
                $this->model->pdfToS3($posts['q']['id']);
            }

            return redirect('/quotations/'.$posts['q']['id'])->with(['success' => '見積もりを修正しました。']);
        } catch (Exception $e) {
            \Log::debug('==============quotation update error==========');
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();

            return redirect('/quotations')->with(['warning' => '見積もりを修正できませんでした。時間をおいて再度お試しください。']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function download($id)
    {
        $pdf = app('dompdf.wrapper');

        $products = Product::orderBy('id', 'ASC')->get();
        $quotation_type = $this->model->getField(['id' => $id], 'type');

        // カット陳の行は別にカウント
        $cut_row_count = 0;
        if ($quotation_type == 0) {
            $quotations = $this->model->getNomalQuotation($id);
            $cut_total = null;
            $cut_total_length = null;
            $pdf_product_count = ProductQuotation::where('quotation_id', $id)->where('status', 1)->count();
        } elseif ($quotation_type == 1) {
            $quotations = $this->model->getMaterialQuotation($id);
            $pdf_product_count = ProductQuotationMaterial::where('quotation_id', $id)->where('status', 1)->count();
            // PDFは個別のカットメニューを出せずに「カット賃」の合計を表示すればいいので、計算
            $total = 0;
            $total_length = 0.00;
            if(!$quotations[0])  \App::abort(404);
            foreach ($quotations as $q) {
                if ($q['cut'] == 1) {
                    $cut_row_count = 1;
                    foreach ($q['turf_cuts'] as $cut) {
                        if ($cut['unit'] == 'm') {
                            $cut_set_num = !is_null($q['cut_set_num']) ? $q['cut_set_num'] : 1;
                            $total = $total + floatval($cut['num']) * intval($cut['unit_price'] * $cut_set_num);
                            $total_length = $total_length + floatval($cut['num']) * $cut_set_num;
                        }
                    }
                }
            }
            $cut_total = $total;
            $cut_total_length = $total_length;
        }
        foreach($quotations as $q){
            if(mb_strlen($q['memo']) > 15){
                $pdf_product_count++;
            }
        }
        if(!$quotations[0])  \App::abort(404);
        $pdf_memo_count = substr_count($quotations[0]['quotation_memo'],"\n");
        $pdf_payee_count = substr_count($quotations[0]['payee'],"\n");
        
        $pdf_product_count = $pdf_product_count + $cut_row_count;
        $pdf_quotation_row_and_memo_count = $pdf_memo_count + $pdf_product_count;

        // 設定ファイルではなく、コントローラー内で設定しないといけないので注意！
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true, 'isRemoteEnabled' => true, 'enable_font_subsetting' => true])->loadView('share.quotation.pdf', ['quotations' => $quotations, 'products' => $products, 'cut_total' => $cut_total, 'cut_total_length' => $cut_total_length ,'pdf_quotation_row_and_memo_count' => $pdf_quotation_row_and_memo_count, 'pdf_memo_count' => $pdf_memo_count, 'pdf_payee_count' => $pdf_payee_count ,'pdf_product_count' => $pdf_product_count, 'cut_row_count' => $cut_row_count]);

        return $pdf->stream('案件No'.$id.'見積書.pdf');
    }

    public function needsIndex()
    {
        $this->breadcrumbs->addCrumb('見積もり未作成案件一覧');
        $breadcrumbs = $this->breadcrumbs;

        $contacts = Contact::select('contacts.*', 'contacts.user_id', 'contacts.id AS contact_id', 'contacts.surname AS client_surname', 'contacts.name AS client_name', 'u.id AS user_id', 'un.read')
                ->where('contacts.status', 1)
                ->where('contacts.step_id', self::STEP_QUOTATION)
                ->where('contacts.user_id', \Auth::id())
                ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
                ->leftJoin('user_notifications AS un', 'un.contact_id', '=', 'contacts.id')
                ->groupBy('contacts.id')
                ->orderBy('contacts.id', 'DESC')
                ->get();

        return view('share.quotation.needs-index', compact('breadcrumbs', 'contacts'));
    }

    // 複数見積もりから選択
    public function contactUpdate(Request $request, $contactId)
    {
        if (isAdmin()) {
            $contact = Contact::find($contactId);
            // 見積もり選び直しの際はステップ変えない
            $step = $contact['step_id'] < self::STEP_SHIPPING ? self::STEP_SHIPPING : $contact['step_id'] ;
            $resutlt = Contact::where('id', $contactId)->update(['quotation_id' => $request->input('quotation_id'), 'step_id' => $step]);

            return redirect(route('dispatch.pending'))->with('success', '顧客選択見積もりを選びました。準備が出来次第、顧客に発送してください。');
        }
        Contact::where('id', $contactId)->update(['quotation_id' => $request->input('quotation_id')]);

        return redirect()->route('transaction.pending.list')->with('success', '顧客が選んだ見積もりを確定しました。次は必要な部材を発注してください。');
    }

    public function adminNeeds()
    {
        $this->breadcrumbs->addCrumb('本部見積もり案件一覧');
        $breadcrumbs = $this->breadcrumbs;

        $contacts = Contact::select('contacts.*', 'contacts.user_id', 'contacts.id AS contact_id', 'contacts.surname AS client_surname', 'contacts.name AS client_name', 'u.id AS user_id')
            ->where('contacts.status', 1)
            ->where(function ($query) {
                $query->orWhere('contacts.user_id', 1)
                  ->orWhere('contacts.user_id', null);
            })
            ->where('contacts.step_id', '<=', self::STEP_RESULT)
            ->where('contacts.quote_details', '材料のみ')
            ->whereIn('contact_type_id', [2, 6])
            ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
            ->orderBy('contacts.updated_at', 'DESC')
            ->get();

        return view('admin.quotation.need-index', compact('breadcrumbs', 'contacts'));
    }

    public function ajaxQuotations($id)
    {
        $quotations = $this->model->select('quotations.*')
          // TODO 営業日計算
          ->where('status', 1)->where('user_id', \Auth::id())->where('contact_id', $id)
          ->orderBy('id', 'ASC')
          ->get();

        return response()->json($quotations);
    }

    // 本部見積もり案件の見積書以外の添付ファイルアップロード（dropzone)
    public function ajaxUploadFile(Request $request)
    {
        $input = $request->all();
        $rules = [
          'file' => 'max:5000',
        ];

        $validation = \Validator::make($input, $rules);

        if ($validation->fails()) {
            return \Response::make($validation->errors->first(), 400);
        }

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        $path = \Storage::disk('s3')->putFileAs("/quotations/$input[contact_id]/files/", $file, $filename, 'public');

        if ($path) {
            return response()->json('success', 200);
        } else {
            return response()->json('error', 400);
        }
    }

    // 本部見積もり案件の見積書以外の添付ファイルアップロード（dropzone)
    public function ajaxDeleteFile(Request $request)
    {
        $input = $request->all();
        $resutlt = \Storage::disk('s3')->delete("/quotations/$input[id]/files/$input[filename]");
        if ($resutlt) {
            return response()->json('success', 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function adminMailDispatch(AdminQuotationRequest $request)
    {
        $posts = $request->all();
        $contact = Contact::where('id', $posts['contact_id'])->first();
        $posts['name'] = isCompany($contact) ? $posts['name'].'御中' : $posts['name'].'様';
        if (config('app.env') !== 'circleci' && !empty($posts['email'])) {
            Mail::to($posts['email'])->send(new SendAdminQuotationMail($posts));
        }
        // メールを本部にも送信
        if (\App::environment('production')) {
            $email = 'bcc@shintou-s.jp';
            Mail::to($email)->send(new SendAdminQuotationMail($posts));
        }
        Contact::where('id', $posts['contact_id'])->update(['step_id' => self::STEP_RESULT]);
        // 送信完了したら、添付ファイルを削除
        // filesがないとエラーになるので、make
        \Storage::disk('s3')->makeDirectory('/quotations/'.$posts['contact_id'].'/files');
        \Storage::disk('s3')->deleteDirectory('/quotations/'.$posts['contact_id'].'/files');

        return redirect(route('quotations.pending.list'))->with('success', '顧客に見積書をメール送信しました。返答があり次第、商談の結果を入力しましょう');
    }

    public function ajaxPdfParse(Request $request)
    {
      $pdf = $request->file('file');
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdf);
        $text = $pdf->getText();
        // 余分なスペースを削除
        $text = str_replace(array(" ", "　"), "", $text);
        // 改行の特殊文字を抽出
        $text = str_replace(["\r\n", "\r", "\n"], "\n", $text);
        // 配列に変換
        $array = explode("\n", $text);
        // さらにスペース分を子配列に変換
        foreach($array as $key => $a){
            $array[$key] = preg_split('/\s+/', $a);
            // 子要素の空文字プロパティを削除
            foreach($array[$key] as $ckey => $crc){
                if($crc === ""){
                    Arr::forget($array[$key], $ckey);
                }
            }
        }

        return response()->json($array);
    }


    public function pendingList()
    {
        $this->breadcrumbs->addCrumb('本部見積もり商談結果待ち');
        $breadcrumbs = $this->breadcrumbs;

        if (Contact::where('step_id', self::STEP_RESULT)->where('user_id', 1)->exists()) {
            $contacts = Contact::select('contacts.*')
              ->where('contacts.step_id', self::STEP_RESULT)
              ->where('contacts.user_id', 1)
              ->orderBy('contacts.created_at', 'DESC')
              ->paginate(20);
        } else {
            $contacts = '該当するお問い合わせは存在しません。';
        }

        return view('share/contact/pending-list', compact('breadcrumbs', 'contacts'));
    }

    public function getJsonQuotation($contactId)
    {
        $quotation = Contact::select('q.*')
            ->join('quotations as q', 'contacts.quotation_id', '=', 'q.id')
            ->find($contactId);

        $items = [];
        if($quotation->type == 0){
            $items = ProductQuotation::where('quotation_id', $quotation['id'])->where('status', 1)->orderBy('id', 'ASC')->get();
        }else{
            $items = ProductQuotationMaterial::where('quotation_id', $quotation['id'])->where('status', 1)->orderBy('id', 'ASC')->get();
        }

        return response()->json($items);
    }
}
