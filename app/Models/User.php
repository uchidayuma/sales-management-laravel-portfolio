<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use App\Notifications\ResetPasswordCustom;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Models\Transaction;

class User extends Authenticatable
{
    //use MustVerifyEmail, Notifiable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getNearFcs($latitude, $longitude, $distance = null)
    {
        $distance = empty($distance) ? 100 : $distance;
        $carbon = new Carbon();
        // 三角関数で距離を計算
        $franchises = $this->selectRaw('users.*, users.id AS id, c.id AS contact_id, faa.name AS area_name,
            COUNT(c.id) AS year_count, COUNT(c.step_id in(2,3,4,5,6,7,8) AND c.created_at = ' . $carbon->year . ' OR NULL) AS progress_count,
            ( 6371 * acos( cos( radians(' . $latitude . ') ) *
            cos( radians(users.latitude) ) *
            cos( radians(users.longitude) - radians(' . $longitude . ') ) +
            sin( radians(' . $latitude . ') ) *
            sin( radians(users.latitude) ) ) )
            AS distance')
            ->where('role', 2)
            ->where('users.status', 1)
            // ->whereYear('c.created_at', $carbon->year)
            ->leftJoin('contacts AS c', 'c.user_id', '=', 'users.id')
            ->leftJoin('fc_apply_areas AS faa', 'faa.id', '=', 'users.fc_apply_area_id')
            ->having('distance', '<', $distance)
            ->groupBy('users.id')
            ->orderBy('distance')
            ->get()->toArray();

        return $franchises;
    }

    public function contact()
    {
        return $this->hasMany('App\Models\Contact');
    }

    public function postFreee($inputs)
    {
        $refresh_token = User::find(1)->freee_refresh_token ? User::find(1)->freee_refresh_token : '';
        $refreshAccessToken = Transaction::refreshFreeeToken($refresh_token);

        $freee = User::where('role', 1)->get(array('freee_user_id', 'freee_access_token'))->toArray();
        $freeeCompanyId = $freee[0]['freee_user_id'];
        $freeeAccessToken = $freee[0]['freee_access_token'];

        if (array_key_exists('is_personal', $inputs['fc'])) {
            $title = '様';
        } else {
            $title = '御中';
        }
        unset($inputs['fc']['is_personal']);

        $pref = $inputs['fc']['pref'];

        $arrayPref = [
            0 => '北海道',
            1 => '青森県',
            2 => '岩手県',
            3 => '宮城県',
            4 => '秋田県',
            5 => '山形県',
            6 => '福島県',
            7 => '茨城県',
            8 => '栃木県',
            9 => '群馬県',
            10 => '埼玉県',
            11 => '千葉県',
            12 => '東京都',
            13 => '神奈川県',
            14 => '新潟県',
            15 => '富山県',
            16 => '石川県',
            17 => '福井県',
            18 => '山梨県',
            19 => '長野県',
            20 => '岐阜県',
            21 => '静岡県',
            22 => '愛知県',
            23 => '三重県',
            24 => '滋賀県',
            25 => '京都府',
            26 => '大阪府',
            27 => '兵庫県',
            28 => '奈良県',
            29 => '和歌山県',
            30 => '鳥取県',
            31 => '島根県',
            32 => '岡山県',
            33 => '広島県',
            34 => '山口県',
            35 => '徳島県',
            36 => '香川県',
            37 => '愛媛県',
            38 => '高知県',
            39 => '福岡県',
            40 => '佐賀県',
            41 => '長崎県',
            42 => '熊本県',
            43 => '大分県',
            44 => '宮崎県',
            45 => '鹿児島県',
            46 => '沖縄県',
        ];

        $prefCode = array_search($pref, $arrayPref);
        //dd($prefCode);

        //$arrayPrefから検索

        $address = [
            'zipcode' => $inputs['fc']['zipcode'],
            'pref' => $prefCode,
            'city' => $inputs['fc']['city'],
            'street' => $inputs['fc']['street'],
        ];
        //dd($address);

        $body = [
            'company_id' => $freeeCompanyId,
            'name' => $inputs['fc']['company_name'],
            'default_title' => $title,
            'phone' => $inputs['fc']['tel'],
            'address_attributes[zipcode]' => $inputs['fc']['zipcode'],
            'address_attributes[prefecture_code]' => $prefCode,
            'address_attributes[street_name1]' => $inputs['fc']['city'] . $inputs['fc']['street'],
            'contact_name' => $inputs['fc']['staff'],
            'email' => $inputs['fc']['email'],
        ];
        //dd($body);

        $ch = curl_init();

        $header = ['Accept: application/json', 'Authorization: Bearer ' . $freeeAccessToken, 'Content-Type: application/json'];

        curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/api/1/partners');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result = json_decode($result, true);
    }

    /**
     * パスワード再設定メールの送信
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    public function ownContactCountOkCheck($user_id = 0, $start = '', $end = '')
    {
        /*
        $count = $this->selectRaw('COUNT(c.id) AS contact_count')
            ->leftJoin('contacts as c', 'c.user_id', '=', 'users.id')
            ->where('c.user_id', $user_id)->where('c.status', 1)
            ->where('c.own_contact', 1)->whereBetween('c.step_id', [1,11])
            ->whereBetween('c.created_at', [$start, $end])
            ->get();
        */
        $count = $this->ownGetContactCount($user_id, $start, $end, false);
        \Log::debug(print_r($count, true));

        return $count >= config('app.area_open_count');
    }

    // 指定期間の自己獲得案件件数（資材発注のみも含む）
    public function ownGetContactCount($user_id = 0, $start = '2022-01-01', $end = '2023-01-01', $return_json = false)
    {
        \Log::debug(print_r('FC ID = ' . $user_id, true));
        \Log::debug(print_r($start, true));
        \Log::debug(print_r($end, true));
        // - 自己獲得案件（contacts.own_contact=1）で発注が済んでいるもの
        $fc_query_count = Transaction::query()
            // ->select(DB::raw('COUNT(*) AS transaction_count'))
            ->where('transactions.status', 1)->where('transactions.user_id', $user_id)
            ->where('c.own_contact', 1)->where('c.status', 1)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->join('contacts as c', 'transactions.contact_id', '=', 'c.id')
            // ->groupBy('c.id')
            ->count();
        // $fc_query_count = $fc_query->sum('transaction_count');
        // ->get()->count();
        \Log::debug(print_r('自己獲得案件で発注が済んでいるもの' . $fc_query_count . '件', true));
        // $fc_only_query = 案件に紐付かない資材発注をカウント
        $fc_only_query = Transaction::query()
            ->selectRaw('COUNT(DISTINCT transactions.id) AS transaction_count')
            ->where('transactions.status', 1)->whereNull('transactions.contact_id')
            ->where('transactions.user_id', $user_id)
            ->whereBetween('transactions.created_at', [$start, $end])
            // 自己獲得案件に紐づく発注 or 自己獲得案件なら副資材だけでもカウントする or 案件に紐付かない案件（芝の注文）ならカウント
            ->where(function ($query) {
                // 案件に紐付かない案件（芝の注文）ならカウント(自由記述に書かれた芝も拾う 
                $query->orWhere('pr.product_type_id', 1)
                    ->orWhere('pt.other_product_name', 'LIKE', "%芝%")
                    ->orWhere('pt.other_product_name', 'LIKE', "%本部見積書%")
                    ->orWhere('pt.other_product_name', 'LIKE', "%芝%");
            })
            ->join('product_transactions as pt', 'transactions.id', '=', 'pt.transaction_id')
            ->join('products as pr', 'pt.product_id', '=', 'pr.id')
            ->first();
        \Log::debug(print_r('案件に紐付かない発注' . $fc_only_query['transaction_count'] . '件', true));
        // - 自己獲得案件（contacts.own_contact=1）で発注をスキップしているもの
        $fc_skip_query = Contact::query()
            ->whereNull('t.id')->where('contacts.user_id', $user_id)
            ->where('contacts.status', 1)->where('contacts.own_contact', 1)
            ->whereIn('contacts.step_id', [9, 10, 11])
            ->whereBetween('contacts.created_at', [$start, $end])
            // ->leftJoin('users as u', 'contacts.user_id', '=', 'u.id' )
            ->leftJoin('transactions as t', 'contacts.id', '=', 't.contact_id')
            ->count();
        // ->groupBy('contacts.id')->count();
        // \Log::debug(print_r($fc_skip_query->toSql(), true));
        \Log::debug(print_r('案件に紐付くSKIP' . $fc_skip_query . '件', true));

        $total = $fc_query_count + $fc_only_query['transaction_count'] + $fc_skip_query;
        // \Log::debug(print_r('TOTAL : ' . $total . '件', true));
        return $total;
        // $union_table = $fc_query->union($fc_only_query)->union($fc_skip_query);
        // $tmp_results = DB::query()->fromSub($union_table, 'ut')
        //     ->select(DB::raw('SUM(transaction_count) AS transaction_count'), 'ut.user_id')
        //     ->groupBy('ut.user_id')->get()->toArray();

        // return !empty($tmp_results[0]) ? $tmp_results[0]->transaction_count : 0;
    }

    public function areaOpenEmailSends($date = '2022-12-21')
    {
        $date = Carbon::parse($date);
        $year = $date->format('m') == 1 ? intval($date->format('Y') - 1) : $date->format('Y');
        $fcs = $this->select('users.*', 'aoes.id AS send_id')->whereIn('users.status', [1, 3, 4])
            ->where('aoes.status', 2)->where('aoes.send_status', 1)
            ->whereMonth('users.contract_date', $date->format('m'))
            ->whereDay('users.contract_date', $date->format('d'))
            ->whereYear('aoes.created_at', $year)
            ->join('area_open_email_sends as aoes', 'aoes.user_id', '=', 'users.id')
            ->orderBy('users.id', 'ASC')->groupBy('users.id')->get();
        \Log::debug(print_r('昨日エリア開放メールを送った areaOpenEmailSends', true));
        \Log::debug(print_r($fcs, true));

        return $fcs;
    }
}
