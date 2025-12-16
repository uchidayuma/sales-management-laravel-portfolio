<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Mail\SendUserRegistMail;
use App\Mail\SendUserYearMail;
use App\Mail\SendUserOpenMail;
use App\Mail\SendUserPreOpenMail;
use App\Models\Contact;
use App\Models\Config;
use App\Models\AreaOpenEmailSend;
use App\Models\FcApplyArea;
use App\Models\Prefecture;
use App\Models\PasswordReset;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Hamcrest\Arrays\IsArray;
use Illuminate\Support\Facades\Log;
use Mail;


class UserController extends MyController
{
    //use ResetsPasswords;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('verified')->except(['destroy']);
        $this->model = $user;
        $this->breadcrumbs->addCrumb('<i class="fas fa-users"></i>FC管理', '');
    }

    public function getLogout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function index()
    {
        if (isFc()) {
            return redirect('/')->with('danger', 'このページはFCのアクセスを禁じられています。');
        }
        $this->user;
        $this->breadcrumbs->addCrumb('FC一覧', '/users')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        // 1=アクティブ、2=退会済み, 3=研修中, 4=活動停止中
        $fc = User::select('users.*', 'fca.name AS area_name', 'fca.content AS area_content')->where('role', 2)->where('users.status', '!=', 99)
            ->leftJoin('fc_apply_areas as fca', 'fca.id', '=', 'users.fc_apply_area_id')
            ->orderByRaw('FIELD(users.status, 1, 3, 4, 2)')->orderBy('users.id', 'ASC')->paginate(50);
        //dd($users);

        return view('admin.users.index', compact('fc', 'breadcrumbs'));
    }

    public function search(Request $request)
    {
        $this->user;
        $this->breadcrumbs->addCrumb('FC検索結果', '/users/search')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $pref = $request->input('pref');

        if (!empty($pref)) {
            $fc = User::where('pref', 'LIKE', '%' . $pref . '%')->where('users.status', '!=', 2)->paginate(50);
        } else {
            $fc = User::all()->where('role', 2);
        }

        return view('admin.users.index', compact('fc', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (isFc()) {
            return redirect('/')->with('danger', 'このページはFCのアクセスを禁じられています。');
        }
        $this->user;
        $prefectures = Prefecture::all();
        $fc_apply_areas = FcApplyArea::where('status', 1)->get();

        return view('admin.users.create', compact('prefectures', 'fc_apply_areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        adminOnly();
        $user = new User();

        $inputs = $request->all();
        //dd($inputs);

        //入力住所から緯度と経度を取得
        $api_key = config('app.google_api_key');
        $address = $inputs['fc']['pref'] . $inputs['fc']['city'] . $inputs['fc']['street'];
        //dd($address);
        $address = urlencode($address);
        $geocode = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}");
        $jsonData = json_decode($geocode, true);
        //dd($jsonData);
        $latitude = $jsonData['results'][0]['geometry']['location']['lat'];
        $longitude = $jsonData['results'][0]['geometry']['location']['lng'];

        $inputs['fc']['latitude'] = $latitude;
        $inputs['fc']['longitude'] = $longitude;
        $inputs['fc']['password'] = bcrypt('kaerusan');
        //dd($inputs['fc']);

        $token = Str::random(60);

        $inputs['fc']['remember_token'] = $token;

        try {
            // Stripeへ登録
            // if (config('app.env') == 'production') {
            //     $stripeAccount = $this->model->postStripe($inputs);
            //     $inputs['fc']['stripe_id'] = $stripeAccount->id;
            // } else {
            //     $inputs['fc']['stripe_id'] = null;
            // }

            // Freeeに登録
            $freee_message = '';
            if (config('app.env') == 'production' || config('app.env') == 'local' || config('app.env') == 'localhost') {
                $result = $this->model->postFreee($inputs);
                if (empty($result['errors'])) {
                    //freee userレスポンスからユーザーIDをセット(localhostの場合 freee_user_id に nullをセット)
                    $inputs['fc']['freee_user_id'] =  config('app.env') == 'production' ? $result['partner']['id'] : null;
                } else {
                    \Log::debug(print_r($result['errors'], true));
                    if (!empty($result['errors'][1])) {
                        if (!empty($result['errors'][1]['messages'][0] == '名前（通称）はすでに存在します。')) {
                            // code...
                            $inputs['fc']['freee_user_id'] = null;
                            $freee_message = '登録されたFCは既にfreeeに登録されていました。FCOPと紐づけるため、運用会社に登録したFCの名前をご連絡ください。運用者が紐付けをおこないます。';
                        }
                    }
                }
            }

            unset($inputs['fc']['is_personal']);
            //dd($inputs);

            // FCを登録
            $id = $user->insertGetId($inputs['fc']);

            //password_resetsテーブルにtokenを保存
            $saveToken = new PasswordReset();
            $saveToken->email = $inputs['fc']['email'];
            $saveToken->token = $token;
            $saveToken->save();

            //メールを送る処理
            if (\App::environment('production') || \App::environment('testing')) {
                Mail::to($inputs['fc']['email'])->send(new SendUserRegistMail($inputs, $token));
            }

            //FC保存完了メッセージを発行して、FC一覧画面へ誘導
            if ($freee_message != '') {
                return redirect()->route('users.index')->with('danger', '新しいFCが保存されました！' . $freee_message);
            } else {
                return redirect()->route('users.index')->with('success', '新しいFCが保存されました！');
            }
        } catch (\Throwable $th) {
            \Log::debug(print_r('error log', true));
            \Log::debug(print_r($th->getMessage(), true));
            throw $th;

            return $th->getMessage();
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
        $this->breadcrumbs->addCrumb('FC詳細', 'users/' . $id)->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;
        $now_year = Carbon::now()->format('Y');

        $fc = User::select('users.*', 'p.name AS prefecture_name', 'fca.name AS area_name', 'fca.content AS area_content')
            // 'aoes.created_at AS area_open_created_at', 'aoes.send_status', 'aoes.status AS area_open_status')
            ->where('users.id', $id)
            ->leftJoin('prefectures as p', 'p.id', '=', 'users.prefecture_id')
            ->leftJoin('fc_apply_areas as fca', 'fca.id', '=', 'users.fc_apply_area_id')
            ->first();
        if ($fc === null) {
            // ユーザーが見つからない場合の処理
            return redirect()->route('users.index')->with('danger', '指定されたFCが見つかりません。');
        }
        if (($id != \Auth::id()) && isFc()) {
            return redirect()->route('dashboard')->with('danger', '他のFCページを閲覧することはできません');
        }
        $fc = $fc->toArray();
        // エリア開放メール送信履歴を整形
        $fc['open_mail_sends'] = [];
        // ・自己獲得案件5件以上あった → 件数チェック  → area_open_email_sendsにレコードが存在しない
        // ・自己獲得案件5件未満で本部がチェック外した → area_open_email_sends.send_status = 2
        // ・自己獲得案件5件未満でメール送った → area_open_email_sends.send_status = 1
        for ($i = 2022; $i <= $now_year; $i++) {
            $result = AreaOpenEmailSend::where('user_id', $fc['id'])->whereYear('created_at', $i)->first();
            $fc['open_mail_sends'][$i] = is_null($result) ? 'success' : $result['send_status'];
        }
        //担当案件が何件あるかを判定
        $count = Contact::where('user_id', $id)
            ->where('status', 1)->count();

        // dd($fc);
        return view('share.users.detail', compact('fc', 'breadcrumbs', 'count', 'now_year'));
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
        if (isFc() && $id != \Auth::id()) {
            return redirect('/')->with('danger', 'このページは本人以外のアクセスを禁じられています。');
        }
        $this->breadcrumbs->addCrumb('FC情報編集', 'users/edit/' . $id)->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $fc = User::find($id);
        $prefectures = Prefecture::all();
        $fc_apply_areas = FcApplyArea::where('status', 1)->get();

        return view('share.users.edit', compact('fc', 'breadcrumbs', 'prefectures', 'fc_apply_areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::find($id);

        $user->name = $request->input('fc.name');
        $user->company_name = $request->input('fc.company_name');
        $user->company_ruby = $request->input('fc.company_ruby');
        $user->qualified_business_number = $request->input('fc.qualified_business_number');
        //admin以外のアカウントではメールの送信許可関係の設定を受け付けない
        if (isAdmin()) {
            $user->allow_email = $request->input('fc.allow_email') == '1' ? '1' : '0';
            $user->prefecture_id = $request->input('fc.prefecture_id');
            $user->require_prepaid = $request->input('fc.require_prepaid') == '1' ? '1' : '0';
            $user->fc_apply_area_id = $request->input('fc.fc_apply_area_id');
            $user->memo = $request->input('fc.memo');
        }
        $user->allow_notification = $request->input('fc.allow_notification') == '1' ? '1' : '0';
        $user->contract_date = $request->input('fc.contract_date');
        $user->email = $request->input('fc.email');
        $user->email2 = $request->input('fc.email2');
        $user->email3 = $request->input('fc.email3');
        $user->zipcode = $request->input('fc.zipcode');
        $user->pref = $request->input('fc.pref');
        $user->city = $request->input('fc.city');
        $user->street = $request->input('fc.street');
        $user->s_zipcode = $request->input('fc.s_zipcode');
        $user->s_pref = $request->input('fc.s_pref');
        $user->s_city = $request->input('fc.s_city');
        $user->s_street = $request->input('fc.s_street');
        $user->storage_tel = $request->input('fc.storage_tel');
        $user->optional_zipcode = $request->input('fc.optional_zipcode');
        $user->optional_pref = $request->input('fc.optional_pref');
        $user->optional_city = $request->input('fc.optional_city');
        $user->optional_street = $request->input('fc.optional_street');
        $user->optional_tel = $request->input('fc.optional_tel');
        $user->optional_staff = $request->input('fc.optional_staff');
        $user->tel = $request->input('fc.tel');
        $user->fax = $request->input('fc.fax');
        $user->s_tel = $request->input('fc.s_tel');
        $user->s2_tel = $request->input('fc.s2_tel');
        $user->s3_tel = $request->input('fc.s3_tel');
        $user->staff = $request->input('fc.staff');
        $user->staff_ruby = $request->input('fc.staff_ruby');
        $user->staff2 = $request->input('fc.staff2');
        $user->staff2_ruby = $request->input('fc.staff2_ruby');
        $user->staff3 = $request->input('fc.staff3');
        $user->staff3_ruby = $request->input('fc.staff3_ruby');
        $user->quotation_memo = $request->input('fc.quotation_memo');
        $user->account_infomation1 = $request->input('fc.account_infomation1');
        $user->account_infomation2 = $request->input('fc.account_infomation2');
        $user->account_infomation3 = $request->input('fc.account_infomation3');
        $user->quotation_tax_option = $request->input('fc.quotation_tax_option');
        $user->admin_sample_send = $request->input('fc.admin_sample_send');

        // fc.invoice_payments_typeがnullの場合更新しない(FCの場合nullになる)
        if (!is_null($request->input('fc.invoice_payments_type'))) {
            $user->invoice_payments_type = $request->input('fc.invoice_payments_type');
        }

        // fc.status がnullなら true
        if (!empty($request->input('fc.status'))) {
            $user->status = $request->input('fc.status');
        }

        // ハンコ（笑）アップロード
        $seal = $request->file('fc.seal');
        if (!empty($seal) && $request->input('seal-state') != 'nochange') {
            $path = 'images/seals/' . $user->id . '.' . $seal->getClientOriginalExtension();
            $seal->storeAs('/images/seals/', $user->id . '.' . $seal->getClientOriginalExtension(), ['disk' => 'public']);
            $user->seal = $user->id . '.' . $seal->getClientOriginalExtension();
        } elseif ($request->input('seal-state') == 'deleted') {
            $user->seal = null;
        }

        // saveを使っているので、ユーザー項目を増やすたら、必ず $user->増やした値を入れること！
        $user->save();

        return redirect()->route('users.show', ['id' => $user->id])->with('success', 'FC情報を修正しました！');
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
        // TODO Stripeアカウントも削除する
        $user = User::find($id);
        //stepを変更

        //stripeアカウントも削除する
    }

    // パスワードの変更
    public function showChangePasswordForm($id)
    {
        $fc = User::find($id);

        return view('share.users.change-password', ['id' => $fc->id], compact('fc'));
    }

    public function changePassword(Request $request, $id)
    {
        //現在のパスワードが正しいかを調べる
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            return redirect()->back()->with('change_password_error', '現在のパスワードが間違っています。');
        }

        //現在のパスワードと新しいパスワードが違っているかを調べる
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            return redirect()->back()->with('change_password_error', '新しいパスワードが現在のパスワードと同じです。違うパスワードを設定してください。');
        }

        //パスワードのバリデーション。新しいパスワードは8文字以上255文字以下、new-password_confirmationフィールドの値と一致しているかどうか。
        $validated_data = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|between:8,255|confirmed',
        ]);

        //パスワードを変更
        $user = Auth::user();
        User::where('id', $user->id)->update(['password' => bcrypt($request->get('new-password'))]);

        return redirect()->route('users.show', ['id' => $user->id])->with('success', 'パスワードを変更しました。');
    }

    public function csvExport()
    {
        if (isFc()) {
            return false;
        }
        $response = new StreamedResponse(function () {
            $stream = fopen('php://output', 'w');
            $users = $this->model->select('users.*', 'faa.name AS area_name', 'faa.content AS area_content')
                ->where('users.role', 2)->where('users.name', 'not like', "%テスト%")->where('users.status', '<>', 2)
                ->leftJoin('fc_apply_areas AS faa', 'faa.id', '=', 'users.fc_apply_area_id')
                ->orderByRaw('FIELD(users.status, 1, 3, 4, 2)')->orderBy('users.id', 'ASC')->get();

            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // タイトルを追加
            fputcsv($stream, [
                'FC名（屋号）',
                '会社名（請求書宛名）',
                '住所（郵便番号含め）事業所住所',
                '契約日',
                '担当エリア',
                'TEL',
                'メールアドレス',
                '担当者携帯TEL',
                'システム利用料種別',
                'メモ'
            ]);

            foreach ($users as $row) {
                // 0=請求なし、1=HP掲載料、2=ブランド使用料が月末自動発行する請求書の最終行に載る
                $invoice_payments_type = '';
                switch ($row['invoice_payments_type']) {
                    case '0':
                        $invoice_payments_type = '請求なし';
                        break;
                    case '1':
                        $invoice_payments_type = 'HP掲載料';
                        break;
                    case '2':
                        $invoice_payments_type = 'ブランド使用料';
                        break;

                    default:
                        $invoice_payments_type = '請求なし';
                        break;
                }
                fputcsv($stream, [
                    $row['name'],
                    $row['company_name'],
                    '〒' . $row['zipcode'] . ' ' . $row['pref'] . $row['city'] . $row['street'],
                    date('Y年m月d日', strtotime($row->contract_date)),
                    $row['area_name'] . ' ' . $row['area_content'],
                    $row['tel'],
                    $row['email'],
                    $row['s_tel'],
                    $invoice_payments_type,
                    $row['memo'],
                ]);
            }
            fclose($stream);
        });
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . date('Y年m月d日') . '出力FCリスト.csv"');

        return $response;
    }

    public function csvPostExport()
    {
        $users = $this->model->where('role', 2)->where('name', 'not like', "%テスト%")->where('status', '<>', 2)->orderByRaw('FIELD(status, 1, 3, 4)')->orderBy('id', 'ASC')->get();

        $response = new StreamedResponse(function () use ($users) {
            $stream = fopen('php://output', 'w');

            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // タイトルを追加
            fputcsv($stream, [
                '利用区分',
                '法人名',
                '郵便番号',
                '住所1',
                '住所2',
                '住所3',
            ]);

            if (empty($users[0])) {
                fputcsv($stream, [
                    'データは存在しませんでした。',
                ]);
            } else {
                foreach ($users as $row) {
                    fputcsv($stream, [
                        1,
                        $row->company_name,
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
        $response->headers->set('Content-Disposition', 'attachment; filename="' . date('Y年m月d日') . 'ゆうプリ用FCリスト.csv"');

        return $response;
    }

    public function contractIndex(Request $request)
    {
        if (isFc()) {
            return redirect('/');
        }
        $this->breadcrumbs->addCrumb('契約更新FC一覧', '');
        $breadcrumbs = $this->breadcrumbs;

        $date = Carbon::now();
        $currentYear = $date->year;
        $currentMonth = $date->month;
        $year = $request->get('year');
        $month = $request->get('month');
        $query = [
            'year' => $year,
            'month' => $month,
        ];

        if (empty($query['year']) || empty($query['month'])) {
            $query = [
                'year' => $currentYear,
                'month' => $date->addMonthNoOverflow()->month,
            ];
            $year = empty($request->get('year')) || $currentMonth == 12 ? $currentYear + 1 : $request->get('year');
            $month = $query['month'];
        }
        $queryString = http_build_query($query);

        // 過去の西暦を取得
        $i = 0;
        $pastYear = [];
        for ($y = $currentYear; $y > 2020; --$y) {
            $pastYear[$i] = $y;
            ++$i;
        }
        if ($currentMonth == 12) {
            array_unshift($pastYear, $currentYear + 1);
        }
        $get_date = Carbon::create($query['year'], $query['month'], 1);
        // 1ヶ月前の日付を計算
        $get_date->subMonth();
        $prevMonth = $get_date->month;
        $prevYear = $get_date->year;
        // dd($prevMonth, $prevYear);
        $start_date = $query['year'] - 1 . '-' . $query['month'] . '-01';
        $end_date = $query['year'] - 1 . '-' . $query['month'] . '-31';
        $fcs = User::select('users.*', 'aoes.send_status', 'aoes.id AS send_id')->whereIn('users.status', [1, 3, 4])->where('users.role', 2)->whereNotNull('users.contract_date')
            ->whereMonth('users.contract_date', $query['month'])
            // 契約更新のひと月前に area_open_email_sendsにインサートされるので、created_atで判定
            ->whereMonth('aoes.created_at', $prevMonth)
            ->whereYear('aoes.created_at', $prevYear)
            ->join('area_open_email_sends as aoes', 'aoes.user_id', '=', 'users.id')
            ->orderBy('users.id', 'ASC')->orderBy('aoes.created_at', 'DESC')->get();

        return view('admin.users.constracts', compact('breadcrumbs', 'fcs', 'query', 'queryString', 'year', 'month', 'pastYear'));
    }

    // area_open_sendsテーブルに入れる処理
    public function cronAreaOpenIsFc()
    {
        $next_month_date = Carbon::now()->addMonthNoOverflow();
        $past_year_month_date = Carbon::now()->addMonthNoOverflow()->subYear()->toDateString();
        // dd($next_month_date->format('d'),$past_year_month_date);
        // 年を書き換え
        $fcs = $this->model->whereMonth('contract_date', $next_month_date->format('m'))
            ->whereNotNull('contract_date')->whereIn('status', [1, 3, 4])
            ->orderBy('id', 'ASC')->get();
        foreach ($fcs as $fc) {
            // 1ヶ月後更新のFCの自己獲得案件の数を調べる（キャンセル・削除以外）
            if (!$this->model->ownContactCountOkCheck($fc['id'], $past_year_month_date, $next_month_date->toDateString())) {
                AreaOpenEmailSend::insert(['user_id' => $fc['id'], 'send_status' => 1]);
            }
        }
    }
    // CRONで毎日自己獲得案件達成状況をチェック
    // area_open_mailsでチェックが入っているものだけを更新日まで確認して条件クリアしていたらチェック外す
    public function cronOwnContactCountCheck()
    {
        $tomorrow = Carbon::now()->addDay()->format('m-d');
        $tomorrow_add_month = Carbon::now()->addDay()->addMonthNoOverflow()->format('m-d');
        $next_month_date = Carbon::now()->addMonthNoOverflow();
        $past_year_month_date = Carbon::now()->addMonthNoOverflow()->subYear()->toDateString();
        // 契約更新日が明日から1ヶ月間のFCをリストアップ
        $fcs = $this->model->whereBetween('contract_date', [$tomorrow, $tomorrow_add_month])
            // ->orWhereMonth('contract_date', $next_month_date->format('m'))
            ->orWhereMonth('contract_date', Carbon::now()->format('m'))
            ->whereNotNull('contract_date')->whereIn('status', [1, 3, 4])
            ->orderBy('id', 'ASC')->get();
        \Log::debug(print_r('============ cronOwnContactCountCheck() ===========', true));
        // dd($fcs);
        // \Log::debug(print_r($fcs, true));
        $last_year = Carbon::now()->subYear()->format('Y');
        $now_year = Carbon::now()->format('Y');
        foreach ($fcs as $fc) {
            // 1ヶ月後更新のFCの自己獲得案件の数を調べる（キャンセル・削除以外）
            // 達成していればチェック外す（area_open_email_sends.send_status = 0が送る予定なしに変更
            $contract_date_month_day = Carbon::parse($fc['contract_date'])->format('m-d');
            $last_year_contract_date = $last_year . '-' . $contract_date_month_day;
            $now_year_contract_date = $now_year . '-' . $contract_date_month_day;
            if ($this->model->ownContactCountOkCheck($fc['id'], $last_year_contract_date, $now_year_contract_date)) {
                // \Log::debug(print_r($fc['id'], true));
                AreaOpenEmailSend::where(['user_id' => $fc['id'], 'status' => 1])->update(['send_status' => 0]);
            }
        }
    }

    // CRONで送るエリア開放メール (毎日CRON)
    public function cronSendOpenMail()
    {
        // FC契約更新エリア開放メールにチェックが入っているFCだけを抽出
        $fcs = $this->model->select('users.*', 'aoes.id AS send_id', 'faa.name AS area_name')->whereIn('users.status', [1, 3, 4])
            ->where('aoes.status', 1)->where('aoes.send_status', 1)
            ->whereMonth('users.contract_date', Carbon::now()->format('m'))
            ->whereDay('users.contract_date', Carbon::now()->format('d'))
            ->join('area_open_email_sends as aoes', 'aoes.user_id', '=', 'users.id')
            ->leftJoin('fc_apply_areas as faa', 'faa.id', '=', 'users.fc_apply_area_id')
            ->orderBy('users.id', 'ASC')->groupBy('users.id')->get();

        foreach ($fcs as $fc) {
            // 送信済みの処理
            AreaOpenEmailSend::where('id', $fc['send_id'])->update(['status' => 2]);
            $bcc = 'bcc@shintou-s.jp';
            $bcc2 = config('mail.fallback_notification', 'notifications@example.com');
            Mail::to($bcc)->send(new SendUserOpenMail($fc));
            Mail::to($bcc2)->send(new SendUserOpenMail($fc));
            Mail::to($fc['email'])->send(new SendUserOpenMail($fc));
            if (!empty($fc['email2'])) {
                Mail::to($fc['email2'])->send(new SendUserOpenMail($fc));
            }
            if (!empty($fc['email3'])) {
                Mail::to($fc['email3'])->send(new SendUserOpenMail($fc));
            }
        }
    }

    // CRONで送るエリア開放7日前に送るメール (毎日CRON)
    public function cronSendPreOpenMail()
    {
        \Log::debug(print_r('cronSendPreOpenMail()', true));
        $sub_day = Config::where('key', 'pre_area_open_email_days')->first();
        $contract_date = Carbon::now()->addDay($sub_day->value);
        \Log::debug(print_r('Contrace_date', true));
        \Log::debug(print_r($contract_date->format('Y-m-d'), true));
        // $contract_date = Carbon::parse('2023-08-02 00:00:00');
        // FC契約更新エリア開放メールにチェックが入っているFCだけを抽出
        $fcs = $this->model->select('users.*', 'aoes.id AS send_id', 'faa.name AS area_name')->whereIn('users.status', [1, 3, 4])
            ->where('aoes.status', 1)->where('aoes.send_status', 1)
            ->whereMonth('users.contract_date', $contract_date->format('m'))
            ->whereDay('users.contract_date', $contract_date->format('d'))
            ->whereDate('aoes.created_at', '>=', Carbon::now()->subMonth(2)->format('Y-m-d'))
            ->join('area_open_email_sends as aoes', 'aoes.user_id', '=', 'users.id')
            ->leftJoin('fc_apply_areas as faa', 'faa.id', '=', 'users.fc_apply_area_id')
            ->orderBy('users.id', 'ASC')->groupBy('users.id')->get()->toArray();
        \Log::debug(print_r('fcs', true));
        // \Log::debug(print_r($fcs, true));

        foreach ($fcs as $fc) {
            \Log::debug(print_r($fc['name'], true));
            // $start = Carbon::now()->addDay($sub_day)->subYear()->format('Y') . '-' . Carbon::parse($fc['contract_date'])->subYear()->format('m-d');
            $start = Carbon::now()->addDays($sub_day->value)->subYear()->format('Y') . '-' . Carbon::parse($fc['contract_date'])->format('m-d');
            // \Log::debug(print_r($start, true));
            // dd($start);
            $end = Carbon::now()->format('Y-m-d');
            $myself_count = $this->model->ownGetContactCount($fc['id'], $start, $end);
            \Log::debug(print_r('$myself_count', true));
            \Log::debug(print_r($myself_count, true));
            // 送信済みの処理
            if ($myself_count < config('app.area_open_count')) {
                \Log::debug(print_r("======== プレオープンメール " . $fc['id'] . " ========", true));
                // dd($myself_count);
                $bcc = 'bcc@shintou-s.jp';
                sleep(1);
                $bcc2 = config('mail.fallback_notification', 'notifications@example.com');
                sleep(1);
                Mail::to($bcc)->send(new SendUserPreOpenMail($fc, $sub_day->value, $myself_count));
                sleep(1);
                Mail::to($bcc2)->send(new SendUserPreOpenMail($fc, $sub_day->value, $myself_count));
                sleep(1);
                Mail::to($fc['email'])->send(new SendUserPreOpenMail($fc, $sub_day->value, $myself_count));
                sleep(1);
                if (!empty($fc['email2'])) {
                    Mail::to($fc['email2'])->send(new SendUserPreOpenMail($fc, $sub_day->value, $myself_count));
                    sleep(1);
                }
                if (!empty($fc['email3'])) {
                    Mail::to($fc['email3'])->send(new SendUserPreOpenMail($fc, $sub_day->value, $myself_count));
                    sleep(1);
                }
            }
        }
    }

    // 同エリアFCの自己獲得案件達成状況
    public function ajaxAreaOpenStatusToggle($id)
    {
        $status = AreaOpenEmailSend::find($id);
        $update_value = $status['send_status'] == 1 ? 0 : 1;
        AreaOpenEmailSend::where('id', $id)->update(['send_status' => $update_value]);

        return $id;
    }

    // 同エリアFCの自己獲得案件達成状況
    public function ajaxSameAreaFcStatus($userId = 1, $targetYear = 2022)
    {
        $return_array = [];
        $myself = $this->model->find($userId);

        $same_area_fcs = $this->model->select('users.*', \DB::raw('COUNT(c.id) AS own_contact_count'))
            ->whereIn('users.status', [1, 3, 4])->where('users.fc_apply_area_id', $myself['fc_apply_area_id'])
            ->leftJoin('contacts AS c', 'c.user_id', '=', 'users.id')
            ->groupBy('users.id')->get();

        foreach ($same_area_fcs as $key => $fc) {
            // 先に過去1年分も調べる
            $fc_contract_start = $targetYear - 2 . '-' . Carbon::parse($fc['contract_date'])->format('m-d');
            $fc_contract_end = $targetYear - 1 . '-' . Carbon::parse($fc['contract_date'])->format('m-d');
            $result = $this->model->ownContactCountOkCheck($fc['id'], $fc_contract_start, $fc_contract_end);
            $return_array[$key] = ['userid' => $fc['id'], 'name' => $fc['company_name'], 'result1' => $result];
            // 配列の関係上、今年の結果が後
            $fc_contract_start = $targetYear - 1 . '-' . Carbon::parse($fc['contract_date'])->format('m-d');
            $fc_contract_end = $targetYear . '-' . Carbon::parse($fc['contract_date'])->format('m-d');
            $result = $this->model->ownContactCountOkCheck($fc['id'], $fc_contract_start, $fc_contract_end);
            $return_array[$key]['result2'] = $result;
        }

        return response()->json($return_array);
    }
    // CRONでブランド使用料発生開始メール
    public function cronSend1yearMail()
    {
        $contract_date = Carbon::now()->subYear()->format('Y-m-d');

        $fcs = $this->model->whereDate('contract_date', $contract_date)
            ->where('role', 2)->whereIn('status', [1, 3, 4])
            ->orderBy('id', 'ASC')->get();

        foreach ($fcs as $fc) {
            // 1年経ったらブランド使用料に変更
            $bcc = 'bcc@shintou-s.jp';
            Mail::to($bcc)->send(new SendUserYearMail($fc));
            Mail::to($fc['email'])->send(new SendUserYearMail($fc));
            if (!empty($fc['email2'])) {
                Mail::to($fc['email2'])->send(new SendUserYearMail($fc));
            }
            if (!empty($fc['email3'])) {
                Mail::to($fc['email3'])->send(new SendUserYearMail($fc));
            }
        }
    }
    // CRONでブランド使用料に切り替える（メールの翌月に）
    public function cronSwichInvoicePaymentType()
    {
        // 契約日から1年後の翌月
        $contract_date_year = Carbon::now()->subYear()->subMonth()->format('Y');
        $contract_date_month = Carbon::now()->subYear()->subMonth()->format('m');

        $fcs = $this->model->where('role', 2)->whereIn('status', [1, 3, 4])
            ->whereYear('contract_date', $contract_date_year)
            ->whereMonth('contract_date', $contract_date_month)
            ->orderBy('id', 'ASC')->get();
        \Log::debug(print_r('======= cronSwichInvoicePaymentType()  =========', true));

        foreach ($fcs as $fc) {
            \Log::debug(print_r($fc['id'], true));
            // 1年経ったらブランド使用料に変更
            $this->model->where('id', $fc['id'])->update(['invoice_payments_type' => 2]);
        }
    }
}
