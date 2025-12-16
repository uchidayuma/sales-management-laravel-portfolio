<?php

use App\Models\Contact;
use App\Models\Product;
use App\Models\Step;
use App\Models\User;
use App\Models\Transaction;
use Carbon\Carbon;
use Yasumi\Yasumi;
use Illuminate\Support\Arr;

// 管理者権限ならTRUE
function isAdmin()
{
    if (!\Auth::user()) {
        return false;
    }
    $admin = \Auth::user()->role;
    if ($admin == '1') {
        return true;
    } else {
        return false;
    }
}

// FCアカウントならTRUE
function isFc()
{
    if (!\Auth::user()) {
        return false;
    }
    $admin = \Auth::user()->role;
    if ($admin != '1') {
        return true;
    } else {
        return false;
    }
}

// 管理者権限なら以外リダイレクト
function adminOnly()
{
    if (isFc()) {
        return Response::make('Unauthorized', 401);
    } else {
        return true;
    }
}

// ボタンをアクティブ判断 → 引数boolは正誤判断
function isActiveBtn($bool)
{
    if ($bool) {
        return 'btn-primary';
    } else {
        return null;
    }
}

// 管理者権限なら class='hidden'を付与
function adminOnlyHidden()
{
    $admin = \Auth::user()->role;
    if ($admin != '1') {
        return 'hidden';
    } else {
        return '';
    }
}

// TRUEなら class='hidden'を付与
function isHidden($args)
{
    if ($args) {
        return 'hidden';
    } else {
        return '';
    }
}

// TRUEならselectedを返す
function selected($cond)
{
    if ($cond) {
        return ' selected';
    } else {
        return null;
    }
}

// TRUEならcheckedを返す
function checked($cond)
{
    if ($cond) {
        return ' checked';
    } else {
        return null;
    }
}

// 市町村だけを抽出
function dripCity($str)
{
    $city = explode('市', $str);
    if(array_key_exists(1, $city) && strlen($city[0]) > 1){
        return $city[0] . '市';
    }
    $city = explode('区', $str);
    if(array_key_exists(1, $city) && strlen($city[0]) > 1){
        return $city[0] . '区';
    }
    $city = explode('町', $str);
    if(array_key_exists(1, $city) && strlen($city[0]) > 1){
        return $city[0] . '町';
    }
    $city = explode('村', $str);
    if(array_key_exists(1, $city) && strlen($city[0]) > 1){
        return $city[0] . '村';
    }
    return '市町村不明';
}

function s3url()
{
    switch (\App::environment()) {
        case 'localhost':
            return 'https://sales-management-local.s3-ap-northeast-1.amazonaws.com/';
            break;
        case 'testing':
            return 'https://sales-management-testing.s3-ap-northeast-1.amazonaws.com/';
            break;
        case 'circleci':
            return 'https://sales-management-testing.s3-ap-northeast-1.amazonaws.com/';
            break;
        case 'production':
            return 'https://sales-management-production.s3-ap-northeast-1.amazonaws.com/';
            break;

        default:
            return 'https://sales-management-local.s3-ap-northeast-1.amazonaws.com/';
            break;
    }
}

function isStockMargin($high, $low, $stock)
{
    switch (true) {
        case $stock >= $high:
            return '<button class="btn btn-primary btn-stock stock-high" type="button">    </button>';
            break;

        case $high > $stock && $stock > $low:
            return '<button class="btn btn-warning btn-stock stock-middle" type="button">    </button>';
            break;

        case $stock <= $low:
            return '<button class="btn btn-danger btn-stock stock-low" type="button">    </button>';
            break;

        default:
            return '<button class="btn btn-primary btn-stock stock-high" type="button">    </button>';
            break;
    }
}

function getExtention($filename)
{
    return substr($filename, strrpos($filename, '.') + 1);
}

function removeHyphen($string)
{
    return str_replace(['-', 'ー', '−', '―', '‐'], '', $string);
}

function returnContactTypeId($contact_id = null)
{
    $contact = Contact::where('id', $contact_id)->first();
    if(is_null($contact['contact_type_id'])){
        return null;
    }else{
        return $contact['contact_type_id'];
    }
}

function returnStepLabel($step, $cancelStep)
{
    // $labels = '<img class="step__label mb5 mr5" src="/images/icons/steps/1.png">';
    $labels = '';
    // キャンセルステップがあれば、キャンセルになったステータスのラベルまでレンダリング
    if (!is_null($cancelStep)) {
        $step = $cancelStep;
    }
    for ($i = 2; $i < intval($step) + 1; ++$i) {
        // 工程が終わったらラベルを表示したいので、stepId -1 FCへの依頼が終わったら 1.pngを表示する
        $clearStep = $i - 1;
        if ($clearStep != 7 && $clearStep != 8) {
            $labels .= '<img class="step__label mb5 mr5" src="/images/icons/steps/' . $clearStep . '.png">';
        }
        if ($i >= 11) {
            break;
        }
    }
    if (!is_null($cancelStep)) {
        $labels .= '<img class="step__label mb5 mr5" src="/images/icons/steps/99.png">';
    }
    // step_idが100の場合 過去顧客ラベル追加
    if ($step == 100) {
        $labels .= '<img class="step__label mb5 mr5" src="/images/icons/steps/100.png">';
    }

    return $labels;
}
function sampleSend()
{
    return '<img class="step__label mb5 mr5" src="/images/icons/steps/sample-sended.png">';
}

//メール送って良いかの判断
function isAllowEmailUser($user_id)
{
    if(is_null($user_id)){return false;}
    $isAllow = User::where('id', $user_id)->value('allow_email');
    if (config('app.env') == 'circleci') {
        return false;
    }
    if ($isAllow == '1') {
        return true;
    } else {
        return false;
    }
}

/**
 * 多次元連想配列を指定したキーで整形.
 */
function arrayToMultiColumn($values, $keys = [], $filter = null)
{
    if (is_array($keys) && empty($keys) || $keys === null) {
        return $values;
    }
    $enable_filter = $filter !== null && is_callback($filter);
    $keys = (array) $keys;
    $ret = [];
    foreach ($values as $row) {
        $tmp = &$ret;
        foreach ($keys as $key) {
            $tmp = &$tmp[$row[$key]];
        }
        if ($enable_filter) {
            $row = $filter($row);
        }
        $tmp = $row;
    }

    return $ret;
}

function alertDateTime($updated_at, $period)
{
    //ステータス変更年月日とアラート発生日数を定義
    $startDate = new Carbon($updated_at);
    $carbon = new Carbon($updated_at);
    $endDate = $carbon->addDay($period);

    // 発生日とアラート日数の間に何日土日祝日があるか計算
    $weekends = (int) $startDate->diffInDaysFiltered(
        function (Carbon $date) {
            return $date->isWeekend();
        },
        $endDate->addDay()
    );
    // 終了日自体を休日にカウントしてくれないので1日足した後、元に戻す
    $endDate->subDay();

    // 祝日を取得
    $publicHolidays = Yasumi::create('Japan', $startDate->year, 'ja_JP');
    $publicHolidaysInBetweenDays = $publicHolidays->between(new DateTime($startDate->format('m/d/Y')), new DateTime($endDate->format('m/d/Y')));

    $numberOfHoliday = 0;
    foreach ($publicHolidaysInBetweenDays as $holiday) {
        $holidayDate = new Carbon($holiday);
        if ($holidayDate->isWeekend() === false || $holidayDate == $endDate) {
            ++$numberOfHoliday;
        }
    }
    // 年を跨ぐ場合
    if ($startDate->year != $endDate->year) {
        $nextPublicHolidays = Yasumi::create('Japan', $startDate->addYear()->year, 'ja_JP');
        $nextPublicHolidaysInBetweenDays = $nextPublicHolidays->between(new DateTime($startDate->subYear()->format('m/d/Y')), new DateTime($endDate->format('m/d/Y')));

        foreach ($nextPublicHolidaysInBetweenDays as $holiday) {
            $holidayDate = new Carbon($holiday);
            // echo $holidayDate->diffInDays($endDate) === 1;
            if ($holidayDate->isWeekend() === false || $holidayDate == $endDate) {
                ++$numberOfHoliday;
            }
        }
    }

    $sumHoliday = $weekends + $numberOfHoliday;
    $alertDay = $endDate->addDay($sumHoliday + intval($period));
    // \Log::debug(print_r('======  CRON通知 =========', true));
    // \Log::debug(print_r($alertDay, true));

    while ($alertDay->isWeekend()) {
        $alertDay->addDay();
        $publicHolidaysInBetweenDays = $publicHolidays->between(new DateTime($startDate->format('m/d/Y')), new DateTime($alertDay->format('m/d/Y')));
        foreach ($publicHolidaysInBetweenDays as $holiday) {
            $holidayDate = new Carbon($holiday);
            if ($holidayDate == $alertDay) {
                $alertDay->addDay();
            }
        }
    }

    return $alertDay;
}

function alertInView($updated_at, $period)
{
    $carbon = new Carbon();
    $limitDay = alertDateTime($updated_at, $period);
    \Log::debug($limitDay);
    if (strtotime($carbon::today()) > strtotime($limitDay)) {
        return true;
    } else {
        return false;
    }
}

// 自己案件比率
function contactRate($id, $count)
{
    //担当案件が1件以上のとき
    $conditions = ['user_id' => $id, 'status' => 1, 'own_contact' => 1];
    $ownContact = Contact::where($conditions)->count();
    //dd($ownContact);
    $contactRate = round(($ownContact / $count) * 100);
    //dd($contactRate);
    return $contactRate;
}

// 訪問不要案件の判定
function isMaterialOnly($contactId)
{
    $contact = Contact::where('id', $contactId)->first();
    if ($contact->quote_details == '材料のみ') {
        if ($contact->contact_type_id != '2' || $contact->contact_type_id != '6') {
            //id=2:個人図面見積もりまたはid=6:法人図面見積もりなら見積もりボタン非表示
            return false;
        }
    } else {
        return true;
    }
}

// 案件配列 or オブジェクトから企業案件か否かを返す
function isCompany($contact)
{
    if (is_array($contact)) {
        if ($contact['contact_type_id'] > 4) {
            return true;
        } else {
            return false;
        }
    } else {
        if ($contact->contact_type_id > 4) {
            return true;
        } else {
            return false;
        }
    }
    // 引数がおかしい場合はnullを返す
    return null;
}

// 案件配列 or オブジェクトから顧客名を返す
function customerName($contact)
{
    if (is_array($contact)) {
        if ($contact['contact_type_id'] > 4) {
            return $contact['company_name'];
        } else {
            return $contact['surname'] . $contact['name'];
        }
    } else {
        if ($contact->contact_type_id > 4) {
            return $contact->company_name;
        } else {
            return $contact->surname . $contact->name;
        }
    }
    // 引数がおかしい場合はnullを返す
    return null;
}

//依頼種別を判定
function returnContactType($contactTypeId)
{
    switch ($contactTypeId) {
        case '1':
            return ' サンプル';
            break;
        case '5':
            return 'サンプル';
            break;
        case '2':
            return '図面見積もり';
            break;
        case '6':
            return '図面見積もり';
            break;
        case '3':
            return '訪問見積もり';
            break;
        case '7':
            return '訪問見積もり';
            break;
        case '4':
            return 'その他';
            break;
        case '8':
            return 'その他';
            break;
        default:
            return 'その他';
            break;
    }
}

// 配列にある問い合わせタイプならtrue
function isContactType( $target_id = 1, $type_array = [] ){
    $result = false;
    foreach($type_array as $t){
        if($target_id == $t){
            $result = true;
        }
    }
    return $result;
}

function stepName($step_id)
{
    $step = Step::where('id', $step_id)->first();

    return $step->name;
}

function returnTransportCompany($id)
{
    switch ($id) {
        case '1':
            return '西濃運輸';
            break;
        case '2':
            return 'ヤマト運輸';
            break;
        case '3':
            return '佐川急便';
            break;
        case '4':
            return '日本郵政';
            break;
        case '5':
            return 'チャーター便';
            break;
        case '6':
            return '工場引き取り';
            break;
        case '7':
            return 'メーカー直送手配';
            break;
        case null:
            return '発送前';
        default:
            return '';
            break;
    }
    $step = Step::where('id', $step_id)->first();

    return $step->name;
}
function returnInvoicePaymentsType($id)
{
    switch ($id) {
        case '0':
            return '請求なし';
            break;
        case '1':
            return 'HP掲載料';
            break;
        case '2':
            return 'ブランド使用料';
            break;

        default:
            return 'HP掲載料';
            break;
    }
}
function isProduction()
{
    if (config('app.env') == 'production') {
        return true;
    } else {
        return false;
    }
}
function transactionPrice($transaction)
{
    if (!empty($transaction['discount'])) {
        return $transaction['total'] - $transaction['discount'];
    } else {
        return $transaction['total'];
    }
}
// 以下 array_filter用の関数
function isTurf($value)
{
    return $value['product_type_id'] == '1';
}

function isSub($value)
{
    return $value['product_type_id'] == '2';
}

function isSales($value)
{
    return $value['product_type_id'] == '3';
}

function isCut($value)
{
    return $value['product_type_id'] == '4';
}

function isProducts($value, $id)
{
    return $value['id'] == $id;
}

function productsFilterById($products, $id)
{
    $product = array_filter($products, function ($p) use ($id) {
        return ($p['id'] == $id);
    });
    return Arr::collapse($product);
}

function isCutSubitem($product_id, $unit)
{
    $product = Product::where('id', $product_id)->first();
    if ($product['product_type_id'] == '2' && ($product['unit'] != $unit)) {
        return true;
    } else {
        return false;
    }
}

function myArrayFilter($array, $filter_key, $filter_val)
{
    foreach ($array as $row) {
        if ($row[$filter_key] == $filter_val) {
            return $row;
        }
    }
}

function floatNumberFormat($num, $decimal = 1)
{
    // $decimalは小数点以下の表示桁数(デフォルト引数が1なので、デフォルトは少数第1位まで表示
    if (preg_match('/^([1-9]\d*|0)\.(\d+)?$/', $num)) {
        return number_format($num, $decimal, null, ',');
    } else {
        return number_format($num);
    }
}

// FCの案件の場合FCのIDを表示させる。
function displayContactId($contact = [])
{
    $id = 0;
    if(is_array($contact)){
        if ($contact['own_contact'] == '1' && !empty($contact['user_id'])) {
            $id = $contact['user_id'] . '-' . $contact['id'];
        } else {
            $id = $contact['id'];
        }
    }else{
        if ($contact->own_contact == '1' && !empty($contact->user_id)) {
            $id = $contact->user_id . '-' . $contact->id;
        } else {
            $id = $contact->id;
        }

    }
    return $id;
}

function transactionChangeAble($create_time = null)
{
    $now = Carbon::now();
    $limitTime = Carbon::parse($create_time)->addDay()->format('Y-m-d');
    $limitTime = Carbon::parse($limitTime . ' 10:59:59');
    $changeAble = $now->lte($limitTime);

    return $changeAble;
}

function transactionCount($contact_id = null)
{
    $count = Transaction::whereNotNull('contact_id')->where('contact_id', $contact_id)->where('status', 1)->count();

    return $count;
}

function  isMainTransaction($contact_id = null, $transaction_id = null){
    // 一番最初に登録されたTransaction_idを持ってくる。
    $main_transactin_id = Transaction::where('contact_id', $contact_id)->where('status',1)->min('id');
    if ($transaction_id == $main_transactin_id){
        return true;
    } else {
        return false;
    }
}

function subTransactionLimitDate($contact_id = null){

    // 最初の発注を挙げてから1ヶ月以内であれば追加発注ができる
    $main_transactin_create_date = Transaction::where('contact_id', $contact_id)->where('status',1)->min('created_at');
    // １ヶ月(30日)
    $limit_date = Carbon::parse($main_transactin_create_date)->addMonth();
    $now_date = Carbon::now();

    if(empty($contact_id)){
        // 案件がない場合はfalse
        return false;
    } elseif(!empty($contact_id) && $now_date <= $limit_date){
        // 案件があるかつ メイン発注が作成されてから１ヶ月以内がtrue
        return true;
    } else {
        // それ以外はfalse
        return false;
    }
    
}
// contact の step_id を返す。
function nowStepId($id) {
    $now_step_id = Contact::where('id',$id)->value('step_id');
    return $now_step_id;
}

// horizontal と vertical が設定されている場合その値を（2m ×10m）の形で返す
function productArea($products,$product_id) {
    $selected_product;
    foreach($products as $p){
       if($p['id'] == $product_id){
           $selected_product = $p;
       }
    }
    if ( !empty($selected_product['horizontal']) && !empty($selected_product['vertical'])){
        $product_area = "（".$selected_product['horizontal']."m ×".$selected_product['vertical']."m）";
        return $product_area;
    }
}

function transactionDestroyToSlack($user, $transaction_id, $delete_location){
    if( is_null($transaction_id)){
        return false;
    }
    $message = [
        "channel" => "#システム開発チーム",
        "text" => $user['name'] . "が発注書No." . $transaction_id . "を削除しました。" . "($delete_location)",
    ];

    $ch = curl_init();
    $options = [
        CURLOPT_URL => env('SLACK_WEBHOOK_URL', ''),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'payload' => json_encode($message)
        ])
    ];
    if(config('app.env') == 'production'){
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
    }
}

function toSlack($url = '', $channel = '#開発チーム', $message = 'No.の発注書の金額がズレています'){
    if (empty($url)) {
        $url = env('SLACK_WEBHOOK_URL', '');
    }
    $message = [
        "channel" => $channel,
        "text" => $message,
    ];

    $ch = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'payload' => json_encode($message)
        ])
    ];
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
}

function convertPriceFieldName($filename = '')
{
    $return_name = '';
    switch ($filename) {
        case 'price':
            $return_name = '一般価格';
            break;
        case 'whole_price':
            $return_name = '卸売価格';
            break;
        case 'fc_price':
            $return_name = 'FC価格';
            break;
        case 'cut_price':
            $return_name = '一般切り売り価格';
            break;
        case 'whole_cut_price':
            $return_name = '卸売切り売り価格';
            break;
        case 'fc_cut_price':
            $return_name = 'FC切り売り価格';
            break;
    }

    return $return_name;
}

function monthDiff($start, $end)
{
    $month1   = date('Y', strtotime($start)) * 12 + date('m', strtotime($start));
    $month2   = date('Y', strtotime($end)) * 12 + date('m', strtotime($end));
    return $month2 - $month1;
}

function sortOderType($oderType = hogeDesecData, $setOderTypeAsc = ascendingDeliveryDate, $selectOderDesc = descendingDeliveryDate)
{
    if( empty($oderType) || $oderType == $selectOderDesc){
        return $setOderTypeAsc;
    } else {
        return $selectOderDesc;
    }
}

function sortOderIcon($oderType = hogeDesecData, $setOderTypeAsc = ascendingDeliveryDate)
{
    if($oderType == $setOderTypeAsc){
        $result = 'up';
    } else {
        $result = 'down';
    }
    return $icon = '<i class="btn btn-primary py-1 px-2 ml-2 fas fa-caret-'.$result.'"></i>';
}

function environmentalCharacterConversion($environmentalCharacter = hogeEnvironmentalCharacter)
{
    switch ($environmentalCharacter) {
        case '㎟':
            return 'mm²';
            break;
        case '㎠':
            return 'cm²';
            break;
        case '㎡':
            return 'm²';
            break;
        case '㎢':
            return 'km²';
            break;
        case '㎣':
            return 'mm³';
            break;
        case '㎤':
            return 'cm³';
            break;
        case '㎥':
            return 'm³';
            break;
        case '㎦':
            return 'km³';
            break;

        default:
            return $environmentalCharacter;
            break;
    }
}

// 発注書のキャンセル可能日時を計算
function transactionCancelAble($create_time = null)
{
    $now = Carbon::now();
    $limitTime = Carbon::parse($create_time)->addDay()->format('Y-m-d');
    $limitTime = Carbon::parse($limitTime . ' 10:59:59');
    $cancelAble = $now->lte($limitTime);

    return $cancelAble;
}

// 西暦を和暦に変換
function westernYearToJapaneseYear($year = 2022)
{
    $eras = array(
        array('year' => 2018, 'name' => '令和'),
        array('year' => 1988, 'name' => '平成'),
        array('year' => 1925, 'name' => '昭和'),
        array('year' => 1911, 'name' => '大正'),
        array('year' => 1867, 'name' => '明治')
    );

    foreach($eras as $era) {

        $base_year = $era['year'];
        $era_name = $era['name'];

        if($year > $base_year) {

            $era_year = $year - $base_year;

            if($era_year === 1) {
                return $era_name .'元年';
            }

            return $era_name . $era_year .'年';
        }

    }
    return null;
}

function contactsColumnToJapanese($columnName) {
    $contactsColumnMapping = [
        'id' => '案件ID',
        'contact_type_id' => '案件種別',
        'user_id' => 'ユーザーID',
        'main_user_id' => 'メインユーザーID',
        'registered_user_id' => '登録ユーザーID',
        'step_id' => 'ステップID',
        'cancel_step' => 'キャンセルステップID',
        'quotation_id' => '選択した見積書ID',
        'free_sample' => '無料サンプル',
        'email' => 'メールアドレス',
        'fax' => 'FAX番号',
        'tel' => '電話番号',
        'zipcode' => '郵便番号',
        'pref' => '都道府県',
        'city' => '市区町村',
        'street' => '番地等',
        'ground_condition' => '下地状況',
        'desired_datetime1' => '訪問希望日時1',
        'desired_datetime2' => '訪問希望日時2',
        'finished_datetime' => '施工完了日',
        'visit_address' => '訪問住所',
        'square_meter' => '平米数',
        'use_application' => '人工芝の使用用途',
        'surname' => '姓',
        'name' => '名前',
        'surname_ruby' => '姓（フリガナ）',
        'name_ruby' => '名（フリガナ）',
        'ruby' => 'フリガナ',
        'company_name' => '会社名',
        'company_ruby' => '会社名（フリガナ）',
        'industry' => '業種',
        'quote_details' => '見積もり内容',
        'vertical_size' => '施工場所の縦のサイズ',
        'horizontal_size' => '施工場所の横のサイズ',
        'desired_product' => '希望商品',
        'comment' => 'コメント',
        'age' => '年代',
        'requirement' => '必要事項',
        'where_find' => '情報取得元',
        'sns' => 'SNSの認知',
        'visit_time' => 'アポ日時',
        'shipping_id' => '発送業者ID',
        'shipping_number' => '発送番号',
        'shipping_date' => '発送日',
        'before_image1' => '訪問前写真1',
        'before_image2' => '訪問前写真2',
        'before_image3' => '訪問前写真3',
        'after_image1' => '施工後写真1',
        'after_image2' => '施工後写真2',
        'after_image3' => '施工後写真3',
        'public' => '画像の公開設定',
        'own_contact' => '独自コンタクト',
        'status' => 'ステータス',
        'fc_assigned_at' => 'FCアサイン日',
        'fc_confirmed_at' => 'FC確認日',
        'contracted_at' => '契約日',
        'completed_at' => '完了報告日',
        'updated_at' => '更新日',
        'created_at' => '作成日',
        'document1' => '添付資料1',
        'document1_original_name' => '添付資料1の元のファイル名',
        'document2' => '添付資料2',
        'document2_original_name' => '添付資料2の元のファイル名',
        'document3' => '添付資料3',
        'document3_original_name' => '添付資料3の元のファイル名',
        'document4' => '添付資料4',
        'document4_original_name' => '添付資料4の元のファイル名',
        'document5' => '添付資料5',
        'document5_original_name' => '添付資料5の元のファイル名',
        'tel2' => '電話番号2',
        'memo' => 'メモ',
        'finish_memo' => '完了報告メモ',
        'etc_memo' => 'その他のメモ',
        'sample_send_at' => 'サンプル送付日',
        'free_sample_required' => '無料サンプル要求',
        'same_customer' => '同一顧客ID',
        'vertical_horizontal' => '施工場所面積',
        'fc_id' => '担当fc会社名',
    ];
    
    return $contactsColumnMapping[$columnName] ?? $columnName;
}