<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Requests\ContactVisitReportRequest;
use App\Http\Requests\CustomCsvRequest;
use App\Mail\SendTestMail;
use App\Mail\SendUserAssignMail;
use App\Mail\SendRequestQuotationMail;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Quotation;
use App\Models\SameCustomerContact;
use App\Models\Step;
use App\Models\Transaction;
use App\Models\User;
use App\Models\CsvExportOptions;
use App\Models\Prefecture;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use Mail;
use Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends MyController
{
    const PERSONAL_SAMPLE = 2191;
    const PERSONAL_DRAW = 2186;
    const PERSONAL_VISIT = 2190;
    const PERSONAL_ETC = 2210;
    const COMPANY_SAMPLE = 2192;
    const COMPANY_DRAW = 2194;
    const COMPANY_VISIT = 2193;
    const COMPANY_ETC = 2213;

    public function __construct(Contact $contact)
    {
        parent::__construct();
        $this->model = $contact;
        $this->breadcrumbs->addCrumb('<i class="fas fa-address-book"></i>お問い合わせ管理', '');
    }

    public function assign($id, $distance = 50)
    {
        $user = $this->user;
        $case = $this->model->findById($id);
        $this->breadcrumbs->addCrumb('FCを選択', 'assign/{id}/{distance?}')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $api_key = config('app.google_api_key');
        $address = $case->pref . $case->city . $case->street;
        $address = urlencode($address);
        $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}");
        $jsonData = json_decode($geocode, true);
        \Log::debug(print_r($jsonData, true));
        if ($jsonData['status'] == 'ZERO_RESULTS') {
            return redirect()->route('contact.show', ['id' => $id])->with('danger', 'この顧客住所は存在しない住所です。正しい住所に編集してください。');
        }
        $case->latitude = $jsonData['results'][0]['geometry']['location']['lat'];
        $case->longitude = $jsonData['results'][0]['geometry']['location']['lng'];

        $userModel = new User();
        $franchises = $userModel->getNearFcs($case->latitude, $case->longitude, $distance);
        $recommendFc = Arr::first($franchises);

        return view('admin.contact.assign', compact('breadcrumbs', 'user', 'case', 'franchises', 'recommendFc', 'distance'));
    }

    public function assignCommit(Request $request)
    {
        $post = $request->all();
        // 訪問見積もりと図面見積もりで分岐
        if (returnContactTypeId($post['id']) == '2' || returnContactTypeId($post['id']) == '6') {
            $next_step = self::STEP_QUOTATION;
        } else {
            $next_step = self::STEP_APPOINT;
        }
        $this->model->where('id', $post['id'])->update(['step_id' => $next_step, 'user_id' => $post['fcid'], 'fc_assigned_at' => date("Y/m/d H:i:s")]);

        $fc = User::where('id', $post['fcid'])->first();
        $contact = Contact::where('id', $post['id'])->first();

        //メールを送る処理
        if (isAllowEmailUser($post['fcid'])) {
            Mail::to($fc['email'])->send(new SendUserAssignMail($contact));
            if (!empty($fc['email2'])) {
                Mail::to($fc['email2'])->send(new SendUserAssignMail($contact));
            }
            if (!empty($fc['email3'])) {
                Mail::to($fc['email3'])->send(new SendUserAssignMail($contact));
            }
        }

        return redirect('/')->with('success', 'FCに見積もりを依頼しました');
    }

    public function store(Request $request)
    {
        $post = $request->all();
        \Log::debug('WPからのPOST削除前');
        \Log::debug(print_r($post, true));
        if (config('app.env') == 'testing' || config('app.env') == 'production') {
            Arr::forget($post, 'g-recaptcha-response');
        }
        $id = 0;
        switch ($post['_wpcf7']) {
            case self::PERSONAL_SAMPLE:
                $id = $this->model->personalSample($post);
                $post['pref'] = $post['pref_kojin_sample'];
                $post['city'] = $post['addr1_kojin_sample'];
                $post['street'] = $post['addr2_kojin_sample'];
                $post['email'] = $post['kojin_email_sample'];
                $post['tel'] = $post['kojin_tel_sample'];
                $post['surname'] = $post['kojin_name_sample_sei'];
                $post['name'] = $post['kojin_name_sample_mei'];
                $post['contact_type_id'] = 1;
                break;
            case self::PERSONAL_DRAW:
                $id = $this->model->personalDraw($post);
                $post['pref'] = $post['pref_kojin_zumen'];
                $post['city'] = $post['addr1_kojin_zumen'];
                $post['street'] = $post['addr2_kojin_zumen'];
                $post['email'] = $post['kojin_email_zumen'];
                $post['tel'] = $post['kojin_tel_zumen'];
                $post['surname'] = $post['kojin_name_zumen_sei'];
                $post['name'] = $post['kojin_name_zumen_mei'];
                $post['contact_type_id'] = 2;
                break;
            case self::PERSONAL_VISIT:
                $id = $this->model->personalVisit($post);
                $post['pref'] = $post['pref_kojin_houmon'];
                $post['city'] = $post['addr1_kojin_houmon'];
                $post['street'] = $post['addr2_kojin_houmon'];
                $post['email'] = $post['kojin_email_houmon'];
                $post['tel'] = $post['kojin_tel_houmon'];
                $post['surname'] = $post['kojin_name_houmon_sei'];
                $post['name'] = $post['kojin_name_houmon_mei'];
                $post['contact_type_id'] = 3;
                break;
            case self::PERSONAL_ETC:
                $id = $this->model->personalEtc($post);
                $post['pref'] = $post['pref_kojin_other'];
                $post['city'] = $post['addr1_kojin_other'];
                $post['street'] = $post['addr2_kojin_other'];
                $post['email'] = $post['kojin_email_other'];
                $post['tel'] = $post['kojin_tel_other'];
                $post['surname'] = $post['kojin_name_other_sei'];
                $post['name'] = $post['kojin_name_other_mei'];
                $post['contact_type_id'] = 4;
                break;
            case self::COMPANY_SAMPLE:
                $id = $this->model->companySample($post);
                $post['pref'] = $post['pref_hojin_sample'];
                $post['city'] = $post['addr1_hojin_sample'];
                $post['street'] = $post['addr2_hojin_sample'];
                $post['email'] = $post['hojin_email_sample'];
                $post['tel'] = $post['hojin_tel_sample'];
                $post['company_name'] = $post['hojin_company_name_sample'];
                $post['contact_type_id'] = 5;
                break;
            case self::COMPANY_DRAW:
                $id = $this->model->companyDraw($post);
                $post['pref'] = $post['pref_hojin_zumen'];
                $post['city'] = $post['addr1_hojin_zumen'];
                $post['street'] = $post['addr2_hojin_zumen'];
                $post['email'] = $post['hojin_email_zumen'];
                $post['tel'] = $post['hojin_tel_zumen'];
                $post['company_name'] = $post['hojin_company_name_zumen'];
                $post['contact_type_id'] = 6;
                break;
            case self::COMPANY_VISIT:
                $id = $this->model->companyVisit($post);
                $post['pref'] = $post['pref_hojin_houmon'];
                $post['city'] = $post['addr1_hojin_houmon'];
                $post['street'] = $post['addr2_hojin_houmon'];
                $post['email'] = $post['hojin_email_houmon'];
                $post['tel'] = $post['hojin_tel_houmon'];
                $post['company_name'] = $post['hojin_company_name_houmon'];
                $post['contact_type_id'] = 7;
                break;
            case self::COMPANY_ETC:
                $id = $this->model->companyEtc($post);
                $post['pref'] = $post['pref_hojin_other'];
                $post['city'] = $post['addr1_hojin_other'];
                $post['street'] = $post['addr2_hojin_other'];
                $post['email'] = $post['hojin_email_other'];
                $post['tel'] = $post['hojin_tel_other'];
                $post['company_name'] = $post['hojin_company_name_other'];
                $post['contact_type_id'] = 8;
                break;
            default:
                // code...
                break;
        }
        $this->insertSameCustomer($post, $id);

        return $id;
    }

    private function insertSameCustomer($post, $contactId)
    {
        $post['street'] =
            $sameCustomers = $this->model->findSameCustomer($post, $contactId);
        \Log::debug(print_r($sameCustomers, true));
        $sameCustomerContact = new SameCustomerContact();
        $sameCustomerContact->contactInsert($contactId, $sameCustomers);
    }

    public function unassignedList()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('FC未振り分け一覧', 'contact/unassigned/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $query = Contact::query()->whereIn('status', [1, 3])
            ->where('step_id', self::STEP_ASSIGN)
            ->where('user_id', null)
            ->where('cancel_step', null)
            ->whereNotIn('contact_type_id', [1, 5])
            ->orderBy('created_at', 'DESC');
        // if (isProduction()) {
        //     $query->where('id', '>', 24925);
        // }
        $contacts = $query->paginate(50);

        //$contactTypes = 複数形なので、問い合わせ全種類を取得
        $contactTypes = ContactType::select('id', 'name')->get();

        return view('share.contact.unassigned-list', compact('contacts', 'contactTypes', 'breadcrumbs'));
    }

    public function unassignedSearch(Request $request)
    {
        $user = $this->user;

        $this->breadcrumbs->addCrumb('FC未振り分け一覧', 'contact/unassigned/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        //$contactTypes = 複数形なので、問い合わせ全種類を取得
        $contactTypes = ContactType::select('id', 'name')->get();

        $type = $request->input('type');
        //$typeOfContact = 単数形なので、限定された問い合わせ1種類を取得
        $typeOfContact = $request->input('contact_type');
        $comment = $request->input('comment');

        $query = Contact::query()->select('contacts.*', 'contacts.name AS name', 'contact_types.name AS type_of_contact')
            ->leftJoin('contact_types', 'contacts.contact_type_id', '=', 'contact_types.id')
            ->whereIn('status', [1, 3])
            ->where('step_id', self::STEP_ASSIGN);

        if (!is_null($type)) {
            $query->where('type', $type);
        }
        if (!is_null($typeOfContact)) {
            $query->where('contacts.contact_type_id', $typeOfContact);
        }
        if (!is_null($comment)) {
            $query->where('comment', 'LIKE', '%' . $comment . '%');
        }

        $contacts = $query->orderBy('contacts.created_at', 'DESC')->paginate(50)
            ->appends(request()->query());

        return view('share.contact.unassigned-list', compact('contacts', 'contactTypes', 'type', 'typeOfContact', 'breadcrumbs'));
    }

    public function show($id)
    {
        $this->breadcrumbs->addCrumb('案件詳細', 'contact/unassigned/' . $id)->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $contact = $this->model->select(
            'contacts.*',
            'u.name AS fc_name',
            'u.id AS fcid',
            'ct.name AS contact_type_name',
            'steps.name AS step_name',
            'ru.id AS registered_user_id',
            'ru.name AS registered_user_name'
        )
            ->where('contacts.id', $id)
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
            ->leftJoin('users AS ru', 'contacts.registered_user_id', '=', 'ru.id')
            ->leftJoin('contact_types AS ct', 'ct.id', '=', 'contacts.contact_type_id')
            ->leftJoin('steps', 'steps.id', '=', 'contacts.step_id')
            ->first();

        if (empty($contact)) {
            return redirect(route('contact.customers'))->with('danger', 'この案件は存在しません');
        }
        $quotations = Quotation::where('contact_id', $id)->where('user_id', $contact->user_id)->get();

        $auths = Auth::user();
        if (isFc() && ($auths->id != $contact->user_id)) {
            return redirect()->route('assigned.list')->with('warning', '他FCの案件にはアクセスできません。');
        }
        $same_customer_contacts_id = SameCustomerContact::select('contact_id')->where('ref_contact_id', $id)->orderBy('contact_id', 'ASC')->get()->toArray();
        // 一覧に戻るの位置
        $query = $this->model->query()->select('contacts.*', 's.name AS step_name')
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('steps AS s', 's.id', '=', 'contacts.step_id')
            ->orderBy('contacts.id', 'DESC');

        if (isFC()) {
            $query->where('user_id', \Auth::id());
        }

        $indexPosition = floor($query->count() / 200);
        $indexUrl = '?page=' . $indexPosition . '#' . $id;

        if (($contact['contact_type_id'] == 2 || $contact['contact_type_id'] == 6) && $contact['step_id'] > self::STEP_APPOINT) {
            $switchContactType = true;
        } else {
            $switchContactType = false;
        }

        //訪問見積もりで進行中同一顧客がいるか
        $sameCustomers = $this->model->findSameCustomer($contact, $id)->toArray();
        \Log::debug(print_r($sameCustomers, true));
        $isOnSite = false; //訪問見積もりのフラグ
        foreach ($sameCustomers as $sc) {
            if (($sc['contact_type_id'] == 3 || $sc['contact_type_id'] == 7) && ($sc['step_id'] < 11) && is_null($contact['cancel_step'])) {
                $isOnSite = true;
                break;
            }
        }
        $existenceMainFc = !is_null($contact['main_user_id']) ? true : false;
        $isMainFc = ($contact['user_id'] === $contact['main_user_id'] || !$existenceMainFc) ? true : false;

        // FCが初めて確認した時
        try {
            if (!isAdmin() && is_null($contact['fc_confirmed_at'])) {
                $now = new Carbon();
                Contact::where('id', $contact['id'])->update(['fc_confirmed_at' => $now->format('Y-m-d H:i:s')]);
            } elseif (is_null($contact['fc_confirmed_at'])) {
                if (Auth::id() != 1) {
                    toSlack($url = env('SLACK_WEBHOOK_URL', ''), $channel = env('SLACK_DEV_CHANNEL', '#sample-dev'), $message = "!isAdmin*(がおかしい user_id:" . Auth::id() . ") contact_id = " . $contact['id']);
                }
            }
        } catch (QueryException $e) {
            // ここでエラー処理を行います。
            // 例：ログにエラーメッセージを書き出す
            \Log::error('データベースの更新中にエラーが発生しました：' . $e->getMessage());
            // 必要に応じてユーザーにエラーメッセージを表示するなどの処理を追加できます。
            toSlack($url = env('SLACK_WEBHOOK_URL', ''), $channel = env('SLACK_DEV_CHANNEL', '#sample-dev'), $message = "fc_confirmed_atの更新に失敗しました。" . $e->getMessage());
        }

        return view('share.contact.detail', compact('contact', 'breadcrumbs', 'id', 'quotations', 'same_customer_contacts_id', 'indexUrl', 'isOnSite', 'isMainFc', 'existenceMainFc', 'switchContactType'));
    }

    public function assignedList()
    {
        $user = $this->user;
        if (isAdmin()) {
            $this->breadcrumbs->addCrumb('FC依頼済み一覧', 'assigned/list')->setLastItemWithHref(true);
        } else {
            $this->breadcrumbs->addCrumb('見積もり要請案件一覧（要アポイントメント）', 'assigned/list')->setLastItemWithHref(true);
        }
        $breadcrumbs = $this->breadcrumbs;

        if (isAdmin()) {
            $contacts = Contact::select('contacts.*', 'u.name AS fc_name', 'u.id AS fcid')
                ->where('user_id', '!=', null)
                ->whereIn('contacts.status', [1, 3])
                ->where('contacts.step_id', self::STEP_APPOINT)
                ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
                ->orderBy('contacts.updated_at', 'DESC')
                ->paginate(50);
        } else {
            $contacts = Contact::select('contacts.*', 'nt.period AS alert_days', 'un.read')
                ->whereIn('contacts.status', [1, 3])
                ->where('contacts.user_id', $user->id)
                ->where('contacts.step_id', self::STEP_APPOINT)
                ->leftJoin('notification_types AS nt', 'nt.step_id', '=', 'contacts.step_id')
                ->leftJoin('user_notifications AS un', 'un.contact_id', '=', 'contacts.id')
                ->groupBy('contacts.id')
                ->orderBy('contacts.updated_at', 'DESC')
                ->paginate(20);
        }
        $contactTypes = ContactType::select('id', 'name')->get();

        return view('share.contact.assigned-list', compact('contacts', 'contactTypes', 'breadcrumbs'));
    }

    public function assignedSearch(Request $request)
    {
        $user = $this->user;
        if (isAdmin()) {
            $this->breadcrumbs->addCrumb('FC依頼済み一覧', 'contacnt/assigned/list')->setLastItemWithHref(true);
        } else {
            $this->breadcrumbs->addCrumb('見積もり要請案件一覧（要アポイントメント）', 'contact/assigned/list')->setLastItemWithHref(true);
        }
        $breadcrumbs = $this->breadcrumbs;

        //$contactTypes = 複数形なので、問い合わせ全種類を取得
        $contactTypes = ContactType::select('id', 'name')->get();

        //$typeOfContact = 単数形なので、問い合わせ1種類を取得
        $typeOfContact = $request->input('contact_type');
        $comment = $request->input('comment');
        $type = $request->input('type');

        if (isAdmin()) {
            $query = Contact::query()->select('contacts.*', 'contact_types.name AS type_of_contact')
                ->leftJoin('contact_types', 'contacts.contact_type_id', '=', 'contact_types.id')
                ->whereIn('contacts.status', [1, 3])
                ->where('step_id', self::STEP_ASSIGN);

            if (!is_null($type)) {
                $query->where('type', $type);
            }
            if (!is_null($typeOfContact)) {
                $query->where('contacts.contact_type_id', $typeOfContact);
            }
            if (!is_null($comment)) {
                $query->where('comment', 'LIKE', '%' . $comment . '%');
            }

            $contacts = $query->paginate(50)
                ->appends(request()->query());
        } else {
            $query = Contact::query()->select('contacts.*', 'contacts.name AS name', 'contact_types.name AS type_of_contact')
                ->leftJoin('contact_types', 'contacts.contact_type_id', '=', 'contact_types.id')
                ->whereIn('contacts.status', [1, 3])
                ->where('role', 2)
                ->where('step_id', self::STEP_ASSIGN);

            if (!is_null($type)) {
                $query->where('type', $type);
            }
            if (!is_null($typeOfContact)) {
                $query->where('contacts.contact_type_id', $typeOfContact);
            }
            if (!is_null($comment)) {
                $query->where('comment', 'LIKE', '%' . $comment . '%');
            }
        }

        $contacts = $query->orderBy('contacts.created_at', 'DESC')->paginate(50)
            ->appends(request()->query());

        return view('share.contact.assigned-list', compact('contacts', 'breadcrumbs', 'contactTypes'));
    }

    public function assignedEdit($id)
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('案件詳細編集', '/contact/assigned/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $contact = $this->model->select('contacts.*', 'u.name AS user_name', 's.name AS step_name')
            ->where('contacts.id', $id)
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
            ->leftJoin('steps AS s', 'contacts.step_id', '=', 's.id')
            ->first();
        if (isFc() && (Auth::id() != $contact->user_id)) {
            return redirect()->route('assigned.list')->with('warning', '他FCの案件にはアクセスできません。');
        }
        if (!isAdmin() && is_null($contact['fc_confirmed_at'])) {
            $now = new Carbon();
            Contact::where('id', $contact['id'])->update(['fc_confirmed_at' => $now->format('Y-m-d H:i:s')]);
        }
        $contactTypes = ContactType::all();
        $fcs = User::where('status', 1)->orderBy('id', 'ASC')->get();
        $steps = Step::where('status', 1)->get();
        $sameIds = SameCustomerContact::select('contact_id')->where('ref_contact_id', $id)->orderBy('contact_id', 'ASC')->get()->toArray();
        $same_customer_contacts_id = null;
        if (!empty($sameIds)) {
            foreach ($sameIds as $key => $val) {
                $same_customer_contacts_id .= $val['contact_id'];
                if (next($sameIds)) {
                    // 最後の要素ではないとき
                    $same_customer_contacts_id .= ',';
                }
            }
        }

        return view('share.contact.edit', compact('contact', 'breadcrumbs', 'contactTypes', 'steps', 'same_customer_contacts_id', 'fcs'));
    }

    public function contactUpdate(ContactUpdateRequest $request, $id)
    {
        $contact = $this->model->findOrFail($id);

        $inputs = $request->all();
        // FCの場合ステップがレンダリングされない場合があるので、$contactから取得
        $inputs['c']['step_id'] = !empty($inputs['c']['step_id']) ? $inputs['c']['step_id'] : $contact['step_id'];

        if ($inputs['c']['step_id'] == self::STEP_CANCELATION && empty($contact->cancel_step)) {
            $addCancelStep = $inputs['c']['cancel_step'] = $contact->step_id;
        } elseif (!empty($inputs['c']['cancel_step'])) {
            $inputs['c']['cancel_step'] = null;
        }
        //法人案件の場合 名前部分 と 名前ふりがな部分 を nullにする
        if ($inputs['c']['contact_type_id'] > 4) {
            $inputs['c']['name'] = null;
            $inputs['c']['name_ruby'] = null;
        }
        if(array_key_exists('sample_send_at', $inputs['c'])){
            if(isFc()){
                $inputs['c']['sample_send_at'] = $inputs['c']['sample_send_at'] === '1970-01-01' ? $inputs['c']['sample_send_at'] : NULL;
            }
        }
        // FC自己登録案件なら、ステップ動かす
        // もしFCが自分でサンプル案件として登録した案件を訪問見積もりに変えた場合→アポ取り未完了一覧
        // 図面見積もりに変えた場合→見積未作成一覧（アポ入力・現場報告をスキップした状態で見積作成から始められる）
        // ※訪問見積もりに種別を変更した場合アポ取り未完了一覧から再スタートできる
        if ($contact['contact_type_id'] != $inputs['c']['contact_type_id'] &&  $contact['own_contact'] == 1 && $contact['step_id'] <= self::STEP_QUOTATION && isFc()) {
            switch ($inputs['c']['contact_type_id']) {
                    // 図面見積もり
                case 2:
                case 6:
                    // 図面見積もりかつ、変更元がサンプル請求だった場合
                    if ($contact['contact_type_id'] == 1 || $contact['contact_type_id'] == 5) {
                        $inputs['c']['step_id'] = self::STEP_QUOTATION;
                    }
                    break;
                    // 訪問見積もり
                case 3:
                case 7:
                    $inputs['c']['step_id'] = self::STEP_APPOINT;
                    break;
            }
        }

        /* メール自動送信の処理 */
        if ($contact['own_contact'] == 1 && $contact['step_id'] == self::STEP_ASSIGN) {
            //var_dump("自己獲得案件かつSTEP=1の案件です");
        } else if (isAdmin() && $contact['step_id'] == self::STEP_APPOINT) {
            //var_dump("本部アカウントかつSTEP=2の案件を更新しました");
            //if (config('app.env') !== 'circleci') {
            //    Mail::to('shoooot@outlook.jp')->send(new SendRequestQuotationMail($inputs));
            //}
        }

        $this->model->transStart();
        try {
            // S3に施工前画像を登録
            for ($i = 1; $i < 4; ++$i) {
                if (!empty($inputs['c']['before_image' . $i]) || !empty($inputs['c']['before-image' . $i . '-state'])) {
                    if (array_key_exists('before_image' . $i, $inputs['c'])) {
                        $file = $inputs['c']['before_image' . $i];
                        // 拡張子の取得
                        $ext = getExtention($file->getClientOriginalName());
                        $path = Storage::disk('s3')->putFileAs("/images/before/$id", $file, Str::random(20) . '.' . $ext, 'public');
                        $pathExplodes = explode('/', $path);
                        $filename = last($pathExplodes);
                        $inputs['c']['before_image' . $i] = $filename;
                        //削除した場合はDBをnullに
                    } elseif ($inputs['c']['before-image' . $i . '-state'] == 'deleted') {
                        $inputs['c']['before_image' . $i] = null;
                    }
                }
                Arr::forget($inputs['c'], 'before-image' . $i . '-state');
                if (!empty($inputs['c']['after_image' . $i]) || !empty($inputs['c']['after-image' . $i . '-state'])) {
                    if (array_key_exists('after_image' . $i, $inputs['c'])) {
                        $file = $inputs['c']['after_image' . $i];
                        // 拡張子の取得
                        $ext = getExtention($file->getClientOriginalName());
                        // A sha256 checksum could not be calculated for the provided upload body, because it was not seekable. 対策
                        // Save the file locally in the tmp directory
                        $tempPath = $file->store('tmp');
                        // Get the absolute path to the file
                        $absolutePath = storage_path('app/' . $tempPath);
                        // Use putFileAs to upload the file then delete the temporary file
                        $path = Storage::disk('s3')->putFileAs("/images/after/$id", new File($absolutePath), Str::random(20) . '.' . $ext, 'public');
                        Storage::delete($tempPath);

                        // $path = Storage::disk('s3')->putFileAs("/images/after/$id", $file, Str::random(20).'.'.$ext, 'public');
                        $pathExplodes = explode('/', $path);
                        $filename = last($pathExplodes);
                        $inputs['c']['after_image' . $i] = $filename;
                        //削除した場合はDBをnullに
                    } elseif ($inputs['c']['after-image' . $i . '-state'] == 'deleted') {
                        $inputs['c']['after_image' . $i] = null;
                    }
                }
                Arr::forget($inputs['c'], 'after-image' . $i . '-state');
            }
            // S3に添付資料を5枚まで登録
            for ($i = 1; $i < 6; ++$i) {
                if (array_key_exists('document' . $i, $inputs['c'])) {
                    $file = $inputs['c']['document' . $i];
                    // 拡張子の取得
                    $ext = getExtention($file->getClientOriginalName());
                    $tempPath = $file->store('tmp');
                    // Get the absolute path to the file
                    $absolutePath = storage_path('app/' . $tempPath);
                    // Use putFileAs to upload the file then delete the temporary file
                    $path = Storage::disk('s3')->putFileAs("/documents/$id", new File($absolutePath), Str::random(20) . '.' . $ext, 'public');
                    Storage::delete($tempPath);
                    // $path = Storage::disk('s3')->putFileAs("/documents/$id", $file, Str::random(20).'.'.$ext, 'public');
                    $pathExplodes = explode('/', $path);
                    $filename = last($pathExplodes);
                    $inputs['c']['document' . $i] = $filename;
                    $inputs['c']['document' . $i . '_original_name'] = $file->getClientOriginalName();
                    //削除した場合はDBをnullに
                } elseif ($inputs['c']['document' . $i . '-state'] == 'deleted') {
                    $inputs['c']['document' . $i . '_original_name'] = null;
                    $inputs['c']['document' . $i] = null;
                }
                Arr::forget($inputs['c'], 'document' . $i . '-state');
            }
            // 担当FCが変わっていたら依頼と判断する
            if ($contact['user_id'] != $inputs['c']['user_id']) {
                $date = Carbon::now();
                $inputs['c']['fc_assigned_at'] = $date->toDateTimeString();
                //メールを送る処理
                $fc = User::find($inputs['c']['user_id']);
                if (isAllowEmailUser($inputs['c']['user_id'])) {
                    Mail::to($fc['email'])->send(new SendUserAssignMail($contact));
                    if (!empty($fc['email2'])) {
                        Mail::to($fc['email2'])->send(new SendUserAssignMail($contact));
                    }
                    if (!empty($fc['email3'])) {
                        Mail::to($fc['email3'])->send(new SendUserAssignMail($contact));
                    }
                }
            }
            // 本部がキャンセルに設定したらcontacts.cancel_stepに代入する
            if (isAdmin() && $inputs['c']['step_id'] == self::STEP_CANCELATION) {
                $inputs['c']['cancel_step'] = $contact['step_id'];
            }

            Contact::where('id', $inputs['c']['id'])->update($inputs['c']);

            // 同一顧客
            if (isAdmin()) {
                $same = new SameCustomerContact();
                $same->contactUpdate($inputs['c']['id'], $inputs['sc']['ids']);
            }
            $this->model->transCommit();

            return redirect()->route('contact.show', ['id' => $id])->with('success', 'お問い合わせ内容が更新されました！');
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();

            return redirect()->route('dashboard')->with('danger', '登録エラーが発生しました。再度実行してください。解決しない場合は開発者にご連絡ください。');
        }
    }

    //訪問見積もりに変更
    public function switchContactType($id)
    {
        $contact = $this->model->findOrFail($id);

        if ($contact['contact_type_id'] == 2) {
            $this->model->where('id', $id)->update(['contact_type_id' => 3, 'step_id' => 2, 'status' => 1]);
        } elseif ($contact['contact_type_id'] == 6) {
            $this->model->where('id', $id)->update(['contact_type_id' => 7, 'step_id' => 2, 'status' => 1]);
        }

        return redirect()->route('contact.show', ['id' => $id])->with('success', '図面見積もりを訪問見積もりに変更しました。');
    }

    /* 案件一覧 */
    public function customersList(Request $request)
    {
        $this->breadcrumbs->addCrumb('案件一覧', 'contact/customers/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $users_query = User::query()->orderBy('role', 'ASC')->orderByRaw('FIELD(status, 1, 3, 4, 2)')->orderBy('id', 'DESC');
        if (config('app.env') === 'production') {
            $users_query->where('name', 'NOT LIKE', '%テスト%');
        }
        $users = $users_query->get();
        $types = ContactType::all();
        $prefectures = Prefecture::all();
        $contact_types = ContactType::all();
        $steps = STEP::whereBetween('id', [1, 11])->where('status', 1)->get();

        $csv_options = CsvExportOptions::get();

        $select = $request->input('selectData');

        $inputs = $request->all();

        $query = $this->model->query()->select('contacts.*', 's.name AS step_name')
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('steps AS s', 's.id', '=', 'contacts.step_id')
            ->orderBy('contacts.id', 'DESC');

        //案件の絞り込み(本部のみ)
        $this->model->filteringContact($request, $query);

        if (isFC()) {
            $query->where('contacts.user_id', \Auth::id());
        }
        //本部案件のみフィルター
        if ($select == 'admin') {
            $query->where('contacts.own_contact', 0);
        }

        $customers = $query->paginate(100);

        return view('share.contact.customers-list', compact('breadcrumbs', 'customers', 'select', 'inputs', 'users', 'types', 'csv_options', 'prefectures', 'contact_types', 'steps'));
    }

    /* 顧客検索 */
    public function customersSearch(Request $request)
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('案件一覧', 'contact/customers/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $users = User::all();
        $contact_types = ContactType::all();
        $steps = STEP::where('status', 1)->get();
        $csv_options = CsvExportOptions::get();

        $number = $request->input('number');
        $name = $request->input('name');
        $address = $request->input('address');
        $tell = $request->input('tell');

        $query = $this->model->query()->select('contacts.*', 's.name AS step_name')
            ->whereIn('contacts.status', [1, 3])
            ->leftJoin('steps AS s', 's.id', '=', 'contacts.step_id')
            ->orderBy('contacts.id', 'DESC');

        if (isFC()) {
            $query->where('contacts.user_id', \Auth::id());
        }
        if (!is_null($number)) {
            $query->where('contacts.id', $number);
        }
        if (!is_null($name) || !is_null($address) || !is_null($tell)) {
            $query = $this->model->customerOrSearch($query, $name, $address, $tell);
        }

        $customers = $query->paginate(20);
        $prefectures = Prefecture::all();

        $inputs = $request->all();

        return view('share.contact.customers-list', compact('breadcrumbs', 'customers', 'inputs', 'users', 'contact_types', 'csv_options', 'prefectures', 'steps'));
    }

    public function setAppointment(Request $request)
    {
        $inputs = $request->all();
        //dd($inputs);
        $visitDateTime = $inputs['date'] . ' ' . sprintf('%02d', $inputs['hours']) . ':' . sprintf('%02d', $inputs['minutes']) . ':00';

        $contact = Contact::where('id', $inputs['id'])->update([
            'visit_time' => $visitDateTime,
            'step_id' => self::STEP_ONSITE_CONFIRM,
        ]);

        //dd($contact);

        return redirect()->route('contact.show', ['id' => $inputs['id']])->with('success', 'アポイント日時を登録しました。実際に訪問して現場報告を行ってください。');
    }

    public function skipOnsiteConfirmation(Request $request)
    {
        $input = $request->id;

        $contact = Contact::where('id', $input)->update(['step_id' => self::STEP_QUOTATION]);

        return redirect()->route('quotations.create', ['id' => $input])->with('success', '現場報告をスキップしました。見積もりを作成してください。');
    }

    public function getOwnContactForm()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('自己獲得案件登録', '/contact/fc/new')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $contactTypes = ContactType::all();

        return view('fc.contact.own-get-form', compact('contactTypes', 'breadcrumbs'));
    }

    public function postOwnContactForm(Request $request)
    {
        $inputs = $request->all();
        $inputs['c']['user_id'] = $this->user->id;
        $inputs['c']['step_id'] = self::STEP_APPOINT;

        $ownContact = $this->model->insertGetId($inputs['c']);

        return redirect()->route('assigned.list')->with('success', '新しいお問い合わせを登録しました。');
    }

    // 手動案件登録
    public function getContactForm(Request $request)
    {
        $user = $this->user;
        $copy = $request->input('copy');
        if (empty($copy)) {
            $this->breadcrumbs->addCrumb(isAdmin() ? '<i class="fas fa-phone"></i>新規顧客登録' : '<i class="fas fa-user-friends"></i>自己獲得案件登録', '/contact/new')->setLastItemWithHref(true);
            $copyData = null;
        } else {
            $this->breadcrumbs->addCrumb('案件コピー');
            $copyData = Contact::where('id', $copy)->first();
        }
        $breadcrumbs = $this->breadcrumbs;

        $contactTypes = ContactType::orderBy('id', 'ASC')->get()->toArray();
        $firstContactType = array_slice($contactTypes, 0, 1);
        $contactTypes = array_slice($contactTypes, 1);

        return view('share.contact.tel-registar', compact('contactTypes', 'firstContactType', 'breadcrumbs', 'copyData'));
    }

    // 手動案件登録(tel register)
    public function postContactForm(ContactUpdateRequest $request)
    {
        $inputs = $request->all();

        //お問い合わせ種別未入力のバリデーション
        //未入力なら0が返り、エラー
        $rules = [
            'contact_type_id' => 'numeric|between:1,8',
            'zipcode' => 'required',
        ];
        if (preg_match('/^ *$/', $inputs['c']['tel'])) {
            $rules['c.tel'] = 'required | min:9';
        }
        $validator = \Validator::make($inputs['c'], $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // =========ここから文字列整形===============
        // 使用用途の整形
        if (!empty($inputs['c']['use_application'])) {
            $inputs['c']['use_application'] = $this->model->stringFormatting($inputs['c']['use_application']);
        } else {
            $inputs['c']['use_application'] = null;
        }
        // 認知経路の整形
        if (!empty($inputs['c']['where_find'])) {
            $inputs['c']['where_find'] = $this->model->stringFormatting($inputs['c']['where_find']);
        } else {
            $inputs['c']['where_find'] = null;
        }
        // SNSの整形
        if (!empty($inputs['c']['sns'])) {
            $inputs['c']['sns'] = $this->model->stringFormatting($inputs['c']['sns']);
        } else {
            $inputs['c']['sns'] = null;
        }
        // 下地状況の整形
        if (!empty($inputs['c']['ground_condition'])) {
            $inputs['c']['ground_condition'] = $this->model->stringFormatting($inputs['c']['ground_condition']);
        } else {
            $inputs['c']['ground_condition'] = null;
        }
        // 希望商品の整形
        if (!empty($inputs['c']['desired_product'])) {
            $inputs['c']['desired_product'] = $this->model->stringFormatting($inputs['c']['desired_product']);
        } else {
            $inputs['c']['desired_product'] = null;
        }
        // 訪問先日時の整形
        if (!empty($inputs['c']['desired_datetime1']['date']) || !empty($inputs['c']['desired_datetime1']['time'])) {
            $inputs['c']['desired_datetime1'] = $inputs['c']['desired_datetime1']['date'] . ' ' . sprintf('%02d:00:00', $inputs['c']['desired_datetime1']['time']);
        } else {
            $inputs['c']['desired_datetime1'] = null;
        }
        // 訪問先日時2の整形
        if (!empty($inputs['c']['desired_datetime2']['date']) || !empty($inputs['c']['desired_datetime2']['time'])) {
            $inputs['c']['desired_datetime2'] = $inputs['c']['desired_datetime2']['date'] . ' ' . sprintf('%02d:00:00', $inputs['c']['desired_datetime2']['time']);
        } else {
            $inputs['c']['desired_datetime2'] = null;
        }
        // 訪問先日時の整形
        if (!empty($inputs['c']['quote_details']) && is_array($inputs['c']['quote_details'])) {
            $inputs['c']['quote_details'] = $this->model->stringFormatting($inputs['c']['quote_details']);
        }
        // =========ここまで文字列整形===============

        $now = new Carbon();
        $inputs['finished_datetime'] = null;
        if (isFc()) {
            $inputs['c']['user_id'] = \Auth::id();
            $inputs['c']['own_contact'] = 1;
            $inputs['c']['fc_confirmed_at'] = $now->format('Y-m-d H:i:s');
        } else {
            $inputs['c']['user_id'] = null;
            $inputs['c']['own_contact'] = 0;
        }

        switch ($inputs['c']['contact_type_id']) {
            case 1:
            case 4:
            case 5:
            case 8:
                $inputs['c']['step_id'] = self::STEP_ASSIGN;
                $redirect = 'contact.customers';
                break;
            case 2:
            case 6:
                $inputs['c']['step_id'] =  isAdmin() ? self::STEP_ASSIGN : self::STEP_QUOTATION;
                $redirect = 'quotations.needs';
                break;
            case 3:
            case 7:
                $inputs['c']['step_id'] = isAdmin() ? self::STEP_ASSIGN : self::STEP_APPOINT;
                $redirect = 'assigned.list';
                break;
            default:
                break;
        }

        if (isAdmin() || !empty($inputs['c']['sample_send_at'])) {
            $inputs['c']['sample_send_at'] = null;
        } else {
            $inputs['c']['sample_send_at'] = '1970-01-01';
        }

        //本部に一任する場合
        if (!empty($inputs['leave_to_admin'])) {
            $inputs['c']['sample_send_at'] = null;
            $inputs['c']['own_contact'] = 0;
            $inputs['c']['user_id'] = null;
            $inputs['c']['step_id'] = self::STEP_ASSIGN;
            $inputs['c']['registered_user_id'] = Auth::id();
        }

        /* 条件が一致したらsame_customer_contactsにデータを入れる処理 */
        $this->model->transStart();
        try {
            //追加される案件のid
            $contactId = Contact::insertGetId($inputs['c']);
            // S3に資料を5枚まで登録
            for ($i = 1; $i < 6; ++$i) {
                if (array_key_exists('document' . $i, $inputs['c'])) {
                    $file = $inputs['c']['document' . $i];
                    // 拡張子の取得
                    $ext = getExtention($file->getClientOriginalName());
                    $path = Storage::disk('s3')->putFileAs("/documents/$contactId", $file, Str::random(20) . '.' . $ext, 'public');
                    $pathExplodes = explode('/', $path);
                    $filename = last($pathExplodes);
                    $inputs['c']['document' . $i] = $filename;
                    $inputs['c']['document' . $i . '_original_name'] = $file->getClientOriginalName();
                }
            }
            // 画像のパス（S3）はアップロードしてから決まるので、S3のURLだけ更新
            Contact::where('id', $contactId)->update($inputs['c']);
            $sameCustomers = $this->model->findSameCustomer($inputs['c'], $contactId);

            foreach ($sameCustomers as $sc) {
                if ($sc['main_user_id'] != null && $sc['status'] == '1' && intval($sc['step_id']) < self::STEP_COMPLETE) {
                    Contact::where('id', $contactId)->update(['main_user_id' => $sc['main_user_id']]);
                    break;
                }
            }

            $sameCustomerContact = new SameCustomerContact();
            $sameCustomerContact->contactInsert($contactId, $sameCustomers);

            if (!empty($refId[0])) {
                SameCustomerContact::insert(['ref_contact_id' => $refId[0]['id'], 'contact_id' => $contactId]);
            }

            $this->model->transCommit();

            return redirect()->route(isAdmin() ? 'contact.customers' : $redirect)->with('success', '案件No' . $contactId . 'として、新しいお問い合わせを登録しました。');
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->model->transRollback();
        }
    }

    public function addBeforeImages()
    {
        //施工後報告画面
        $user = $this->user;
        $this->breadcrumbs->addCrumb('現場報告', 'contact/before/report')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        //ステータスとユーザー番号に考慮してクエリを作成
        $contacts = Contact::where('user_id', $user->id)
            ->where('status', 1)
            ->where('step_id', self::STEP_ONSITE_CONFIRM)
            ->orderBy('updated_at', 'asc')->get();

        if (isset($contacts)) {
            return view('fc.progress.add-before-images', compact('breadcrumbs', 'contacts'));
        } else {
            $contacts = '該当する案件がありません。';

            return view('fc.progress.add-before-images', compact('breadcrumbs', 'contacts'));
        }
    }

    //現場画像を登録
    public function postBeforeImages(ContactVisitReportRequest $request)
    {
        $inputs = $request->all();
        $contact = $this->model->findOrFail($inputs['c']['id']);

        //S3に画像を3枚まで登録
        for ($i = 1; $i < 4; ++$i) {
            if (!empty($inputs['c']['before_image' . $i])) {
                $file = $inputs['c']['before_image' . $i];
                $path = $file->store('/images/before/' . $inputs['c']['id'], 's3', 'public');
                $pathExplodes = explode('/', $path);
                $filename = last($pathExplodes);
                $inputs['c']['before_image' . $i] = $filename;
            }
        }

        $inputs['c']['step_id'] = self::STEP_QUOTATION;

        $contact->fill($inputs['c']);
        $contact->save();

        //WIP 見積もりの準備がOKになるので、要見積もり一覧へリダイレクト
        return redirect()->route('dashboard')->with('success', '現場画像登録が完了しました。見積もり案件一覧から見積もりが作成できます。');
    }

    public function csvExport(Request $request)
    {
        $post = $request->all();

        if (!empty($post['form_name'])) {
            $form_name = CsvExportOptions::select('csv_export_options.form_name')->where('id', $post['form_name'])->first();
        } else {
            $form_name['form_name'] = 'サンプル請求後フォローメール';
        }

        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $results = $this->model->getCsvFormat($post['start_date'], $post['end_date']);
        $results_copy = $results;
        $filterd_results = $results;
        // 除外するキーリスト(除外するcontacts.idが並ぶ)
        $exclude_ids = [];
        $same_customers_model = new SameCustomerContact();
        // まずは除外キーリストを作成
        foreach ($results as $r) {
            // 同一顧客3つ以上のパターンはSQLのUNIONとGroupByで排除
            // ref_contact_idとcontact_idのクロスで揃うパターンがあるか？（同一顧客2つだけのパターン）
            if (!is_null($r['ref_contact_id'])) {
                // 同一顧客がある案件の場合、以下のforeachで配列の後ろ側にある同一顧客を削除する
                foreach ($results_copy as $key => $rc) {
                    // ここで削除対象を選定。自分自身ではないかつ、除外したリスト以外ならば配列から除外する
                    if (!is_null($rc['ref_contact_id']) && $r['id'] != $rc['id'] && !in_array($r['id'], $exclude_ids)) {
                        // $same_customers_model->summarizeSameCostomerIds($rc['id'], $post['start_date'], $post['end_date']);
                        array_push($exclude_ids, $rc['id']);
                        Arr::forget($filterd_results, $key);
                    }
                }
            }
        }
        $response = new StreamedResponse(function () use ($request, $post, $filterd_results, $form_name) {
            $stream = fopen('php://output', 'w');
            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');
            // タイトルを追加
            fputcsv($stream, [
                'メールアドレス',
                '顧客姓',
                '顧客名',
                '都道府県',
                '以降の住所',
                'フォーム名',
            ]);

            if (empty($filterd_results[0])) {
                fputcsv($stream, [
                    '指定期間のデータは存在しませんでした。',
                ]);
            } else {
                foreach ($filterd_results as $row) {
                    fputcsv($stream, [
                        $row->email,
                        $row->surname,
                        $row->name,
                        $row->pref,
                        $row->city,
                        $form_name['form_name']
                    ]);
                }
            }
            fclose($stream);
        });
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . date('Y年m月d日', strtotime($post['start_date'])) . '〜' . date('Y年m月d日', strtotime($post['end_date'])) . 'フォローメール用リスト.csv"');

        return $response;
    }

    public function csvSampleExport()
    {
        // $results = $this->model->getCsvFormat($post['start_date'], $post['end_date']);
        $query = $this->model->query()
            ->where(function ($query) {
                $query->whereIn('contact_type_id', [1, 5])
                    ->where('sample_send_at', null)
                    ->where('step_id', '!=', self::STEP_PAST_CUSTOMER)
                    ->where('status', 1);
            })
            ->orWhere(function ($query) {
                $query->whereIn('contact_type_id', [2, 3, 4, 6, 7, 8])
                    ->where('free_sample', '必要')
                    ->where('sample_send_at', null)
                    ->where('step_id', '!=', self::STEP_PAST_CUSTOMER)
                    ->where('status', 1);
            });
        $results = $query->orderBy('id', 'DESC')->get();

        $response = new StreamedResponse(function () use ($results) {
            $stream = fopen('php://output', 'w');

            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // タイトルを追加
            fputcsv($stream, [
                '利用区分',
                '氏名',
                '担当者名',
                '郵便番号',
                '住所1',
                '住所2',
                '住所3',
            ]);

            if (empty($results[0])) {
                fputcsv($stream, [
                    '指定期間のデータは存在しませんでした。',
                ]);
            } else {
                foreach ($results as $row) {
                    fputcsv($stream, [
                        1,
                        $row->contact_type_id < 5 ? $row->surname . ' ' . $row->name : $row->company_name,
                        $row->contact_type_id > 4 ? $row->surname . ' ' . $row->name : null,
                        $row->zipcode,
                        $row->pref,
                        $row->city,
                        $row->street,
                    ]);
                }
            }
            fclose($stream);
        });
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . date('Y年m月d日') . 'ゆうプリ用サンプル送付リスト.csv"');

        return $response;
    }

    public function csvCustomExport(CustomCsvRequest $request)
    {
        $posts = $request->all();
        // dd($posts['export']);

        $filters = [];
        $filters['start'] = $posts['start_date'];
        $filters['end'] = $posts['end_date'];
        $filters['steps'] = $posts['steps'] ?? [];
        $filters['prefectures'] = $posts['prefectures'] ?? [];
        $filters['contact_types'] = $posts['contact_types'] ?? [];
        // dd($filters);

        $data = $this->model->customCsvExport($filters, $posts['export']);
        $results = $data['results'];
        $labels = $data['labels'];
        // dd($data['results']);
        $response = new StreamedResponse(function () use ($results, $labels) {
            $stream = fopen('php://output', 'w');
            // stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');
            $results = $results->toArray();
            // タイトルもエンコーディング変換
            foreach ($labels as $key => $value) {
                $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                $value = iconv('UTF-8', 'SJIS-win//IGNORE', $value);
                $labels[$key] = $value;
            }
            // タイトルを追加
            fputcsv($stream, $labels);

            if (empty($results[0])) {
                fputcsv($stream, [
                    '指定期間のデータは存在しませんでした。',
                ]);
            } else {
                // 不正なマルチバイト文字を削除
                foreach ($results as $row) {
                    foreach ($row as $key => $value) {
                        $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                        $value = iconv('UTF-8', 'SJIS-win//IGNORE', $value);
                        $row[$key] = $value;
                    }
                    fputcsv($stream, $row);
                }
            }

            fclose($stream);
        });
        $start = Carbon::parse($posts['start_date'])->format('Y年m月d日');
        $end = Carbon::parse($posts['end_date'])->format('Y年m月d日');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $start . '〜' . $end . 'カスタム顧客リスト.csv"');

        return $response;
    }

    //進捗管理-キャンセル案件一覧
    public function cancelList()
    {
        $user = $this->user;
        $fcId = $user->id;
        $this->breadcrumbs->addCrumb('キャンセル案件一覧', '/contact/cancel')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        if (isAdmin()) {
            $contacts = $this->model->where('cancel_step', '!=', null)->orderBy('created_at', 'DESC')->paginate(50);
        } else {
            $contacts = $this->model->where(function ($query) use ($fcId) {
                return $query->where('user_id', $fcId)->where('cancel_step', '!=', null);
            })
                ->orderBy('created_at', 'DESC')->paginate(50);
        }

        return view('share/contact/cancelled-list', compact('contacts', 'breadcrumbs'));
    }

    //商談結果待ち一覧
    public function pendingList()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('商談結果待ち', 'contact/pending/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        //$contacts = $this->model->where('step_id', self::STEP_RESULT)->where('user_id', $user->id)->paginate(20);

        if ($this->model->where('step_id', self::STEP_RESULT)->where('user_id', $user->id)->exists()) {
            $contacts = $this->model->select('contacts.*', 'un.read')
                ->whereIn('contacts.status', [1, 3])
                ->where('contacts.cancel_step', null)
                ->where('contacts.step_id', self::STEP_RESULT)
                ->where('contacts.user_id', $user->id)
                ->leftJoin('user_notifications AS un', 'un.contact_id', '=', 'contacts.id')
                ->groupBy('contacts.id')
                ->orderBy('contacts.id', 'DESC')
                ->paginate(100);
        } else {
            $contacts = '該当するお問い合わせは存在しません。';
        }
        //dd($contacts);

        return view('share/contact/pending-list', compact('breadcrumbs', 'contacts'));
    }

    //商談結果登録
    public function pendingListCommit(Request $request)
    {
        $contactId = $request->input('id');
        $type = $request->input('type');
        $now = Carbon::now();
        $now_time = $now->format('Y-m-d H:i:s');
        switch ($type) {
            case '0':
                $resutlt = $this->model->where('id', $contactId)->update(['step_id' => self::STEP_RESULT, 'status' => 3]);
                $message = 'ステータスを顧客返答待ちに設定しました。顧客からの返答があり次第入力してください。';

                return redirect(route('pending.list'))->with('warning', $message);
                break;

            case '1':
                //本部が見積もった場合、発送連絡待ちに行く
                $quotations = Quotation::where('contact_id', $contactId)->where('user_id', \Auth::id())->where('status', 1)->get();
                if (count($quotations) < 2) {
                    if (isAdmin()) {
                        if (empty($quotations[0])) {
                            return redirect(route('quotations.needs'))->with('danger', 'この案件には見積書がありません。見積書を作成してください。');
                        }
                        Contact::where('id', $contactId)->update(['step_id' => self::STEP_SHIPPING, 'contracted_at' => $now_time, 'quotation_id' => $quotations[0]->id, 'status' => 1]);

                        return redirect(route('dispatch.pending'))->with('success', 'ステータスを受注成立に設定しました。商品を発送したら発送メールを送信してください。');
                    } else {
                        if (empty($quotations[0])) {
                            return redirect(route('quotations.needs'))->with('danger', 'この案件には見積書がありません。見積書を作成してください。');
                        }
                        Contact::where('id', $contactId)->update(['step_id' => self::STEP_TRANSACTION, 'contracted_at' => $now_time, 'quotation_id' => $quotations[0]->id, 'status' => 1]);

                        return redirect(route('transaction.pending.list'))->with('success', 'ステータスを受注成立に設定しました。必要な部材を本部に発注してください。');
                    }
                } else {
                    Contact::where('id', $contactId)->update(['step_id' => self::STEP_TRANSACTION, 'contracted_at' => $now_time, 'status' => 1]);
                    // 見積書が2枚以上あった場合はFCと同じ動き
                    // if (isAdmin()) {

                    // return redirect(route('dispatch.pending'))->with('success', 'ステータスを受注成立に設定しました。商品を発送したら発送メールを送信してください。');
                    return redirect(route('contact.show', ['id' => $contactId]))->with('success', 'ステータスを受注成立に設定しました。顧客に提出・選択した見積もりを選んでください。');
                }
                break;

            case '2':
                $result = $this->model->where('id', $contactId)->first();

                $result->update(['step_id' => self::STEP_CANCELATION, 'cancel_step' => $result->step_id, 'status' => 1]);
                $message = 'ステータスを失注に設定しました。';

                return redirect(route('pending.list'))->with('danger', $message);
                break;
        }
    }

    // FCから本部への発注待ち一覧
    public function transactionPendingList()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('部材発注待ち', 'contact/transaction/pending/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $contacts = $this->model->where('step_id', self::STEP_TRANSACTION)->where('user_id', $user->id)->paginate(100);

        // 本部発送待ちのためのデータ
        $pendings = $this->model->waitShipment();

        return view('fc/contact/transaction-pending-list', compact('breadcrumbs', 'contacts', 'pendings'));
    }

    public function getSearchForm()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('顧客検索', 'search/')->setLastItemWithHref(false);
        $breadcrumbs = $this->breadcrumbs;

        return view('share.search.result', compact('breadcrumbs'));
    }

    public function searchResult(Request $request)
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('検索結果', 'search/')->setLastItemWithHref(false);
        $breadcrumbs = $this->breadcrumbs;

        $contact_types = ContactType::all();

        //検索ロジックの定義
        $inputs = $request->all();
        //dd($inputs);
        $name = $inputs['name'];
        $tel = $inputs['tel'];
        $pref = $inputs['pref'];

        $query = Contact::query()->where('step_id', self::STEP_REPORT_COMPLETE);
        //Todo set cancellation as well. ->orWhere('step_id', self::STEP_CANCELLATION);

        if (!is_null($name)) {
            $query->where('name', $name);
        }
        if (!is_null($tel)) {
            $query->where('tel', $tel);
        }
        if (!is_null($pref)) {
            $query->where('pref', $pref);
        }

        $contacts = $query->paginate(30)
            ->appends(request()->query());

        //dd($contacts);

        return view('share.search.result', compact('breadcrumbs', 'contacts', 'contact_types'));
    }

    public function downloadImage($id, $path, $file, $downloadName)
    {
        $content = Storage::disk('s3')->get("images/$path/$id/$file");

        $explode = explode('.', $file);
        $contentType = last($explode);
        $filename = '案件No' . $id . ':' . $downloadName . '.' . $contentType;

        $headers = [
            'Content-Type' => $contentType,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$filename}",
            'filename' => $filename,
        ];

        return response($content, 200, $headers);
    }

    public function downloadFile($id, Request $request)
    {
        $file = $request->get('file');
        $originalName = $request->get('originalName');
        $content = Storage::disk('s3')->get("documents/$id/$file");

        $explode = explode('.', $file);
        $contentType = last($explode);

        $headers = [
            'Content-Type' => $contentType,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$originalName}",
            'filename' => $originalName,
        ];

        return response($content, 200, $headers);
    }

    //お問い合わせを削除
    public function Destroy($id)
    {
        $contact = Contact::where('id', $id)->first();
        if (isFc() && $contact['step_id'] > self::STEP_QUOTATION) {
            return redirect('/');
        }
        //ここからトランザクション処理
        $this->model->transStart();
        try {
            Quotation::where('contact_id', $id)->update(['status' => 2]);
            Transaction::where('contact_id', $id)->update(['status' => 2]);

            $sameCustomerContactId = SameCustomerContact::where('contact_id', $id)->get();
            SameCustomerContact::where('ref_contact_id', $id)->delete();
            SameCustomerContact::where('contact_id', $id)->delete();

            //案件削除時に同一顧客のメインFCも解除
            $sameCustomers = $this->model->findSameCustomer($contact, $id);
            $this->model->where('id', $id)->update(['status' => 2, 'main_user_id' => null]);
            if (!is_null($contact['main_user_id'])) {
                //同一顧客のメインFC解除
                foreach ($sameCustomers as $sc) {
                    $this->model->where('id', $sc['id'])
                        ->where('main_user_id', $contact['main_user_id'])
                        ->update(['main_user_id' => null]);
                }
                //手動で同一顧客に登録した案件のメインFC解除
                foreach ($sameCustomerContactId as $scc) {
                    $this->model->where('id', $scc['ref_contact_id'])
                        ->where('main_user_id', $contact['main_user_id'])
                        ->update(['main_user_id' => null]);
                }
            }


            $transaction = Transaction::where('contact_id', $id)->first();
            if (config('app.env') != 'circleci' && !empty($transaction['id'])) {
                transactionDestroyToSlack(\Auth::user(), $transaction['id'], '問い合わせ削除で発注書も削除');
            }
            //トランザクションを確定
            $this->model->transCommit();
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            //ロールバック処理
            $this->model->transRollback();
        }

        return redirect(route('contact.customers'))->with('danger', 'お問い合わせを削除しました');
    }

    public function contactCancel($id)
    {
        $contact = $this->model->findOrFail($id);
        Contact::where('id', $id)->update(['cancel_step' => $contact['step_id'], 'step_id' => 99]);

        return redirect(route('dashboard'))->with('danger', '案件No.' . $id . 'をキャンセルに設定しました');
    }

    //サンプル送付一覧
    public function sampleList()
    {
        $user = $this->user;
        $this->breadcrumbs->addCrumb('サンプル送付一覧', '/contact/sample/list')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        /* 複数条件はクロージャ */
        $query = $this->model->query()
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
        $sample_list = $query->orderBy('id', 'DESC')->paginate(200);

        if (isFc()) {
            return redirect(route('dashboard'));
        } else {
            return view('admin/contact/sample-sending-list', compact('breadcrumbs', 'sample_list'));
        }
    }

    //サンプル送付完了にする
    public function sampleListSent(Request $request)
    {
        $contact_ids = $request->input('contact_id');

        if (!isset($contact_ids)) {
            return redirect(route('sample.list'))->with('danger', '未選択です');
        }

        foreach ($contact_ids as $id) {
            $this->model->where('id', $id)->update(['sample_send_at' => Carbon::now()]);
        }

        return redirect(route('sample.list'))->with('success', 'サンプル送付が完了しました');
    }

    public function testSend()
    {
        Mail::to(config('mail.fallback_notification', 'notifications@example.com'))->send(new SendTestMail());
    }

    // 同一顧客をjsonで返す
    public function ajaxSameCustomer($id)
    {
        $contacts = $this->model->select('contacts.*')
            ->where('contacts.status', 1)
            ->where('scc.contact_id', $id)
            ->leftJoin('same_customer_contacts AS scc', 'contacts.id', '=', 'scc.ref_contact_id')
            ->groupBy('scc.ref_contact_id')
            ->orderBy('contacts.id', 'ASC')
            ->get();

        return response()->json($contacts);
    }

    //同一顧客解除
    public function ajaxSameCustomerDestroy(Request $request)
    {
        $contacts = $request->all();

        SameCustomerContact::where('contact_id', $contacts['contact_id'])->where('ref_contact_id', $contacts['select_id'])->delete();
        SameCustomerContact::where('ref_contact_id', $contacts['contact_id'])->where('contact_id', $contacts['select_id'])->delete();

        /* メインFC解除の処理 解除された側のmain_user_idも削除 */
        $this->model->whereIn('id', [$contacts['contact_id'], $contacts['select_id']])->update(['main_user_id' => null]);

        return response()->json($contacts);
    }
    // 同一顧客追加
    public function ajaxSameCustomerAdd(Request $request)
    {
        $contacts = $request->all();
        // 案件が存在していなかった場合Errorを返す
        if ($this->model->where('id', $contacts['add_same_id'])->where('status', 1)->exists()) {
            // 同一顧客案件登録処理
            SameCustomerContact::insert(['ref_contact_id' => $contacts['add_same_id'], 'contact_id' => $contacts['contact_id']]);
            SameCustomerContact::insert(['ref_contact_id' => $contacts['contact_id'], 'contact_id' => $contacts['add_same_id']]);
            //メインFC登録処理
            $contactId = $this->model->where('id', $contacts['contact_id'])->first();
            $refContactId = $this->model->where('id', $contacts['add_same_id'])->first();
            if (!is_null($contactId['main_user_id']) && is_null($refContactId['main_user_id'])) {
                $this->model->where('id', $refContactId['id'])->update(['main_user_id' => $contactId['main_user_id']]);
            } else if (is_null($contactId['main_user_id']) && !is_null($refContactId['main_user_id'])) {
                $this->model->where('id', $contactId['id'])->update(['main_user_id' => $refContactId['main_user_id']]);
            }

            // ajaxで顧客一覧に追加した案件を持ってくる。
            $contacts = $this->model->select('contacts.*')
                ->where('contacts.status', 1)
                ->where('scc.contact_id', $contacts['contact_id'])
                ->where('scc.ref_contact_id', $contacts['add_same_id'])
                ->leftJoin('same_customer_contacts AS scc', 'contacts.id', '=', 'scc.ref_contact_id')
                ->orderBy('contacts.id', 'ASC')
                ->get();

            // 案件が無事追加された際のResponse
            return response()->json($contacts);
        } else {
            return response('null-contact');
        }
    }

    // 同一顧客がいた場合trueを返す
    public function ajaxSameCustomerButton($id)
    {
        $same = SameCustomerContact::where('ref_contact_id', $id)->orWhere('contact_id', $id)->count();
        $status = ($same == 0) ? 'else' : 'true';
        return $status;
    }
    //キャンセル案件を復元する
    public function restoreCancel($id)
    {
        $contact = $this->model->find($id);
        Contact::where('id', $id)->update(['step_id' => $contact->cancel_step, 'cancel_step' => null]);

        return redirect(route('contact.show', ['id' => $id]))->with('success', 'キャンセル案件を復元しました！');
    }
}
