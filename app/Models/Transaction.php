<?php

namespace App\Models;

use App\Mail\SendTransactionDispatchCustomerMail;
use App\Mail\SendTransactionDispatchMail;
use Carbon\Carbon;
use Mail;
use DB;

class Transaction extends MyModel
{
    protected $table = 'transactions';
    // JSONカラムを配列に変換して扱うには $castsを設定する必要あり
    protected $casts = [
        'turf_cuts' => 'json',
    ];

    public function index($user_id = null, $step_id = null)
    {
        $query = Transaction::query()->select('transactions.*', 'transactions.id AS transaction_id', 'c.*', 'u.name AS fc_name')
            ->where('transactions.status', 1)
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->groupBy('transactions.id')
            ->orderBy('c.updated_at', 'ASC');
        if ($user_id) {
            $query->where('transactions.user_id', $user_id);
        }
        if ($step_id) {
            $query->where('c.step_id', $step_id);
        }
        $results = $query->get();

        foreach ($results as $key => $val) {
            $transactions[$key] = $this->preview($val['transaction_id']);
        }

        return $transactions;
    }

    // 請求書に表示する金額の計算
    public function returnShowPrice($transaction)
    {
        $sub_total = 0;
        $tax = 0;
        $total = 0;
        if (isAdmin() && ($transaction['discount'] || $transaction['special_discount'])) {
            $sub_total =  $transaction['total'] + $transaction['shipping_cost'] - $transaction['discount'] - $transaction['special_discount'];
            $tax = floor(($transaction['total'] + $transaction['shipping_cost'] - $transaction['discount'] - $transaction['special_discount']) * config('app.tax_rate'));
            $total = $sub_total + $tax;
        } elseif (isFc() && !is_null($transaction['shipping_cost'])) {
            $sub_total = $transaction['total'] + $transaction['shipping_cost'] - $transaction['discount'] - $transaction['special_discount'];
            $tax = floor(($transaction['total'] + $transaction['shipping_cost'] - $transaction['discount'] - $transaction['special_discount']) * config('app.tax_rate'));
            $total = $sub_total + $tax;
        } elseif (!is_null($transaction['shipping_cost'])) {
            $sub_total = $transaction['total'] + $transaction['shipping_cost'];
            // dd(($transaction['total'] + $transaction['shipping_cost'])* config('app.tax_rate')) ;
            $tax = floor(($transaction['total'] + $transaction['shipping_cost']) * config('app.tax_rate'));
            $total = $sub_total + $tax;
        } else {
            $sub_total =  $transaction['total'];
            $tax = floor($transaction['total'] * config('app.tax_rate'));
            $total = $sub_total + $tax;
        }
        return [$sub_total, $tax, $total];
    }

    public function preview($id)
    {
        $query = $this->select(
            'transactions.*',
            'transactions.user_id AS transaction_user_id',
            'c.*',
            'transactions.created_at AS transaction_time',
            'u.name AS fc_name',
            'u.staff AS fc_staff',
            'u.zipcode AS fc_zipcode',
            'u.pref AS fc_pref',
            'u.city AS fc_city',
            'u.street AS fc_street',
            'u.tel AS fc_tel',
            'u.s_zipcode AS fc_s_zipcode',
            'u.s_pref AS fc_s_pref',
            'u.s_city AS fc_s_city',
            'u.s_street AS fc_s_street',
            'u.storage_tel AS fc_storage_tel',
            'u.seal AS seal',
            'pt.*',
            // 2022年4月1日の価格変更に後から対応するため
            'pt.unit_price AS pt_unit_price',
            DB::raw('(CASE WHEN (transactions.created_at < "2022-04-01 00:00:00") THEN op.fc_price ELSE p.fc_price END) AS fc_price'),
            DB::raw('(CASE WHEN (transactions.created_at < "2022-04-01 00:00:00") THEN op.cut_fc_price ELSE p.cut_fc_price END) AS cut_fc_price'),
            // ここまで2022年4月1日の価格変更に後から対応するため
            'p.name AS product_name',
            'p.product_type_id',
            'transactions.memo AS transaction_memo',
            'transactions.tel AS transaction_tel',
            DB::raw('(CASE WHEN transactions.transaction_only_shipping_date IS NULL THEN c.shipping_date ELSE transactions.transaction_only_shipping_date END) AS shipping_date'),
            DB::raw('(CASE WHEN transactions.transaction_only_shipping_number IS NULL THEN c.shipping_number ELSE transactions.transaction_only_shipping_number END) AS shipping_number'),
            DB::raw('(CASE WHEN transactions.transaction_only_shipping_id IS NULL THEN c.shipping_id ELSE transactions.transaction_only_shipping_id END) AS shipping_id')
        )
            ->where('transactions.status', 1)
            ->where('transactions.id', $id)
            ->leftJoin('product_transactions AS pt', 'pt.transaction_id', '=', 'transactions.id')
            ->leftJoin('products AS p', 'p.id', '=', 'pt.product_id')
            ->leftJoin('old_products AS op', 'op.id', '=', 'pt.product_id')
            ->leftJoin('product_types AS ptype', 'ptype.id', '=', 'p.product_type_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id');

        $transactions = $query->get()->toArray();

        // 切り売り or 反物売り or 手動入力で分岐価格計算
        foreach ($transactions as $key => $val) {
            if (!$val['product_id']) {
                $transactions[$key]['price'] = $val['other_product_price'] * $val['num'];
            } elseif ($val['cut'] == '1') {
                $price = !is_null($val['pt_unit_price']) ? $val['pt_unit_price'] : $val['cut_fc_price'];
                $transactions[$key]['price'] = $price * $val['num'];
            } else {
                $price = !is_null($val['pt_unit_price']) ? $val['pt_unit_price'] : $val['fc_price'];
                $transactions[$key]['price'] = $price * $val['num'];
            }
        }

        return $transactions;
    }

    //  input = array output = intger
    public function calcSubTotal($products = [])
    {
        $total = 0;
        foreach ($products as $p) {
            // 紐づく枚数を調べる（カット人工芝の枚数）
            $set_count = 1;
            $set_array = [];
            foreach ($products as $rc) {
                if (!empty($rc['set_num'])) {
                    if ($p['row_id'] === $rc['parent_id']) {
                        $set_count = empty($rc['set_num']) ? 1 : $rc['set_num'];
                        break;
                        // カット人工芝に付随するカットメニュー
                    } elseif (!empty($p['parent_id'])) {
                        // カットメニューのセット数はparent_idが揃うところを取る
                        if ($p['parent_id'] === $rc['parent_id']) {
                            $set_count = empty($rc['set_num']) ? 1 : $rc['set_num'];
                            break;
                        }
                    }
                } else {
                    $set_count = 1;
                }
            }
            \Log::debug(print_r('set_count', true));
            \Log::debug(print_r($set_count, true));
            // 人工芝反物 or 副資材まとめ売り or カットメニュー
            if (!empty($p['num'])) {
                $total += intval($p['num'] * $p['unit_price'] * $set_count);
                \Log::debug(print_r($total, true));
                // カット人工芝
            } elseif (!empty($p['area'])) {
                $total += intval($p['area'] * $p['unit_price'] * $set_count);
                \Log::debug(print_r($total, true));
            }
            // カットメニュー
            \Log::debug(print_r($p, true));
        }
        // dd($total);
        return $total;
    }

    // キャンセル可能日時を計算
    public function cancelAble($create_time = null)
    {
        $now = Carbon::now();
        $limitTime = Carbon::parse($create_time)->addDay()->format('Y-m-d');
        $limitTime = Carbon::parse($limitTime . ' 10:59:59');
        $cancelAble = $now->lte($limitTime);

        return $cancelAble;
    }

    public function filterProductTypeId($array = [], $product_type_id = 1, $is_cut = 0)
    {
        $filtered_items = [];
        foreach ($array as $ra) {
            if (is_null($product_type_id) && is_null($ra['product_type_id'])) {
                array_push($filtered_items, $ra);
            } else {
                if ($ra['product_type_id'] == $product_type_id && $ra['cut'] == $is_cut) {
                    array_push($filtered_items, $ra);
                }
            }
        }
        return $filtered_items;
    }

    public function dispatch($posts = [])
    {
        // sub発注カウント
        $transaction_count =  transactionCount($posts['contact_id']);
        // 分納対応
        $shipping_id_column = '';
        $shipping_number_column = '';
        $shipping_date_column = '';
        $dispatch_message = '';
        $posts['number'] = empty($posts['number']) ? 1 : $posts['number'];
        switch ($posts['number']) {
            case '1':
                $shipping_id_column = 'transaction_only_shipping_id';
                $shipping_number_column = 'transaction_only_shipping_number';
                $shipping_date_column = 'transaction_only_shipping_date';
                $dispatch_message = 'dispatch_message';
                break;
            case '2':
                $shipping_id_column = 'shipping_id2';
                $shipping_number_column = 'shipping_number2';
                $shipping_date_column = 'shipping_date2';
                $dispatch_message = 'dispatch_message2';
                break;
            case '3':
                $shipping_id_column = 'shipping_id3';
                $shipping_number_column = 'shipping_number3';
                $shipping_date_column = 'shipping_date3';
                $dispatch_message = 'dispatch_message3';
                break;
            default:
                break;
        }

        if (empty($posts['transaction_id'])) {
            // 本部見積もりはここ
            Contact::where('id', $posts['contact_id'])
                ->update([
                    'shipping_id' => $posts['shipping_id'],
                    'shipping_number' => $posts['shipping_number'],
                    'shipping_date' => $posts['shipping_date'],
                    'step_id' => self::STEP_REPORT_COMPLETE,
                ]);
        } elseif (!empty($posts['contact_id']) && $posts['direct_shipping'] == '1') {
            // FC見積もり顧客へ直接発送
            Contact::where('id', $posts['contact_id'])
                ->update(['step_id' => self::STEP_REPORT_COMPLETE,]);

            $this->where('id', $posts['transaction_id'])->update([
                'shipping_cost' => $posts['shipping_cost'],
                $dispatch_message => $posts['dispatch_message'],
                $shipping_id_column => $posts['shipping_id'],
                $shipping_number_column => $posts['shipping_number'],
                $shipping_date_column => $posts['shipping_date'],
            ]);
        } elseif (!empty($posts['contact_id'])) {
            // FC見積もりFCへ発送（サブ発注）
            Contact::where('id', $posts['contact_id'])
                ->update([
                    'step_id' => self::STEP_COMPLETE,
                ]);
            $this->where('id', $posts['transaction_id'])
                ->update([
                    $shipping_id_column => $posts['shipping_id'],
                    $shipping_number_column => $posts['shipping_number'],
                    $shipping_date_column => $posts['shipping_date'],
                    $dispatch_message => $posts['dispatch_message'],
                    'shipping_cost' => $posts['shipping_cost'],
                ]);
        } else {
            // 案件に紐付けない発注
            $this->where('id', $posts['transaction_id'])->update([
                $shipping_id_column => $posts['shipping_id'],
                $shipping_number_column => $posts['shipping_number'],
                $shipping_date_column => $posts['shipping_date'],
                $dispatch_message => $posts['dispatch_message'],
                'shipping_cost' => $posts['shipping_cost'],
            ]);
        }
    }

    public function dispatchSendEmail($posts = [])
    {
        // 本部が発送した場合の処理
        if (empty($posts['transaction_id'])) {
            $customer = Contact::select('contacts.*', 's.transport_company', 's.trakking_url')
                ->where('contacts.id', $posts['contact_id'])
                ->join('shippings AS s', 'contacts.shipping_id', '=', 's.id')
                ->first();
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
            $customer['dispatch_message'] = $posts['dispatch_message'];
            if (config('app.env') != 'circleci') {
                if (!empty($customer['email'])) {
                    Mail::to($customer['email'])->send(new SendTransactionDispatchCustomerMail($customer));
                    // メールを本部にも送信
                }
                $email = 'bcc@shintou-s.jp';
                Mail::to($email)->send(new SendTransactionDispatchCustomerMail($customer));
                $email = config('mail.fallback_notification', 'notifications@example.com');
                Mail::to($email)->send(new SendTransactionDispatchCustomerMail($customer));
            }

            Contact::where('id', $customer['id'])->update(['step_id' => self::STEP_REPORT_COMPLETE]);

            return redirect(route('dashboard'))->with('success', '顧客に商品の発注連絡を行いました');
        }
        // 本部が発送した場合の処理ここまで

        // FCに発送する場合
        $transaction = $this->shippingMail($posts);
        // numberは分納の際、1〜3の分納
        $transaction['number'] = $posts['number'];
        // メールを本部にも送信
        if (\App::environment('production') || \App::environment('localhost') || \App::environment('local')) {
            $email = 'bcc@shintou-s.jp';
            Mail::to($email)->send(new SendTransactionDispatchMail($transaction));
            $email = config('mail.fallback_notification', 'notifications@example.com');
            Mail::to($email)->send(new SendTransactionDispatchMail($transaction));
        }
        $fc = User::where('id', $posts['userid'])->first();
        if (isAllowEmailUser($posts['userid'])) {
            Mail::to($fc['email'])->send(new SendTransactionDispatchMail($transaction));
            if (!empty($fc['email2'])) {
                Mail::to($fc['email2'])->send(new SendTransactionDispatchMail($transaction));
            }
            if (!empty($fc['email3'])) {
                Mail::to($fc['email3'])->send(new SendTransactionDispatchMail($transaction));
            }
        }
    }

    // 発送メールに整形するための関数
    public function shippingMail($posts = [])
    {
        // dd($posts);
        $transaction_query = $this->query()->select(
            'transactions.*',
            'transactions.id AS transaction_id',
            'transactions.created_at AS transaction_created_at',
            'c.*',
            'c.id AS contact_id',
            's.trakking_url',
            's.transport_company',
            'u.id AS fc_id',
            'u.name AS fc_name',
        )
            ->where('transactions.id', $posts['transaction_id'])
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id');
        // 分納対応
        $posts['number'] = empty($posts['number']) ? 1 : $posts['number'];
        switch ($posts['number']) {
            case '1':
                $transaction_query->leftJoin('shippings AS s', 's.id', '=', 'transactions.transaction_only_shipping_id');
                break;
            case '2':
                $transaction_query->leftJoin('shippings AS s', 's.id', '=', 'transactions.shipping_id2');
                break;
            case '3':
                $transaction_query->leftJoin('shippings AS s', 's.id', '=', 'transactions.shipping_id3');
                break;
            default:
                $transaction_query->leftJoin('shippings AS s', 's.id', '=', 'transactions.transaction_only_shipping_id');
                break;
        }
        $transaction = $transaction_query->first();
        // 分納対応
        switch ($posts['number']) {
            case '1':
                $shipping_id_column = 'transaction_only_shipping_id';
                $shipping_number_column = 'transaction_only_shipping_number';
                $shipping_date_column = 'transaction_only_shipping_date';
                $transaction['dispatch_message'] = $posts['dispatch_message'];
                break;
            case '2':
                $shipping_id_column = 'shipping_id2';
                $shipping_number_column = 'shipping_number2';
                $shipping_date_column = 'shipping_date2';
                $transaction['shipping_id2'] = $posts['shipping_id'];
                $transaction['shipping_number2'] = $posts['shipping_number'];
                $transaction['shipping_date2'] = $posts['shipping_date'];
                $transaction['dispatch_message2'] = $posts['dispatch_message'];
                break;
            case '3':
                $shipping_id_column = 'shipping_id3';
                $shipping_number_column = 'shipping_number3';
                $shipping_date_column = 'shipping_date3';
                $transaction['shipping_id3'] = $posts['shipping_id'];
                $transaction['shipping_number3'] = $posts['shipping_number'];
                $transaction['shipping_date3'] = $posts['shipping_date'];
                $transaction['dispatch_message3'] = $posts['dispatch_message'];
                break;
            default:
                $shipping_id_column = 'transaction_only_shipping_id';
                $shipping_number_column = 'transaction_only_shipping_number';
                $shipping_date_column = 'transaction_only_shipping_date';
                $transaction['dispatch_message'] = $posts['dispatch_message'];
                break;
        }
        if ($transaction[$shipping_id_column] == 1) {
            $numbers = explode(',', $transaction[$shipping_number_column]);
            $params = null;
            foreach ($numbers as $key => $value) {
                $num = $key + 1;
                $joint = ($key === 0) ? '?' : '&';
                $params .= $joint . 'GNPNO' . $num . '=' . $value;
            }
            $transaction['url'] = $transaction['trakking_url'] . $params;
        } else {
            $transaction['url'] = $transaction['trakking_url'] . $transaction[$shipping_number_column];
        }

        return $transaction;
    }

    public static function refreshFreeeToken($refresh_token)
    {
        $client_id = config('services.freeeaccounting.client_id');
        $client_secret = config('services.freeeaccounting.client_secret');
        $redirect = redirect()->action('TransactionController@createInvoice'); //redirect
        $refresh_token = User::find(1)->freee_refresh_token ? User::find(1)->freee_refresh_token : '';

        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://accounts.secure.freee.co.jp/public_api/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&client_id=$client_id&client_secret=$client_secret&refresh_token=$refresh_token&redirect_uri=urn:ietf:wg:oauth:2.0:oob");

        $headers = [];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        $result = json_decode($result, true);
        if (!empty($result['error']) || empty($result)) {
            \Log::debug('=============FreeeApiError===========');
            \Log::debug(print_r($result, true));

            return false;
        }

        curl_close($ch);

        $access_token = $result['access_token'];
        $refresh_token = $result['refresh_token'];

        $save_refresh_token = User::find(1);
        $save_refresh_token->freee_refresh_token = $refresh_token;
        $save_refresh_token->freee_access_token = $access_token;
        $save_refresh_token->save();
    }

    public function createTextRow($order, $transaction)
    {
        $contents = [];
        $contents['order'] = $order;
        $contents['type'] = 'text';
        $contents['description'] = '発注書No.' . $transaction['id'];

        return $contents;
    }

    public function createInvoiceRow($order, $i, $products, $contents, $count_products, $access_token, $company_id, $cut = null, $prepaid = 0)
    {
        if (is_null($cut)) {
            $cut = ['product_id' => null];
        }
        $contents = [];
        $product_id = is_null($cut['product_id']) ? $products[$i]['product_id'] : $cut['product_id'];
        $item = Product::where('id', $product_id)->get()->toArray();
        if (!empty($item[0])) {
            if (is_null($item[0]['freee_item_id']) || !$this->isExistFreeeItem($item[0]['freee_item_id'], $access_token, $company_id)) {
                // 何らかの理由でproducts.freee_item_id = null なら新しく品目を作ってUPDATE
                // \Log::debug(print_r("============TransactionModel 325 ==========", true));
                // \Log::debug(print_r($item[0], true));
                // \Log::debug(print_r("============TransactionModel 325 ==========", true));
                $freee_item_id = $this->createFreeeProduct($access_token, $company_id, $item[0]['name']);
                // Product::where('id', $item[0]['id'])->update(['freee_item_id' => $freee_item_id]);
            } else {
                // productsテーブルにはあるが、freee上にない場合は、freee上に品目を作成して、products.freee_item_idにUpdate
                $freee_item = $this->isExistFreeeItem($item[0]['freee_item_id'], $access_token, $company_id);
            }
        }
        //その他入力は品目「その他」にまとめて表示だけFCが入力したものにする
        // 品目「その他」は envから取得
        if (empty($item)) {
            //Productテーブルを検索して重複しているものがなければAPIを叩くが、既に存在していたらfreee_item_idを取得
            $check_name_exist = Product::where('name', $products[$i]['other_product_name'])->exists();
            if (!$check_name_exist) {
                // $freee_item_id = $this->createFreeeProduct($access_token, $company_id, $products[$i]['other_product_name']);
                $freee_item_id = intval(config('services.freeeaccounting.other_item_id'));
            } else {
                $get_other_product_freee_id = Product::where('name', $products[$i]['other_product_name'])->get('freee_item_id')->toArray();
                $freee_item_id = $get_other_product_freee_id[0]['freee_item_id'];
            }
            //独自注文品の行を追加するための$contentsを作る
            $vat_of_content = floor(($products[$i]['other_product_price'] * $products[$i]['num']) * config('app.tax_rate'));
            $contents['tax_code'] = intval(config('services.freeeaccounting.tax_code'));
            $contents['tax_rate'] = config('app.tax_rate') * 100;
            $contents['order'] = $order;
            $contents['type'] = 'item';
            $contents['item_id'] = $freee_item_id;
            $contents['account_item_id'] = intval(config('services.freeeaccounting.account_item_id'));
            $contents['description'] = $products[$i]['num'] < 1.0 ? $products[$i]['other_product_name'] . '【要数量変更！】→ 元の数量 ＝ ' . strval($products[$i]['num']) : $products[$i]['other_product_name'];
            $contents['quantity'] = $products[$i]['num'] < 1.0 ? 1 : $products[$i]['num'];
            $contents['unit'] = $products[$i]['unit'];
            $contents['number'] = $products[$i]['num'];
            $contents['unit_price'] = strval($products[$i]['other_product_price']);
            $contents['amount'] = $products[$i]['other_product_price'];
            $contents['vat'] = $vat_of_content;
        } else {
            // カット人工芝は枚数分ループ
            $item_name = $item[0]['name'];
            $item_id = $item[0]['freee_item_id'];

            /* 耳なしカットの単価がおかしくなる問題 */
            $unit_price = 0;
            if ($products[$i]['cut'] && !is_null($cut['product_id'])) {
                $unit_price = !empty($cut['unit_price']) ? $cut['unit_price'] : 1000;
                $unit = 'm';
            } else {
                $unit_price = !is_null($products[$i]['unit_price']) ? $products[$i]['unit_price'] : $item[0]['fc_price'];
                // $unit = !is_null($item[0]['unit']) ? $item[0]['unit'] : $item[0]['cut_unit'];
                $unit = $products[$i]['unit'];
            }
            // カット人工芝なら注文時の縦横サイズを表示
            if ($products[$i]['cut'] && $products[$i]['product_type_id'] === 1 && is_null($cut)) {
                $item_name = $item_name . ' （' . $products[$i]['vertical'] . 'm × ' . $products[$i]['horizontal'] . 'm）';
            }

            $number = is_null($cut['product_id']) ? $products[$i]['num'] : $cut['num'];
            $totalPrice = ($number * $unit_price);
            $vat_of_content = floor($totalPrice * config('app.tax_rate'));
            $contents['tax_code'] = intval(config('services.freeeaccounting.tax_code'));
            $contents['tax_rate'] = config('app.tax_rate') * 100;
            $contents['order'] = $order;
            $contents['type'] = 'item';
            $contents['item_id'] = $item_id;
            $contents['description'] = $number < 1.0 ? $item_name . '【要数量変更！】→ 元の数量 ＝ ' . strval($products[$i]['num']) : $item_name;
            $contents['account_item_id'] = intval(config('services.freeeaccounting.account_item_id'));
            $contents['quantity'] = $number < 1.0 ? 1 : $number;
            $contents['unit'] = $unit;
            $contents['number'] = $number;
            $contents['unit_price'] = strval($unit_price);
            $contents['amount'] = $totalPrice;
            $contents['vat'] = $vat_of_content;
        }

        return $contents;
    }

    private function isExistFreeeItem($freee_item_id, $access_token, $company_id)
    {
        $client_id = config('services.freeeaccounting.client_id');
        $client_secret = config('services.freeeaccounting.client_secret');
        $refresh_token = User::find(1)->freee_refresh_token ? User::find(1)->freee_refresh_token : '';
        $ch = curl_init();
        $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
        curl_setopt($ch, CURLOPT_URL, "https://api.freee.co.jp/api/1/items/$freee_item_id?company_id=$company_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $item_result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $item_result = json_decode($item_result);
        $status = true;
        if (!empty($item_result->status_code)) {
            if ($item_result->status_code == '404') {
                $status = false;
            }
        }
        return $status;
    }

    public function createInvoiceDiscountRow($order, $t, $transactionId, $discount = 1000, $type = 'special')
    {
        $contents['tax_code'] = intval(config('services.freeeaccounting.tax_code'));
        $contents['tax_rate'] = 10;
        $contents['order'] = $order;
        $contents['type'] = 'item';
        $contents['account_item_id'] = intval(config('services.freeeaccounting.account_item_id')); //type = textのときのみ勘定科目不要
        $contents['item_id'] = $type === 'special' ? intval(config('services.freeeaccounting.special_discount_item_id')) : intval(config('services.freeeaccounting.discount_item_id'));
        $contents['description'] = $type === 'special' ? '特別割引' : '大口割引';
        $contents['number'] = 1;
        $contents['unit_price'] = "-$discount";
        $contents['amount'] = $discount;
        $contents['quantity'] = 1;
        $contents['unit'] = '式';

        return $contents;
    }

    public function createInvoiceShippingRow($order, $transactionId)
    {
        //送料を追加
        $shipping = Transaction::find($transactionId);

        $shippingCost = is_null($shipping['shipping_cost']) ? 0 : $shipping['shipping_cost'];

        $date = $shipping['transaction_only_shipping_date'];
        if (is_null($date)) {
            $result = Contact::where('t.id', $shipping['id'])->leftJoin('transactions AS t', 't.contact_id', '=', 'contacts.id')->first();
            $date = $result['shipping_date'];
        }
        $date = date('n/j', strtotime($date));
        $contents['tax_code'] = intval(config('services.freeeaccounting.tax_code'));
        $contents['tax_rate'] = config('app.tax_rate') * 100;
        $contents['order'] = $order;
        $contents['type'] = 'item';
        $contents['account_item_id'] = intval(config('services.freeeaccounting.account_item_id'));
        $contents['item_id'] = intval(config('services.freeeaccounting.shipping_item_id'));
        $contents['description'] = '送料';
        $contents['quantity'] = 1;
        $contents['unit'] = '式';
        $contents['number'] = 1;
        $contents['unit_price'] = strval($shippingCost);
        $contents['amount'] = $shippingCost;

        return $contents;
    }

    public function createInvoiceHalfPaymentRow($order, $transaction)
    {
        $totalPrice = $transaction['total'] + $transaction['shipping_cost'] - $transaction['discount'];
        $contents['tax_code'] = intval(config('services.freeeaccounting.advance_payment_tax_code')); // 税区分→対象外
        // $contents['reduced_tax_rate'] = false;
        $contents['tax_code'] = intval(config('services.freeeaccounting.tax_code'));
        $contents['tax_rate'] = 10;
        $contents['order'] = $order;
        $contents['type'] = 'item';
        $contents['account_item_id'] = intval(config('services.freeeaccounting.advance_payment_amount_id')); //勘定科目
        $contents['item_id'] = intval(config('services.freeeaccounting.discount_item_id'));
        $contents['description'] = '発注書No.' . $transaction['id'] . '前金分';
        $contents['number'] = 1;
        $contents['unit_price'] = strval(- ($totalPrice + ($totalPrice * config('app.tax_rate'))) / 2);
        $contents['amount'] = ($totalPrice + ($totalPrice * config('app.tax_rate'))) / 2;
        $contents['quantity'] = 1;
        $contents['unit'] = '式';

        return $contents;
    }

    public function createFreeeProduct($access_token, $admin_company_id, $product_name)
    {
        \Log::debug(print_r($product_name, true));
        $ch = curl_init();
        $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
        $freee_item = [
            'company_id' => $admin_company_id,
            'name' => $product_name,
        ];
        curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/api/1/items');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($freee_item));

        $item_result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $array_freee_item = json_decode($item_result, true);
        // いちいちプロパティ調べないといけないくそ仕様
        if (!empty($array_freee_item['status_code'])) {
            if ($array_freee_item['status_code'] == 400) {
                // すでに同じ名前の商品があればAPIで品目IDを探す
                if (!empty($array_freee_item['errors'][1])) {
                    if ($array_freee_item['errors'][1]['messages'][0] == 'すでに同じ名前の項目が存在しています。' || preg_match("/存在/", $array_freee_item['errors'][1]['messages'][0]) == 1) {
                        $ch = curl_init();
                        $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
                        curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/api/1/items?company_id=' . $admin_company_id . '&limit=3000');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        $freee_items = curl_exec($ch);
                        $freee_items = json_decode($freee_items);
                        foreach ($freee_items->items as $item) {
                            if (trim(mb_convert_kana($item->name, "rnsk")) == trim(mb_convert_kana($product_name, "rnsk"))) {
                                $freee_item_id = $item->id;
                            }
                        }
                        curl_close($ch);
                    }
                } else {
                    $freee_item_id = $array_freee_item['item']['id'];
                }
            } else {
                $freee_item_id = $array_freee_item['item']['id'];
            }
        } else {
            //品目IDを取得
            $freee_item_id = $array_freee_item['item']['id'];
        }
        //取得した品目IDをname,priceと一緒にproductsテーブルに登録
        // $add_item = new Product();
        // $add_item->freee_item_id = $freee_item_id;
        // $add_item->name = $product_name;
        // $add_item->product_type_id = 5; //独自注文
        // $add_item->save();

        if (empty($freee_item_id)) {
            // dd($product_name, $item_result);
        }
        return $freee_item_id;
    }

    public function getInvoices($admin_company_id, $access_token, $from, $to)
    {
        $ch = curl_init();
        $header = ['Accept: application/json', 'Authorization: Bearer ' . $access_token, 'Content-Type: application/json', 'X-Api-Version: 2020-06-15'];
        curl_setopt($ch, CURLOPT_URL, 'https://api.freee.co.jp/invoices?company_id=' . $admin_company_id . '&start_issue_date=' . $from . '&end_issue_date=' . $to . '&limit=100');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $invoices = curl_exec($ch);
        $invoices = json_decode($invoices);
        curl_close($ch);

        return $invoices;
    }

    public function transactionNos($transactions)
    {
        $transactionNos = '';
        $transactions = json_decode(json_encode($transactions), true);
        $ids = [];
        foreach ($transactions as $t) {
            $ids[] = $t['id'];
        }
        $ids = array_column($transactions, 'id');
        array_multisort($ids, SORT_ASC, $transactions);
        foreach ($transactions as $value) {
            $transactionNos .= $value['id'] . ', ';
            if ($value === end($transactions)) {
                // 最後
            }
        }

        return substr($transactionNos, 0, -2);
    }

    public function adminCount()
    {
        $contacts = Contact::select('contacts.*', 'contacts.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 't.id AS transaction_id', 't.address', 't.consignee', 't.created_at AS transaction_created_at')
            ->where('contacts.status', 1)
            ->where('step_id', self::STEP_SHIPPING)
            ->where('t.status', 1)
            ->leftJoin('users AS u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('transactions AS t', 't.contact_id', '=', 'contacts.id')
            ->get()->toArray();

        $transactions = Transaction::select('c.*', 'c.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 'transactions.id AS transaction_id', 'transactions.address', 'transactions.consignee', 'transactions.created_at AS transaction_created_at')
            ->where('transactions.status', 1)
            ->where('transactions.contact_id', null)
            ->where('transactions.transaction_only_shipping_date', null)
            ->where('step_id', self::STEP_SHIPPING)
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->get()->toArray();

        // 本部が見積もりして、商談成立した顧客
        $customers = Contact::where('contacts.status', 1)->where('step_id', self::STEP_SHIPPING)->where('contacts.user_id', 1)
            ->join('quotations AS q', 'contacts.quotation_id', '=', 'q.id')
            ->get();

        $contacts = array_merge($contacts, $transactions);

        return count($contacts);
    }

    public function createUsageFeeRow($fc, $order)
    {
        if ($fc->invoice_payments_type == 0) {
            return null;
        }

        return [
            'tax_code' => intval(config('services.freeeaccounting.tax_code')),
            'tax_rate' => config('app.tax_rate') * 100,
            'order' => $order,
            'type' => 'item',
            'account_item_id' => intval(config('services.freeeaccounting.listing_fee_account_item_id')),
            // HP掲載料とブランド使用料を分岐
            'item_id' => $fc->invoice_payments_type == 1 ? intval(config('services.freeeaccounting.listing_fee_item_id')) : intval(config('services.freeeaccounting.brand_fee_item_id')),
            'description' => returnInvoicePaymentsType($fc->invoice_payments_type),
            'quantity' => 1,
            'unit' => '式',
            'number' => 1,
            'unit_price' => "30000",
            'amount' => 1,
        ];
    }

    public function isCancelable($create_at)
    {
        //キャンセル期限の判別
        $today = Carbon::now();
        $createDate = Carbon::parse($create_at);
        $isFuture = $today->gte($createDate);
        $dateDiff = $today->diffInDays($createDate);
        $time = $today->hour;

        $is_cancelable = $isFuture && $dateDiff >= 1 && $time > 10 ? true : false;
        return $is_cancelable;
    }

    //X営業日後の日付を取得(テストで使用)
    public function getDeliveryDate($days)
    {
        //祝日・土日・会社休日を考慮したx営業日後
        $holiday = $this->japanHoliday();
        $office_holiday = $this->officeHoliday();
        for ($i = 1; $i <= $days; $i++) {
            $target_day = Carbon::now()->timezone('Asia/Tokyo')->addDay($i);
            $target_day_of_week = $target_day->dayOfWeek;
            if ($target_day_of_week == 0 || $target_day_of_week == 6) {
                $days += 1;
            }
            for ($j = 0; $j < count($holiday); $j++) {
                if ((array_keys($holiday)[$j] == $target_day->format('Y-m-d')) && ($target_day_of_week != 0 && $target_day_of_week != 6)) {
                    $days += 1;
                }
            }
            for ($k = 0; $k < count($office_holiday); $k++) {
                if ($office_holiday[$k] == $target_day->format('Y-m-d')) {
                    if ($target_day_of_week != 0 && $target_day_of_week != 6) {
                        $days += 1;
                    }
                }
            }
        }

        $delivery_date = Carbon::now()->timezone('Asia/Tokyo')->addDay($days);

        return $delivery_date;
    }

    //祝日取得
    private function japanHoliday()
    {
        $url = "https://holidays-jp.github.io/api/v1/date.json";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response =  json_decode(curl_exec($ch));
        curl_close($ch);

        $date = Carbon::now();
        $year = $date->year;
        $holiday = (array)$response;
        for ($i = count(array_keys($holiday)) - 1; $i >= 0; $i--) {
            if (!preg_match('{' . $year . '}', array_keys($holiday)[$i])) {
                array_splice($holiday, $i, 1);
            }
        }

        return $holiday;
    }
    //会社休日取得
    private function officeHoliday()
    {
        $data = OfficeHoliday::whereYear('holiday', '>=', Carbon::now()->format('Y'))->orderBy('holiday', 'ASC')->get();
        $office_holiday = [];
        for ($i = 0; $i < count($data); $i++) {
            $fmt_data = new Carbon($data[$i]['holiday']);
            array_push($office_holiday, $fmt_data->format('Y-m-d'));
        }
        return $office_holiday;
    }
}
