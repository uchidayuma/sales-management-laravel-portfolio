<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\ContactType;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Contact extends MyModel
{
    protected $except = [
        'api/contact/*',
    ];
    protected $casts = [
        'etc_memo' => 'json',
    ];

    protected $fillable = [
        'id',
        'contact_type_id',
        'user_id',
        'step_id',
        'cancel_step',
        'shipping_id',
        'free_sample',
        'email',
        'fax',
        'tel',
        'zipcode',
        'pref',
        'city',
        'street',
        'ground_condition',
        'desired_datetime1',
        'desired_datetime2',
        'finished_datetime',
        'visit_address',
        'square_meter',
        'use_application',
        'surname',
        'name',
        'surname_ruby',
        'name_ruby',
        'company_name',
        'company_ruby',
        'industry',
        'quote_details',
        'vertical_size',
        'horizontal_size',
        'desired_product',
        'comment',
        'age',
        'requirement',
        'where_find',
        'sns',
        'before_image1',
        'before_image2',
        'before_image3',
        'after_image1',
        'after_image2',
        'after_image3',
        'status',
        'completed_at',
        'document1',
        'document2',
        'document3',
        'document4',
        'document5',
        'document1_original_name',
        'document2_original_name',
        'document3_original_name',
        'document4_original_name',
        'document5_original_name',
        'tel2',
        'public',
        'memo',
        'etc_memo',
        'sample_send_at',
        'free_sample_required',
    ];

    public static $address_keywords = [
        '1', '2', '3', '4', '5', '6', '7', '8', '9',
        '１', '２', '３', '４', '５', '６', '７', '８', '９',
        '一', '二', '三', '四', '五', '六', '七', '八', '九',
    ];

    protected $table = 'contacts';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function stringFormatting($array)
    {
        $tempString = '';
        foreach ($array as $value) {
            $tempString .= $value . ',';
        }
        // 最後に,が入ってしまうので、最後の1文字を削除
        return substr($tempString, 0, -1);
    }

    public function contactType()
    {
        return $this->belongsTo('App\Models\ContactType', 'contact_type_id');
    }

    // 余計なキーを削除するプライベートファンクション
    private function forgetWp($post)
    {
        $contactType = new ContactType();
        $post['contact_type_id'] = $contactType->getField(['wpid' => $post['_wpcf7']], 'id');
        Arr::forget($post, '_wpcf7');
        Arr::forget($post, '_wpcf7_version');
        Arr::forget($post, '_wpcf7_locale');
        Arr::forget($post, '_wpcf7_unit_tag');
        Arr::forget($post, '_wpcf7_container_post');

        return $post;
    }

    // 個人サンプル請求
    public function personalSample($post)
    {
        $post = self::forgetWp($post);

        $post['surname'] = $post['kojin_name_sample_sei'];
        Arr::forget($post, 'kojin_name_sample_sei');

        $post['name'] = $post['kojin_name_sample_mei'];
        Arr::forget($post, 'kojin_name_sample_mei');

        $post['surname_ruby'] = $post['kojin_katakana_sample_sei'];
        Arr::forget($post, 'kojin_katakana_sample_sei');

        $post['name_ruby'] = $post['kojin_katakana_sample_mei'];
        Arr::forget($post, 'kojin_katakana_sample_mei');

        $post['zipcode'] = $post['zip_kojin_sample'];
        Arr::forget($post, 'zip_kojin_sample');

        $post['pref'] = $post['pref_kojin_sample'];
        Arr::forget($post, 'pref_kojin_sample');

        $post['city'] = $post['addr1_kojin_sample'];
        Arr::forget($post, 'addr1_kojin_sample');

        $post['street'] = $post['addr2_kojin_sample'];
        Arr::forget($post, 'addr2_kojin_sample');

        $post['age'] = $post['kojin_year_sample'];
        Arr::forget($post, 'kojin_year_sample');

        $post['tel'] = $post['kojin_tel_sample'];
        Arr::forget($post, 'kojin_tel_sample');

        $post['email'] = $post['kojin_email_sample'];
        Arr::forget($post, 'kojin_email_sample');

        $post['use_application'] = implode(',', $post['kojin_usage_sample']) . ',' . $post['kojin_usage_other_sample'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'kojin_usage_sample');
        Arr::forget($post, 'kojin_usage_other_sample');

        $post['where_find'] = implode(',', $post['kojin_dokode_sample']) . ',' . $post['kojin_dokode_other_sample'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'kojin_dokode_sample');
        Arr::forget($post, 'kojin_dokode_other_sample');

        $post['sns'] = implode(',', $post['kojin_sns_sample']) . ',' . $post['kojin_sns_other_sample'];
        $post['sns'] = ltrim($post['sns'], ',');
        Arr::forget($post, 'kojin_sns_sample');
        Arr::forget($post, 'kojin_sns_other_sample');

        return $this->insertGetId($post);
    }

    // 個人図面見積もり
    public function personalDraw($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['kojin_sample_zumen'][0];
        Arr::forget($post, 'kojin_sample_zumen');

        // 両方のみだった場合は文字列置換
        $post['quote_details'] = $post['kojin_quote_zumen'][0] === '両方' ? '施工希望、材料のみ' : $post['kojin_quote_zumen'][0];
        Arr::forget($post, 'kojin_quote_zumen');

        $post['ground_condition'] = $post['kojin_ground_status_zumen'][0] . ',' . $post['kojin_ground_status_other_zumen'];
        Arr::forget($post, 'kojin_ground_status_zumen');
        Arr::forget($post, 'kojin_ground_status_other_zumen');

        $post['vertical_size'] = $post['kojin_size_tate_zumen'];
        Arr::forget($post, 'kojin_size_tate_zumen');

        $post['horizontal_size'] = $post['kojin_size_yoko_zumen'];
        Arr::forget($post, 'kojin_size_yoko_zumen');

        $post['desired_product'] = implode(',', $post['kojin_want_product_zumen']);
        Arr::forget($post, 'kojin_want_product_zumen');

        $post['comment'] = $post['kojin_message_zumen'];
        Arr::forget($post, 'kojin_message_zumen');

        $post['surname'] = $post['kojin_name_zumen_sei'];
        Arr::forget($post, 'kojin_name_zumen_sei');

        $post['name'] = $post['kojin_name_zumen_mei'];
        Arr::forget($post, 'kojin_name_zumen_mei');

        $post['surname_ruby'] = $post['kojin_katakana_zumen_sei'];
        Arr::forget($post, 'kojin_katakana_zumen_sei');

        $post['name_ruby'] = $post['kojin_katakana_zumen_mei'];
        Arr::forget($post, 'kojin_katakana_zumen_mei');

        $post['zipcode'] = $post['zip_kojin_zumen'];
        Arr::forget($post, 'zip_kojin_zumen');

        $post['pref'] = $post['pref_kojin_zumen'];
        Arr::forget($post, 'pref_kojin_zumen');

        $post['city'] = $post['addr1_kojin_zumen'];
        Arr::forget($post, 'addr1_kojin_zumen');

        $post['street'] = $post['addr2_kojin_zumen'];
        Arr::forget($post, 'addr2_kojin_zumen');

        $post['age'] = $post['kojin_year_zumen'];
        Arr::forget($post, 'kojin_year_zumen');

        $post['tel'] = $post['kojin_tel_zumen'];
        Arr::forget($post, 'kojin_tel_zumen');

        $post['email'] = $post['kojin_email_zumen'];
        Arr::forget($post, 'kojin_email_zumen');

        $post['use_application'] = implode(',', $post['kojin_usage_zumen']) . ',' . $post['kojin_usage_other_zumen'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'kojin_usage_zumen');
        Arr::forget($post, 'kojin_usage_other_zumen');

        $post['where_find'] = implode(',', $post['kojin_dokode_zumen']) . ',' . $post['kojin_dokode_other_zumen'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'kojin_dokode_zumen');
        Arr::forget($post, 'kojin_dokode_other_zumen');

        $post['sns'] = implode(',', $post['kojin_sns_zumen']) . ',' . $post['kojin_sns_other_zumen'];
        $post['sns'] = ltrim($post['sns'], ',');
        Arr::forget($post, 'kojin_sns_zumen');
        Arr::forget($post, 'kojin_sns_other_zumen');

        if ($post['kojin_file_zumen']) {
            // \Log::debug($post['kojin_file_zumen']);
            // $file = base64_decode($post['file']);
            // $ext = getExtention($post['kojin_file_zumen']);
            // $fileName = Str::random(12).'.'.$ext;
            // \Log::debug($fileName);
            // file_put_contents(storage_path().'/app/public/draw/'.$fileName, $file);
            // $post['document1'] = $fileName;
            $post['document1_original_name'] = '添付ファイルがあります。メールをご確認ください';
            // $file->store()
        }
        Arr::forget($post, 'kojin_file_zumen');

        return $this->insertGetId($post);
    }

    // 個人訪問見積もり
    public function personalVisit($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['kojin_sample_houmon'][0];
        Arr::forget($post, 'kojin_sample_houmon');

        $post['ground_condition'] = $post['kojin_ground_status_houmon'][0] . ',' . $post['kojin_ground_status_other_houmon'];
        Arr::forget($post, 'kojin_ground_status_houmon');
        Arr::forget($post, 'kojin_ground_status_other_houmon');

        $post['square_meter'] = $post['kojin_heibeisuu_houmon'];
        Arr::forget($post, 'kojin_heibeisuu_houmon');

        $post['comment'] = $post['kojin_message_houmon'];
        Arr::forget($post, 'kojin_message_houmon');

        $post['surname'] = $post['kojin_name_houmon_sei'];
        Arr::forget($post, 'kojin_name_houmon_sei');

        $post['name'] = $post['kojin_name_houmon_mei'];
        Arr::forget($post, 'kojin_name_houmon_mei');

        $post['surname_ruby'] = $post['kojin_katakana_houmon_sei'];
        Arr::forget($post, 'kojin_katakana_houmon_sei');

        $post['name_ruby'] = $post['kojin_katakana_houmon_mei'];
        Arr::forget($post, 'kojin_katakana_houmon_mei');

        $post['zipcode'] = $post['zip_kojin_houmon'];
        Arr::forget($post, 'zip_kojin_houmon');

        $post['pref'] = $post['pref_kojin_houmon'];
        Arr::forget($post, 'pref_kojin_houmon');

        $post['city'] = $post['addr1_kojin_houmon'];
        Arr::forget($post, 'addr1_kojin_houmon');

        $post['street'] = $post['addr2_kojin_houmon'];
        Arr::forget($post, 'addr2_kojin_houmon');

        //別住所に訪問する場合

        $post['visit_address'] = $post['kojin_todouhuken_houmon'] . $post['kojin_add01_houmon'] . $post['kojin_add02_houmon'];
        Arr::forget($post, 'kojin_todouhuken_houmon');
        Arr::forget($post, 'kojin_add01_houmon');
        Arr::forget($post, 'kojin_add02_houmon');

        $post['age'] = $post['kojin_year_houmon'];
        Arr::forget($post, 'kojin_year_houmon');

        $post['tel'] = $post['kojin_tel_houmon'];
        Arr::forget($post, 'kojin_tel_houmon');

        $post['email'] = $post['kojin_email_houmon'];
        Arr::forget($post, 'kojin_email_houmon');

        $post['use_application'] = implode(',', $post['kojin_usage_houmon']) . ',' . $post['kojin_usage_other_houmon'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'kojin_usage_houmon');
        Arr::forget($post, 'kojin_usage_other_houmon');

        $carbon = new Carbon();
        // HP側のフォームに年選択がないので、現在の月と比較して翌年の依頼を分岐
        if (intval($post['kojin_month1_houmon']) >= $carbon->month) {
            $year1 = $carbon->year;
        } else {
            $carbon->addYear();
            $year1 = $carbon->year;
        }
        // sprintfは0を埋めるためのフォーマット関数
        $month1 = sprintf('%02d', $post['kojin_month1_houmon']);
        $day1 = sprintf('%02d', $post['kojin_day1']);
        $time1 = sprintf('%02d:00:00', $post['kojin_time1_houmon']);
        //DateTime型に整形
        $post['desired_datetime1'] = $year1 . '-' . $month1 . '-' . $day1 . ' ' . $time1;

        Arr::forget($post, 'kojin_month1_houmon');
        Arr::forget($post, 'kojin_day1');
        Arr::forget($post, 'kojin_time1_houmon');

        // 第2希望は任意なので、入力があった場合Insert
        if ($post['kojin_month2_houmon']) {
            $carbon = new Carbon();
            if (intval($post['kojin_month2_houmon']) >= $carbon->month) {
                $year2 = $carbon->year;
            } else {
                $carbon->addYear();
                $year2 = $carbon->year;
            }
            $month2 = sprintf('%02d', $post['kojin_month2_houmon']);
            $day2 = sprintf('%02d', $post['kojin_day2']);
            $time2 = sprintf('%02d:00:00', $post['kojin_time2_houmon']);
            $post['desired_datetime2'] = $year2 . '-' . $month2 . '-' . $day2 . ' ' . $time2;
        }
        Arr::forget($post, 'kojin_month2_houmon');
        Arr::forget($post, 'kojin_day2');
        Arr::forget($post, 'kojin_time2_houmon');

        $post['where_find'] = implode(',', $post['kojin_dokode_houmon']) . ',' . $post['kojin_dokode_other_houmon'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'kojin_dokode_houmon');
        Arr::forget($post, 'kojin_dokode_other_houmon');

        $post['sns'] = implode(',', $post['kojin_sns_houmon']) . ',' . $post['kojin_sns_other_houmon'];
        $post['sns'] = ltrim($post['sns'], ',');
        Arr::forget($post, 'kojin_sns_houmon');
        Arr::forget($post, 'kojin_sns_other_houmon');

        return $this->insertGetId($post);
    }

    // 個人その他
    public function personalEtc($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['kojin_sample_other_contact'][0];
        Arr::forget($post, 'kojin_sample_other_contact');

        $post['quote_details'] = $post['kojin_youken_other_contact'][0];
        Arr::forget($post, 'kojin_quote_other');

        $post['use_application'] = implode(',', $post['kojin_youken_other_contact']);
        Arr::forget($post, 'kojin_youken_other_contact');

        $post['comment'] = $post['other_message_other'];
        Arr::forget($post, 'other_message_other');

        $post['surname'] = $post['kojin_name_other_sei'];
        Arr::forget($post, 'kojin_name_other_sei');

        $post['name'] = $post['kojin_name_other_mei'];
        Arr::forget($post, 'kojin_name_other_mei');

        $post['surname_ruby'] = $post['kojin_katakana_other_sei'];
        Arr::forget($post, 'kojin_katakana_other_sei');

        $post['name_ruby'] = $post['kojin_katakana_other_mei'];
        Arr::forget($post, 'kojin_katakana_other_mei');

        $post['zipcode'] = $post['zip_kojin_other'];
        Arr::forget($post, 'zip_kojin_other');

        $post['pref'] = $post['pref_kojin_other'];
        Arr::forget($post, 'pref_kojin_other');

        $post['city'] = $post['addr1_kojin_other'];
        Arr::forget($post, 'addr1_kojin_other');

        $post['street'] = $post['addr2_kojin_other'];
        Arr::forget($post, 'addr2_kojin_other');

        $post['age'] = $post['kojin_year_other'];
        Arr::forget($post, 'kojin_year_other');

        $post['tel'] = $post['kojin_tel_other'];
        Arr::forget($post, 'kojin_tel_other');

        $post['email'] = $post['kojin_email_other'];
        Arr::forget($post, 'kojin_email_other');

        $post['where_find'] = implode(',', $post['kojin_dokode_other']) . ',' . $post['kojin_dokode_other_other'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'kojin_dokode_other');
        Arr::forget($post, 'kojin_dokode_other_other');

        $post['sns'] = substr(implode(',', $post['kojin_sns_other']) . ',' . $post['kojin_sns_other_other'], 0, -1);
        $post['sns'] = ltrim($post['sns'], ',');
        Arr::forget($post, 'kojin_sns_other');
        Arr::forget($post, 'kojin_sns_other_other');

        return $this->insertGetId($post);
    }

    // 法人サンプル請求
    public function companySample($post)
    {
        $post = self::forgetWp($post);

        $post['surname'] = $post['hojin_responsible_name_sample'];
        Arr::forget($post, 'hojin_responsible_name_sample');

        $post['company_name'] = $post['hojin_company_name_sample'];
        Arr::forget($post, 'hojin_company_name_sample');

        $post['company_ruby'] = $post['hojin_company_kana_sample'];
        Arr::forget($post, 'hojin_company_kana_sample');

        $post['industry'] = $post['hojin_industries_sample'];
        Arr::forget($post, 'hojin_industries_sample');

        $post['zipcode'] = $post['zip_hojin_sample'];
        Arr::forget($post, 'zip_hojin_sample');

        $post['pref'] = $post['pref_hojin_sample'];
        Arr::forget($post, 'pref_hojin_sample');

        $post['city'] = $post['addr1_hojin_sample'];
        Arr::forget($post, 'addr1_hojin_sample');

        $post['street'] = $post['addr2_hojin_sample'];
        Arr::forget($post, 'addr2_hojin_sample');

        $post['tel'] = $post['hojin_tel_sample'];
        Arr::forget($post, 'hojin_tel_sample');

        $post['fax'] = $post['hojin__fax_sample'];
        Arr::forget($post, 'hojin__fax_sample');

        $post['email'] = $post['hojin_email_sample'];
        Arr::forget($post, 'hojin_email_sample');

        $post['use_application'] = implode(',', $post['hojin_usage_sample']) . ',' . $post['hojin_usage_other_sample'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'hojin_usage_sample');
        Arr::forget($post, 'hojin_usage_other_sample');

        $post['where_find'] = implode(',', $post['hojin_dokode_sample']) . ',' . $post['hojin_dokode_other_sample'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'hojin_dokode_sample');
        Arr::forget($post, 'hojin_dokode_other_sample');

        return $this->insertGetId($post);
    }

    // 法人図面見積もり
    public function companyDraw($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['hojin_sample_zumen'][0];
        Arr::forget($post, 'hojin_sample_zumen');

        // 両方のみだった場合は文字列置換
        $post['quote_details'] = $post['hojin_quote_detail_zumen'][0] === '両方' ? '施工希望、材料のみ' : $post['hojin_quote_detail_zumen'][0];
        Arr::forget($post, 'hojin_quote_detail_zumen');

        $post['ground_condition'] = $post['hojin_ground_status_zumen'][0] . ',' . $post['hojin_ground_status_other_zumen'];
        Arr::forget($post, 'hojin_ground_status_zumen');
        Arr::forget($post, 'hojin_ground_status_other_zumen');

        $post['vertical_size'] = $post['hojin_size_tate_zumen'];
        Arr::forget($post, 'hojin_size_tate_zumen');

        $post['horizontal_size'] = $post['hojin_size_yoko_zumen'];
        Arr::forget($post, 'hojin_size_yoko_zumen');

        $post['desired_product'] = implode(',', $post['hojin_want_product_zumen']);
        Arr::forget($post, 'hojin_want_product_zumen');

        $post['comment'] = $post['hojin_message_zumen'];
        Arr::forget($post, 'hojin_message_zumen');

        $post['name'] = $post['hojin_responsible_name_zumen'];
        Arr::forget($post, 'hojin_responsible_name_zumen');

        $post['company_name'] = $post['hojin_company_name_zumen'];
        Arr::forget($post, 'hojin_company_name_zumen');

        $post['company_ruby'] = $post['hojin_company_kana_zumen'];
        Arr::forget($post, 'hojin_company_kana_zumen');

        $post['industry'] = $post['hojin_industries_zumen'];
        Arr::forget($post, 'hojin_industries_zumen');

        $post['zipcode'] = $post['zip_hojin_zumen'];
        Arr::forget($post, 'zip_hojin_zumen');

        $post['pref'] = $post['pref_hojin_zumen'];
        Arr::forget($post, 'pref_hojin_zumen');

        $post['city'] = $post['addr1_hojin_zumen'];
        Arr::forget($post, 'addr1_hojin_zumen');

        $post['street'] = $post['addr2_hojin_zumen'];
        Arr::forget($post, 'addr2_hojin_zumen');

        $post['tel'] = $post['hojin_tel_zumen'];
        Arr::forget($post, 'hojin_tel_zumen');

        $post['fax'] = $post['hojin__fax_zumen'];
        Arr::forget($post, 'hojin__fax_zumen');

        $post['email'] = $post['hojin_email_zumen'];
        Arr::forget($post, 'hojin_email_zumen');

        $post['use_application'] = implode(',', $post['hojin_usage_zumen']) . ',' . $post['hojin_usage_other_zumen'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'hojin_usage_zumen');
        Arr::forget($post, 'hojin_usage_other_zumen');

        $post['where_find'] = implode(',', $post['hojin_dokode_zumen']) . ',' . $post['hojin_dokode_other_zumen'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'hojin_dokode_zumen');
        Arr::forget($post, 'hojin_dokode_other_zumen');

        if ($post['hojin_file_zumen']) {
            $post['document1_original_name'] = '添付ファイルがあります。メールをご確認ください';
            // $file = base64_decode($post['hojin_file_zumen']);
            // $fileName = Str::random(12).'.'.$post['ext'];
            // file_put_contents(storage_path().'/app/public/draw/'.$fileName, $file);
            // $post['file'] = '/app/public/draw/'.$fileName;
        }
        Arr::forget($post, 'hojin_file_zumen');

        return $this->insertGetId($post);
    }

    // 法人訪問見積もり
    public function companyVisit($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['hojin_sample_houmon'][0];
        Arr::forget($post, 'hojin_sample_houmon');

        $post['industry'] = $post['hojin_industries_houmon'];
        Arr::forget($post, 'hojin_industries_houmon');

        $post['ground_condition'] = $post['hojin_ground_status_houmon'][0] . ',' . $post['hojin_ground_status_other_houmon'];
        Arr::forget($post, 'hojin_ground_status_houmon');
        Arr::forget($post, 'hojin_ground_status_other_houmon');

        $post['comment'] = $post['hojin_message_houmon'];
        Arr::forget($post, 'hojin_message_houmon');

        $post['name'] = $post['hojin_responsible_name_houmon'];
        Arr::forget($post, 'hojin_responsible_name_houmon');

        $post['company_name'] = $post['hojin_company_name_houmon'];
        Arr::forget($post, 'hojin_company_name_houmon');

        $post['company_ruby'] = $post['hojin_company_kana_houmon'];
        Arr::forget($post, 'hojin_company_kana_houmon');

        $post['zipcode'] = $post['zip_hojin_houmon'];
        Arr::forget($post, 'zip_hojin_houmon');

        $post['pref'] = $post['pref_hojin_houmon'];
        Arr::forget($post, 'pref_hojin_houmon');

        $post['city'] = $post['addr1_hojin_houmon'];
        Arr::forget($post, 'addr1_hojin_houmon');

        $post['street'] = $post['addr2_hojin_houmon'];
        Arr::forget($post, 'addr2_hojin_houmon');

        //別住所に訪問する場合

        $post['visit_address'] = $post['hojin_todouhuken_houmon'] . $post['hojin_add01_houmon'] . $post['hojin_add02_houmon'];
        Arr::forget($post, 'hojin_todouhuken_houmon');
        Arr::forget($post, 'hojin_add01_houmon');
        Arr::forget($post, 'hojin_add02_houmon');

        $post['tel'] = $post['hojin_tel_houmon'];
        Arr::forget($post, 'hojin_tel_houmon');

        $post['fax'] = $post['hojin_fax_houmon'];
        Arr::forget($post, 'hojin_fax_houmon');

        $post['email'] = $post['hojin_email_houmon'];
        Arr::forget($post, 'hojin_email_houmon');

        $post['use_application'] = implode(',', $post['hojin_usage_houmon']) . ',' . $post['hojin_usage_other_houmon'];
        $post['use_application'] = ltrim($post['use_application'], ',');
        Arr::forget($post, 'hojin_usage_houmon');
        Arr::forget($post, 'hojin_usage_other_houmon');

        $post['square_meter'] = $post['hojin_heibeisuu_houmon'];
        Arr::forget($post, 'hojin_heibeisuu_houmon');

        $carbon = new Carbon();
        // HP側のフォームに年選択がないので、現在の月と比較して翌年の依頼を分岐
        if (intval($post['hojin_month1_houmon']) >= $carbon->month) {
            $year1 = $carbon->year;
        } else {
            $carbon->addYear();
            $year1 = $carbon->year;
        }
        // sprintfは0を埋めるためのフォーマット関数
        $month1 = sprintf('%02d', $post['hojin_month1_houmon']);
        $day1 = sprintf('%02d', $post['hojin_day1']);
        $time1 = sprintf('%02d:00:00', $post['hojin_time1_houmon']);
        //DateTime型に整形
        $post['desired_datetime1'] = $year1 . '-' . $month1 . '-' . $day1 . ' ' . $time1;

        Arr::forget($post, 'hojin_month1_houmon');
        Arr::forget($post, 'hojin_day1');
        Arr::forget($post, 'hojin_time1_houmon');

        // 第2希望は任意なので、入力があった場合Insert
        if ($post['hojin_month2_houmon']) {
            $carbon = new Carbon();
            if (intval($post['hojin_month2_houmon']) >= $carbon->month) {
                $year2 = $carbon->year;
            } else {
                $carbon->addYear();
                $year2 = $carbon->year;
            }
            $month2 = sprintf('%02d', $post['hojin_month2_houmon']);
            $day2 = sprintf('%02d', $post['hojin_day2']);
            $time2 = sprintf('%02d:00:00', $post['hojin_time2_houmon']);
            $post['desired_datetime2'] = $year2 . '-' . $month2 . '-' . $day2 . ' ' . $time2;
        }
        Arr::forget($post, 'hojin_month2_houmon');
        Arr::forget($post, 'hojin_day2');
        Arr::forget($post, 'hojin_time2_houmon');

        $post['where_find'] = implode(',', $post['hojin_dokode_houmon']) . ',' . $post['hojin_dokode_other_houmon'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'hojin_dokode_houmon');
        Arr::forget($post, 'hojin_dokode_other_houmon');

        return $this->insertGetId($post);
    }

    // 法人その他
    public function companyEtc($post)
    {
        $post = self::forgetWp($post);

        $post['free_sample'] = $post['hojin_sample_other_contact'][0];
        Arr::forget($post, 'hojin_sample_other_contact');

        $post['industry'] = $post['hojin_industries_other'];
        Arr::forget($post, 'hojin_industries_other');

        $post['quote_details'] = $post['hojin_youken_other_contact'][0];
        Arr::forget($post, 'hojin_quote_other');

        $post['use_application'] = implode(',', $post['hojin_youken_other_contact']);
        Arr::forget($post, 'hojin_youken_other_contact');

        $post['comment'] = $post['other_message_other'];
        Arr::forget($post, 'other_message_other');

        $post['name'] = $post['hojin_responsible_name_other'];
        Arr::forget($post, 'hojin_responsible_name_other');

        $post['company_name'] = $post['hojin_company_name_other'];
        Arr::forget($post, 'hojin_company_name_other');

        $post['company_ruby'] = $post['hojin_company_kana_other'];
        Arr::forget($post, 'hojin_company_kana_other');

        $post['zipcode'] = $post['zip_hojin_other'];
        Arr::forget($post, 'zip_hojin_other');

        $post['pref'] = $post['pref_hojin_other'];
        Arr::forget($post, 'pref_hojin_other');

        $post['city'] = $post['addr1_hojin_other'];
        Arr::forget($post, 'addr1_hojin_other');

        $post['street'] = $post['addr2_hojin_other'];
        Arr::forget($post, 'addr2_hojin_other');

        $post['tel'] = $post['hojin_tel_other'];
        Arr::forget($post, 'hojin_tel_other');

        $post['fax'] = $post['hojin__fax_other'];
        Arr::forget($post, 'hojin__fax_other');

        $post['email'] = $post['hojin_email_other'];
        Arr::forget($post, 'hojin_email_other');

        $post['where_find'] = implode(',', $post['hojin_dokode_other']) . ',' . $post['hojin_dokode_other_other'];
        $post['where_find'] = ltrim($post['where_find'], ',');
        Arr::forget($post, 'hojin_dokode_other');
        Arr::forget($post, 'hojin_dokode_other_other');

        return $this->insertGetId($post);
    }

    public function contact_types()
    {
        return $this->hasMany('App\Models\ContactType');
    }

    public function rankings($order, $year = null, $month = null)
    {
        $carbon = new Carbon();

        // shipping_dateは元々発送日が売上が計上されるだったが、前金機能追加に伴い、transactions.created_atが売上が計上日になった
        // 施工面積ランキングの計算
        if ($order == 'number') {
            // サブクエリで売り上げ・面積用の一時テーブルを作成
            $subTable = DB::table('transactions AS t')->select(
                'u.*',
                'u.id AS user_id',
                't.total',
                'contacts.id AS contact_id',
                DB::raw('CASE WHEN pt.cut = 0 THEN (p.horizontal * p.vertical * pt.num) WHEN pt.product_id < 9 THEN 0 ELSE pt.num END AS area'),
                DB::raw('t.created_at AS shipping_date'),
            )
                ->where('u.status', 1)
                ->whereNot('u.id', 176)
                ->where('u.role', 2)
                ->where('t.status', 1)
                ->leftJoin('users AS u', 't.user_id', '=', 'u.id')
                ->leftJoin('contacts', 't.contact_id', '=', 'contacts.id')
                ->leftJoin('product_transactions AS pt', 'pt.transaction_id', '=', 't.id')
                ->join('products AS p', 'p.id', '=', 'pt.product_id');
            // サブクエリここまで
            // distinctで見積書IDの種類をカウント（各FCが施工した件数
            $results = DB::table($subTable)
                ->select('*', DB::raw('SUM(area) AS total_area'), DB::raw('COUNT(distinct contact_id) AS number'))
                ->orderBy('number', 'DESC')->orderBy('total_area', 'DESC')->groupBy('user_id');
            if (empty($year) && empty($month)) {
                $from = $carbon->year - 1 . '-06-01';
                $to = $carbon->year . '-05-31';
                $results->whereBetween('shipping_date', [$from, $to]);
            } elseif (!empty($year) && empty($month)) {
                $from = $year - 1 . '-06-01';
                $to = $year . '-05-31';
                $results->whereBetween('shipping_date', [$from, $to]);
            } else {
                $results->whereYear('shipping_date', $year);
                $results->whereMonth('shipping_date', $month);
            }
            /* 売り上げランキングに合わせることになった
                if( empty($year) && empty($month) ){
                    // 今日は6月1日以降か？
                    if($carbon->month >= 6){
                        $from = $carbon->year . '-06-01';
                        $to = $carbon->year + 1 . '-05-31';
                        $results->whereBetween('shipping_date',[$from,$to]);
                    }else{
                        $from = $carbon->year - 1 . '-06-01';
                        $to = $carbon->year . '-05-31';
                        $results->whereBetween('shipping_date',[$from,$to]);
                    }
                } elseif( !empty($year) && empty($month) ){
                    // 今日は6月1日以降か？
                    if($carbon->month >= 6){
                        $from = $year . '-06-01';
                        $to = $year + 1 . '-05-31';
                        $results->whereBetween('shipping_date',[$from,$to]);
                    }else{
                        $from = $year - 1 . '-06-01';
                        $to = $year . '-05-31';
                        $results->whereBetween('shipping_date',[$from,$to]);
                    }
                }else{
                    $results->whereYear('shipping_date', $year);
                    $results->whereMonth('shipping_date', $month);
                }
                */
            // 売り上げランキングの計算
        } else {
            $subTable = DB::table('transactions AS t')->select(
                'u.*',
                'u.id AS user_id',
                't.total',
                DB::raw('t.created_at AS shipping_date'),
            )
                ->where('t.status', 1)
                ->where('u.status', 1)
                ->where('u.role', 2)
                ->join('users AS u', 't.user_id', '=', 'u.id')
                ->leftJoin('contacts AS c', 't.contact_id', '=', 'c.id');

            $results = DB::table($subTable)->select('*', DB::raw('SUM(total) AS sales'))->orderBy('sales', 'DESC')->groupBy('user_id');
            // サブテーブルで全期間出してから、期間を絞り込む
            if (empty($year) && empty($month)) {
                $from = $carbon->year - 1 . '-06-01';
                $to = $carbon->year . '-05-31';
                $results->whereBetween('shipping_date', [$from, $to]);
            } elseif (!empty($year) && empty($month)) {
                $from = $year - 1 . '-06-01';
                $to = $year . '-05-31';
                $results->whereBetween('shipping_date', [$from, $to]);
            } else {
                $results->whereYear('shipping_date', $year);
                $results->whereMonth('shipping_date', $month);
            }
        }

        // dd($results->toSql());
        if (isFc()) {
            $rankings = $results->limit(5)->get();
        } else {
            $rankings = $results->paginate(50);
        }

        return $rankings;
    }

    public function getCsvFormat($start = null, $end = null)
    {
        // 同一顧客がいる案件のみ集める
        // FCが担当になっている同一顧客は出さない
        $same_costomer_group = $this->select('contacts.id', 'contacts.created_at', 'ct.name AS type_name', 'contacts.comment', 'contacts.surname', 'contacts.name', 'contacts.surname_ruby', 'contacts.name_ruby', 'contacts.zipcode', 'contacts.pref', 'contacts.city', 'contacts.street', 'contacts.tel', 'contacts.email', 'contacts.where_find', 'scc.*')
            ->leftJoin('contact_types AS ct', 'contacts.contact_type_id', '=', 'ct.id')
            ->leftJoin('same_customer_contacts AS scc', 'contacts.id', '=', 'scc.contact_id')
            ->where('contacts.status', 1)
            ->whereNotNull('contacts.sample_send_at')
            ->where(function ($query) {
                // 本部顧客か担当が決まっていない案件
                $query->orWhere('contacts.user_id', 1)->orWhereNull('contacts.user_id');
            })
            ->where('contacts.own_contact', 0)
            ->where('ct.id', 1)
            ->where('contacts.step_id', self::STEP_ASSIGN)
            ->where('scc.ref_contact_id', '<>', null)
            ->whereBetween('contacts.sample_send_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            // 同一顧客も古い方優先
            ->orderBy('contacts.created_at', 'ASC')
            // ->groupBy('scc.ref_contact_id')
            ->groupBy('scc.contact_id');

        $data = $this->select('contacts.id', 'contacts.created_at', 'ct.name AS type_name', 'contacts.comment', 'contacts.surname', 'contacts.name', 'contacts.surname_ruby', 'contacts.name_ruby', 'contacts.zipcode', 'contacts.pref', 'contacts.city', 'contacts.street', 'contacts.tel', 'contacts.email', 'contacts.where_find', 'scc.*')
            ->leftJoin('contact_types AS ct', 'contacts.contact_type_id', '=', 'ct.id')
            ->leftJoin('same_customer_contacts AS scc', 'contacts.id', '=', 'scc.contact_id')
            ->where('contacts.status', 1)
            ->whereNotNull('contacts.sample_send_at')
            /* 10月まで
            ->where(function ($query) {
                // 本部顧客か担当が決まっていない案件
                // FC案件でも本部にサンプル発送を依頼している案件
                $query->orWhere('contacts.user_id', 1)->orWhereNull('contacts.user_id');
            })
            ->where('contacts.own_contact', 0)
            */
            ->where(function ($query) {
                $query->orWhere(function ($cquery) {
                    $cquery->where('contacts.own_contact', 0)->orWhere('contacts.user_id', 1)->orWhereNull('contacts.user_id');
                });
                $query->orWhere(function ($cquery) {
                    $cquery->where('contacts.own_contact', 1)->where('contacts.sample_send_at', '<>', '1970-01-01');
                });
            })
            ->where('ct.id', 1)
            ->where('scc.ref_contact_id', null)
            ->where('contacts.step_id', self::STEP_ASSIGN)
            ->whereBetween('contacts.sample_send_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->orderBy('contacts.created_at', 'ASC')
            ->union($same_costomer_group)
            ->get();

        return $data;
    }

    public function customCsvExport($filters = [], $exports = [])
    {
        // dd($filters);
        $return_array = [];
        $query = $this->query()->whereBetween('contacts.created_at', [$filters['start'] . ' 00:00:00', $filters['end'] . ' 23:59:59'])
            ->whereIn('contacts.status', [1, 3])
            ->whereIn('contacts.contact_type_id', $filters['contact_types'])
            ->join('contact_types AS ct', 'contacts.contact_type_id', '=', 'ct.id')
            ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
            ->leftJoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->join('prefectures AS p', function ($join) {
                $join->on(DB::raw('p.name'), 'LIKE', DB::raw('CONCAT(contacts.pref, "%")'));
            });
        $query->groupBy('contacts.id');
        $query->whereIn('p.id', $filters['prefectures']);
        // $query->whereIn('contacts.step_id', $filters['steps']);
        // step_idに関する絞り込み
        $query->where(function ($query2) use ($filters) {
            if (in_array('fcapply', $filters['steps'], true)) {
                $query2->orWhere(function ($query3) {
                    $query3->where('contacts.step_id', self::STEP_ASSIGN)
                        ->whereNotNull('contacts.user_id');
                });
                $collection = collect($filters['steps']);
                $filters['steps'] = $collection->reject(function ($value) {
                    return $value == 'fcapply';
                });
            } elseif (in_array('notransaction', $filters['steps'], true)) {
                $query2->orWhere(function ($query3) {
                    $query3->whereNull('t.id')
                        ->whereBetween('contacts.step_id', [self::STEP_SHIPPING, self::STEP_REPORT_COMPLETE]);
                });
                $collection = collect($filters['steps']);
                $filters['steps'] = $collection->reject(function ($value) {
                    return $value == 'notransaction';
                });
            }
            $query2->orWhereIn('contacts.step_id', $filters['steps']);
        });
        foreach ($exports as $value) {
            if ($value == 'ruby') {
                $query->addSelect(DB::raw('CONCAT_WS("", contacts.surname_ruby, contacts.name_ruby) AS ruby'));
                continue;
            } elseif ($value == 'name') {
                $query->addSelect(DB::raw('CONCAT_WS("", contacts.surname, contacts.name) AS name'));
                continue;
            } elseif ($value == 'contact_type_id') {
                $query->addSelect('ct.name AS contact_type_name');
            } elseif ($value == 'vertical_horizontal') {
                $query->addSelect(DB::raw('(contacts.vertical_size * contacts.horizontal_size) AS vertical_horizontal'));
            } elseif ($value == 'fc_id') {
                $query->addSelect('u.company_name AS fc_company_name');
            } elseif ($value == 'same_customer') {
                $query->addSelect(DB::raw('GROUP_CONCAT(ref_contact_id) as contact_ids'));
                $query->leftJoin('same_customer_contacts AS scc', 'contacts.id', '=', 'scc.contact_id');
            } elseif ($value == 'created_at') {
                $query->addSelect(DB::raw("DATE_FORMAT(contacts.created_at, '%Y年%m月%d日') AS created_at_format"));
            } elseif ($value == 'completed_at') {
                $query->addSelect(DB::raw("DATE_FORMAT(contacts.completed_at, '%Y年%m月%d日') AS completed_at_format"));
            } elseif ($value == 'quotation_id') {
                $query->addSelect(DB::raw("CONCAT('https://samplefc.local/quotations/', contacts.quotation_id) as quotation_id"));
            } else {
                $query->addSelect('contacts.' . $value);
            }
        }

        // $query->dd();
        $return_array['results'] = $query->orderBy('contacts.id', 'DESC')->get();
        // dd($return_array['results']);

        $return_array['labels'] = [];
        foreach ($exports as $e) {
            array_push($return_array['labels'], contactsColumnToJapanese($e));
        }

        return $return_array;
    }

    /* surameとnameの結合から名前の部分一致を検索 */
    public function customerOrSearch($query, $name = null, $address = null, $tel = null)
    {
        $query->where(function ($query) use ($name, $address, $tel) {
            if (!is_null($name)) {
                //会社名 or 個人名
                $query->where(function ($query) use ($name) {
                    $query->where(DB::raw('CONCAT(contacts.surname, contacts.name)'), 'LIKE', '%' . $name . '%')
                        //法人担当者名の場合nameしか入っていないため
                        ->orWhere('contacts.name', 'LIKE', '%' . $name . '%')
                        ->orWhere('contacts.company_name', 'LIKE', '%' . $name . '%');
                })
                    //個人名カナ・会社名カナ
                    ->orWhere(function ($query) use ($name) {
                        $query->where(DB::raw('CONCAT(contacts.surname_ruby, contacts.name_ruby)'), 'LIKE', '%' . $name . '%')
                            ->orWhere('contacts.company_ruby', 'LIKE', '%' . $name . '%');
                    });
            }
            if (!is_null($address)) {
                $query->orWhere('contacts.email', 'LIKE', '%' . $address . '%');
            }
            if (!is_null($tel)) {
                $query->where(function () use ($query, $tel) {
                    $query->orWhereRaw('replace(contacts.tel, "-", "") = ?', [removeHyphen($tel)])
                        ->orWhereRaw('replace(contacts.tel2, "-", "") = ?', [removeHyphen($tel)])
                        ->orWhereRaw('replace(contacts.tel, "ー", "") = ?', [removeHyphen($tel)])
                        ->orWhereRaw('replace(contacts.tel2, "ー", "") = ?', [removeHyphen($tel)]);
                });
                // $tel_fmt = str_replace(['-', 'ー', '−', '―', '‐'], '', $tel);
                // $query->orWhere('contacts.tel', $tel)->orWhere('contacts.tel', $tel_fmt);
            }
        });

        return $query;
    }

    /* 同一顧客の判断 */
    public function findSameCustomer($contact = null, $ownId = null, $limit = null)
    {
        // distinctで同一のIDを排除
        $query = Contact::query()->distinct()->select('id', 'user_id', 'main_user_id', 'contact_type_id', 'step_id', 'status', 'created_at')->where('id', '<>', $ownId)->where('status', 1);
        // 自分自身のIDと被らないかつ、以下4つのどれかにひっかかった場合
        $query->where(function ($query) use ($contact) {
            $this->sameAddressAndEmail($query, $contact);
            $this->sameAddressAndTel($query, $contact);
            $this->sameAddressAndSurName($query, $contact);
            $this->sameNameAndTelEmail($query, $contact);
        });
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        // dd($query->get());

        return $query->orderBy('id', 'ASC')->get();
    }

    public function findSameCustomerByCreate($contact = null, $ownId = null, $limit = null)
    {
        $query = Contact::query()->distinct()->select('id', 'user_id', 'created_at')->where('id', '<>', $ownId);
        $query->where(function ($query) use ($contact) {
            $this->sameAddressAndEmail($query, $contact);
            $this->sameAddressAndTel($query, $contact);
            $this->sameAddressAndSurName($query, $contact);
            $this->sameNameAndTelEmail($query, $contact);
        });
        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    private function sameAddressAndEmail($query, $contact)
    {
        $street = $contact['street'];
        // 住所判定ゆるくここで切る位置を調整
        $cut_position = -6;
        foreach ($this::$address_keywords as $kw) {
            if (mb_strpos($contact['street'], $kw) !== false) {
                if (mb_strpos($contact['street'], $kw) < $cut_position || $cut_position == 0) {
                    $cut_position = mb_strpos($contact['street'], $kw);
                }
            }
        }
        $street = mb_substr($street, 0, $cut_position);
        if (empty($street)) {
            return $query;
        }

        return $query->orWhere(function ($query) use ($contact, $street) {
            $query->where(DB::raw('CONCAT(pref,city,street)'), 'LIKE', '%' . $contact['pref'] . $contact['city'] . $street . '%');
            $query->whereNotNull('email')->where('email', '<>', '')->where('email', $contact['email'])->where('status', 1);
        });
    }

    private function sameAddressAndTel($query, $contact)
    {
        // 住所と電話場号が一致した場合
        $street = $contact['street'];
        // $cut_position = -6;
        $cut_position = mb_strlen($street);
        foreach ($this::$address_keywords as $kw) {
            if (mb_strpos($contact['street'], $kw) !== false) {
                if (mb_strpos($contact['street'], $kw) < $cut_position || $cut_position == 0) {
                    $cut_position = mb_strpos($contact['street'], $kw);
                }
            }
        }
        $street = mb_substr($street, 0, $cut_position);
        if (empty($street)) {
            return $query;
        }
        // TODOハイフン抜き
        return $query->orWhere(function ($query) use ($contact, $street) {
            $query->where(DB::raw('CONCAT(pref,city,street)'), 'LIKE', '%' . $contact['pref'] . $contact['city'] . $street . '%')->where('status', 1);
            // DBの電話番号が存在する時だけ電話番号の比較
            $query->where(function ($query) use ($contact) {
                if (!is_null($contact['tel']) && $contact['tel'] != '') {
                    $query->whereNotNull('tel')->where('tel', '<>', '')->whereRaw('replace(tel, "-", "") = ?', [removeHyphen($contact['tel'])]);
                    // $query->whereNotNull('tel2')->where('tel2', '<>', '')->whereRaw('replace(tel2, "-", "") = ?', [removeHyphen($contact['tel'])]);
                } elseif (!is_null($contact['tel2']) && $contact['tel2'] != '') {
                    $query->whereNotNull('tel2')->where('tel2', '<>', '')->whereRaw('replace(tel2, "-", "") = ?', [removeHyphen($contact['tel2'])]);
                    // $query->whereNotNull('tel')->where('tel', '<>', '')->whereRaw('replace(tel, "-", "") = ?', [removeHyphen($contact['tel2'])]);
                } else {
                    // 電話番号が入力されなかったら、whereに該当しないようにする
                    $query->where('tel', 'abnsdgsgsagsafas')->where('tel2', 'sadfsajdlfsadjfhasfas');
                }
            });
        });
    }

    private function sameNameAndTelEmail($query, $contact)
    {
        // 顧客名と住所 or 電話番号が一致した場合
        return $query->orWhere(function ($query) use ($contact) {
            if ($contact['contact_type_id'] < 5 && !empty($contact['surname']) && !empty($contact['name'])) {
                $query->where(DB::raw('CONCAT(surname,name)'), $contact['surname'] . $contact['name']);
                // ↓ こっちは法人同士を比較
            } else if ($contact['contact_type_id'] > 4 && !empty($contact['company_name'])) {
                $query->where('company_name', $contact['company_name']);
            }
            // DBの電話番号が存在する時だけ電話番号の比較
            $query->where(function ($query) use ($contact) {
                if (!is_null($contact['tel']) && $contact['tel'] != '') {
                    // orWhere function
                    $query->orWhere(function ($query2) use ($contact) {
                        $query2->whereNotNull('tel')->where('tel', '<>', '')->whereRaw('replace(tel, "-", "") = ?', [removeHyphen($contact['tel'])]);
                    });
                    // $query->whereNotNull('tel2')->where('tel2', '<>', '')->whereRaw('replace(tel2, "-", "") = ?', [removeHyphen($contact['tel'])]);
                } elseif (!is_null($contact['tel2']) && $contact['tel2'] != '') {
                    $query->orWhere(function ($query2) use ($contact) {
                        $query2->whereNotNull('tel2')->where('tel2', '<>', '')->whereRaw('replace(tel2, "-", "") = ?', [removeHyphen($contact['tel2'])]);
                    });
                }
                $query->orWhere(function ($query2) use ($contact) {
                    if (!empty($contact['email'])) {
                        $query2->whereNotNull('email')->where('email', '<>', '')->where('email', $contact['email']);
                    }
                });
            });
        });
    }

    private function sameAddressAndSurName($query, $contact)
    {
        // 顧客名字と住所一致した場合
        $street = $contact['street'];
        $cut_position = mb_strlen($street);
        foreach ($this::$address_keywords as $kw) {
            if (mb_strpos($contact['street'], $kw) !== false) {
                if (mb_strpos($contact['street'], $kw) < $cut_position || $cut_position == 0) {
                    $cut_position = mb_strpos($contact['street'], $kw);
                }
            }
        }
        $street = mb_substr($street, 0, $cut_position);
        if (empty($street)) {
            return $query;
        }

        return $query->orWhere(function ($query) use ($contact, $street) {
            // if ($contact['contact_type_id'] < 5) {
            if ($contact['contact_type_id'] < 5 && !empty($contact['surname'])) {
                $query->where(DB::raw('CONCAT(surname,name)'), $contact['surname'] . $contact['name']);
            } else if ($contact['contact_type_id'] > 4 && !empty($contact['company_name'])) {
                $query->where('company_name', $contact['company_name']);
            }
            $query->where(DB::raw('CONCAT(pref,city,street)'), 'LIKE', '%' . $contact['pref'] . $contact['city'] . $street . '%')->where('status', 1);
        });
    }

    //メインFC登録の処理
    public function setMainFc($sameCustomers, $posts)
    {
        $contact = Contact::find($posts['contact_id']);
        $sameCustomerContacts = SameCustomerContact::where('contact_id', $contact['id'])->get();

        foreach ($sameCustomers as $sc) {
            if (is_null($sc['main_user_id'])) {
                Contact::where('id', $sc['id'])->update(['main_user_id' => $posts['user_id']]);
            }
        }
        foreach ($sameCustomerContacts as $scc) {
            $data = Contact::where('id', $scc['ref_contact_id'])
                ->where('main_user_id', null)
                ->update(['main_user_id' => $posts['user_id']]);
        }
        if (is_null($contact['main_user_id'])) {
            Contact::where('id', $posts['contact_id'])->update(['main_user_id' => $posts['user_id']]);
        }
    }

    // slack報告の自動化メール件数
    public function slackEmail($date)
    {
        $newContact = $this->whereDate('created_at', $date)->where('status', 1)->where('own_contact', 0)->get();
        $mailCnt = count($newContact);
        foreach ($newContact as $c) {
            $sameCustomers = $this->findSameCustomerByCreate($c, $c['id']);
            if (count($sameCustomers) != 0) {
                $createDate = new Carbon($sameCustomers[0]['created_at']->format('Y-m-d'));
                $diff = $date->diffInYears($createDate);
                if ($diff == 0) {
                    $mailCnt -= 1;
                }
            }
        }
        return $mailCnt;
    }

    // slack報告の自動化メール協力店受注
    public function slackTransactions($date)
    {
        $transactions = Transaction::select(DB::raw('count(*) count, user_id'))
            ->whereDate('created_at', $date)
            ->whereNotNull('user_id')
            ->where('status', 1)
            ->groupBy('user_id')->get();
        $transactionsCount = 0;
        $transactionsData = [];
        foreach ($transactions as $t) {
            $transactionsCount += $t['count'];
            $fc = User::where('id', $t['user_id'])->first();
            $arr = '・' . $fc['name'] . ' ' . $t['count'] . '件';
            array_push($transactionsData, $arr);
        }
        return [$transactionsCount, $transactionsData];
    }

    // slack報告FC依頼件数とFC依頼名
    public function slackFcAssign($date)
    {
        $contacts = $this->select(DB::raw('count(*) count, user_id, contact_type_id'))
            ->whereDate('fc_assigned_at', $date)
            ->where('own_contact', 0)
            ->whereNotNull('user_id')
            ->where('status', 1)
            ->groupBy('user_id')->get();
        $contactsCount = 0;
        $contactsData = [];
        foreach ($contacts as $c) {
            $contactsCount += $c['count'];
            $fc = User::where('id', $c['user_id'])->first();
            $arr = '・' . $fc['name'] . ' ' . returnContactType($c['contact_type_id']) . ' ' . $c['count'] . '件';
            array_push($contactsData, $arr);
        }
        return [$contactsCount, $contactsData];
    }

    public function filteringContact($request, $query)
    {
        $isFilteringContact = ($request->has('fc') || $request->has("type") || $request->has('created_at') || $request->has('sent_at')) ? true : false;

        return $query->where(function ($query) use ($request, $isFilteringContact) {
            if (isAdmin() && $isFilteringContact) {
                //fcの条件の有無
                if (!is_null($request->input('fc'))) {
                    if ($request->input('fc') != 0) {
                        $query->where('contacts.user_id', $request->input('fc'));
                    }
                }
                //問い合わせ種別の有無
                if ($request->has('type')) {
                    $query->whereIn('contacts.contact_type_id', $request->input('type'));
                }
                //問い合わせ日条件の有無
                if (!is_null($request->input('created_at'))) {
                    $parseCreatedAt = explode(" から ", $request->input('created_at'));
                    if (isset($parseCreatedAt[1])) {
                        $query->whereBetween(DB::raw("DATE_FORMAT(contacts.created_at, '%Y-%m-%d')"), [$parseCreatedAt[0], $parseCreatedAt[1]]);
                    } else {
                        $separateCreatedAt = explode("-", $parseCreatedAt[0]);
                        $query->whereYear('contacts.created_at', $separateCreatedAt[0])
                            ->whereMonth('contacts.created_at', $separateCreatedAt[1])
                            ->whereDay('contacts.created_at', $separateCreatedAt[2]);
                    }
                }
                //サンプル送付日条件の有無
                if (!is_null($request->input('sent_at'))) {
                    $parseSentAt = explode(" から ", $request->input('sent_at'));
                    if (isset($parseSentAt[1])) {
                        $query->whereBetween('contacts.sample_send_at', [$parseSentAt[0], $parseSentAt[1]]);
                    } else {
                        $separateSentAt = explode("-", $parseSentAt[0]);
                        $query->whereYear('contacts.sample_send_at', $separateSentAt[0])
                            ->whereMonth('contacts.sample_send_at', $separateSentAt[1])
                            ->whereDay('contacts.sample_send_at', $separateSentAt[2]);
                    }
                }
                //都道府県条件の有無
                if (!is_null($request->input('prefectures'))) {
                    $prefectures_name = $request->input('prefectures');
                    if ($prefectures_name != '0') {
                        $query->where('contacts.pref', 'LIKE', "%$prefectures_name%");
                    }
                };
            }
        });
    }

    private function analysisUsers($query_string = [])
    {
        $users_query = User::query()->select('users.id as user_id', 'users.company_name', 'p.name AS prefecture_name', 'users.status AS user_status')
            ->where('users.name', 'not like', "%テスト%")
            ->where('users.name', 'not like', "%確認%")
            ->where('users.status', '<>', 99)
            ->leftJoin('prefectures as p', 'p.id', '=', 'users.prefecture_id')
            ->orderBy('users.role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('users.id', 'ASC');
        if (!empty($query_string['fcs'])) {
            $users_query->whereIn('users.id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $users_query->whereIn('users.prefecture_id', $query_string['prefs']);
        }

        return $users_query->get()->toArray();
    }

    public function analysisRequests($query_string = [])
    {
        $users = $this->analysisUsers($query_string);
        // 本部からの依頼を集計：依頼は本部→FCへの紹介件数（キャンセル関係なし → 削除はじく）→ 依頼は紹介した月
        $fc_query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            'u.id as user_id',
            'u.company_name',
            'u.status AS user_status',
            'u.role',
            'p.name AS prefecture_name',
            'p.id AS prefecture_id'
        )
            ->whereIn('contacts.status', [1, 3])
            ->where('contacts.own_contact', 0)
            ->whereNotNull('contacts.fc_assigned_at')
            ->where('u.role', 2)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id')
            ->orderBy('p.id', 'ASC')
            ->orderBy('u.id', 'ASC');

        if ($query_string['display'] == 'yearmonth') {
            $fc_query->addSelect(DB::raw('DATE_FORMAT(contacts.fc_assigned_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $fc_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $fc_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $fc_query->addSelect(DB::raw('DATE_FORMAT(contacts.fc_assigned_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $fc_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $fc_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $fc_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $fc_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        // dd($fc_query->get());
        // 本部に表示するのは本部見積もり案件数 → UNIONで合体
        $admin_query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            'u.id as user_id',
            'u.company_name',
            'u.status AS user_status',
            'u.role',
            'p.name AS prefecture_name',
            'p.id AS prefecture_id'
        )
            ->whereIn('contacts.status', [1, 3])
            ->whereIn('contact_type_id', [2, 6])
            ->where(function ($query) {
                $query->orWhere('contacts.user_id', 1)
                    ->orWhere('contacts.user_id', null);
            })
            ->where('contacts.quote_details', '材料のみ')
            ->where('u.role', 1)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id');

        if ($query_string['display'] == 'yearmonth') {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $admin_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $admin_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        $tmp_results = $admin_query->unionAll($fc_query)->orderBy('role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('user_id')->get();
        // dd($tmp_results);
        $own_contacts = $this->analysisOwnContacts($query_string);
        // 月別データに整形
        $users['sums'] = [];
        $users['sums']['total'] = 0;
        $display_type = $query_string['display'] === 'yearmonth' ? 'month' : 'year';
        foreach ($users as $key => $u) {
            $users[$key]['counts'] = [];
            $users[$key]['sums']['total'] = 0;
            // sumsはループしない
            if (empty($u['user_id'])) {
                break;
            }
            foreach ($tmp_results as $tr) {
                if (intval($u['user_id']) == intval($tr['user_id'])) {
                    // 合計
                    if (empty($users['sums'][$tr[$display_type]])) {
                        $users['sums'][$tr[$display_type]] = 0;
                    }
                    $users['sums'][$tr[$display_type]] = $users['sums'][$tr[$display_type]] + intval($tr['contact_count']);
                    $users['sums']['total'] = $users['sums']['total'] + intval($tr['contact_count']);
                    $own_contact = myArrayFilter($own_contacts, 'user_id', $u['user_id']);
                    $users[$key]['counts'][$tr[$display_type]] = $tr['contact_count'];
                }
            }
        }
        return $users;
    }

    public function analysisTransactions($query_string = [])
    {
        $users = $this->analysisUsers($query_string);
        // 本部依頼かつ、発注が来た案件）発注日が受注タイミング
        $transaction_count_query = Transaction::query()->select(DB::raw('COUNT(*) AS transaction_count'), 'transactions.user_id', 'c.own_contact')
            ->where('c.own_contact', 0)->where('c.status', 1)->where('transactions.status', 1)
            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('users as u', 'transactions.user_id', '=', 'u.id')
            ->leftJoin('prefectures as p', 'u.prefecture_id', '=', 'p.id');
        $transaction_count_query = $this->filterFromQueryString($transaction_count_query, $query_string);
        $transaction_count_result = $transaction_count_query->groupBy('transactions.user_id')->orderBy('u.id', 'ASC')->get();
        // dd($transaction_count_result);
        // $fc_query = FCの自己獲得案件からの発注
        $fc_query = Transaction::query()
            ->select(
                DB::raw('COUNT( DISTINCT transactions.contact_id ) AS transaction_count'),
                DB::raw('COUNT( DISTINCT transactions.contact_id ) AS myself_transaction_count'),
                DB::raw('COUNT( DISTINCT transactions.contact_id ) AS myself_only_transaction_count'),
                'u.id as user_id',
                'u.role',
            )
            ->where('transactions.status', 1)->where('c.own_contact', 1)
            ->leftJoin('product_transactions as pt', 'transactions.id', '=', 'pt.transaction_id')
            ->leftJoin('products as pr', 'pt.product_id', '=', 'pr.id')
            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->leftJoin('users as u', 'transactions.user_id', '=', 'u.id')
            ->orderBy('u.role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('u.id', 'ASC')
            ->groupBy('u.id');
        $fc_query = $this->filterFromQueryString($fc_query, $query_string);
        // dd($fc_query->get());
        // ここまで一致
        // $fc_only_query = 案件に紐付かない資材発注をカウント
        $fc_only_query = Transaction::query()
            ->select(
                DB::raw('COUNT( DISTINCT transactions.id ) AS transaction_count'),
                DB::raw('COUNT( DISTINCT transactions.id ) AS myself_transaction_count'),
                DB::raw('COUNT( DISTINCT transactions.id ) AS myself_only_transaction_count'),
                'u.id as user_id',
                'u.role',
            )
            ->where('transactions.status', 1)->whereNull('transactions.contact_id')
            // 自己獲得案件に紐づく発注 or 自己獲得案件なら副資材だけでもカウントする or 案件に紐付かない案件（芝の注文）ならカウント
            ->where(function ($query) {
                // 案件に紐付かない案件（芝の注文）ならカウント(自由記述に書かれた芝も拾う 
                $query->orWhere('pr.product_type_id', 1)
                    ->orWhere('pt.other_product_name', 'LIKE', "%芝%")
                    ->orWhere('pt.other_product_name', 'LIKE', "%本部見積書%")
                    ->orWhere('pt.other_product_name', 'LIKE', "%芝%");
            })
            ->leftJoin('product_transactions as pt', 'transactions.id', '=', 'pt.transaction_id')
            ->leftJoin('products as pr', 'pt.product_id', '=', 'pr.id')
            ->leftJoin('users as u', 'transactions.user_id', '=', 'u.id')
            ->orderBy('u.role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('u.id', 'ASC')
            ->groupBy('u.id');
        $fc_only_query = $this->filterFromQueryString($fc_only_query, $query_string);
        // dd($fc_only_query->get());
        // FCの自己獲得案件かつ発注スキップ案件をUNIONで合体
        $fc_skip_query = $this->query()
            ->select(
                DB::raw('COUNT( DISTINCT contacts.id) AS transaction_count'),
                DB::raw('COUNT( DISTINCT contacts.id ) AS myself_transaction_count'),
                DB::raw('COUNT( DISTINCT contacts.id ) AS myself_only_transaction_count'),
                'u.id as user_id',
                'u.role',
            )
            ->whereNull('t.id')
            ->where('contacts.status', 1)
            ->where('contacts.own_contact', 1)
            ->whereIn('contacts.step_id', [9, 10, 11])
            ->leftJoin('users as u', 'contacts.user_id', '=', 'u.id')
            ->leftJoin('transactions as t', 'contacts.id', '=', 't.contact_id')
            ->orderBy('u.role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('u.id', 'ASC')
            ->groupBy('u.id');
        $fc_skip_query = $this->filterFromQueryStringContacts($fc_skip_query, $query_string);
        // dd($fc_skip_query->get());

        // 本部に表示するのは本部見積もり案件発送までいった案件数 → UNIONで合体
        $admin_query = $this->query()->select(
            DB::raw('COUNT(*) AS transaction_count'),
            DB::raw('COUNT(*) AS myself_transaction_count'),
            DB::raw('COUNT( contacts.own_contact=1 OR NULL) AS myself_only_transaction_count'),
            'u.id as user_id',
            'u.role'
        )
            ->whereIn('contacts.status', [1, 3])
            ->whereIn('contact_type_id', [2, 6])
            ->where('step_id', '>', self::STEP_SHIPPING)
            ->where(function ($query) {
                $query->orWhere('contacts.user_id', 1)
                    ->orWhere('contacts.user_id', null);
            })
            ->where('contacts.quote_details', '材料のみ')
            ->where('u.role', 1)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->groupBy('u.id');
        if ($query_string['display'] == 'yearmonth') {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $admin_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $admin_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        // 年間なら集計変えないといけないので分岐
        $union_table = $admin_query->unionAll($fc_query)->unionAll($fc_only_query)->unionAll($fc_skip_query);
        $tmp_query = DB::query()->fromSub($union_table, 'ut')
            ->select(
                DB::raw('SUM( transaction_count ) AS transaction_count'),
                DB::raw('SUM( myself_transaction_count ) AS myself_transaction_count'),
                DB::raw('SUM( myself_only_transaction_count ) AS myself_only_transaction_count'),
                'ut.user_id',
                'ut.role'
            );
        if ($query_string['display'] === 'year') {
            $tmp_query->addSelect('ut.year')->groupBy('ut.user_id', 'ut.year');
        } else {
            $tmp_query->addSelect('ut.month')->groupBy('ut.user_id', 'ut.month');
        }
        $tmp_results = $tmp_query->get();
        // dd($tmp_results);
        foreach ($tmp_results as $key => $t) {
            $tmp_results[$key] = json_decode(json_encode($t), true);
        }
        // dd($tmp_results);
        $own_contacts = $this->analysisOwnContacts($query_string);
        // $total_contactsは受注率の分母
        $total_contacts = $this->analysisContactCounts($query_string);
        $users['sums'] = [];
        $users['sums']['introduce'] = 0;
        $users['sums']['myself'] = 0;
        $users['sums']['introduces'] = [];
        $users['sums']['myselfs'] = [];
        $users['order_rate'] = [];
        $users['order_rate']['introduce'] = 0;
        $users['order_rate']['myself'] = 0;
        /*  ====== 配列キーの説明
            - sums 合計系
            - order_rate 受注率系
            - counts 各セルに表示する数値
        ========= */
        $display_type = $query_string['display'] === 'yearmonth' ? 'month' : 'year';
        // 合計行はusersループの外でやる 依頼の合計
        foreach ($transaction_count_result as $tcr) {
            if (empty($users['sums']['introduces'][$tcr[$display_type]])) {
                $users['sums']['introduces'][$tcr[$display_type]] = 0;
            }
            $users['sums']['introduces'][$tcr[$display_type]] += $tcr['transaction_count'];
            $users['sums']['introduce'] += $tcr['transaction_count'];
        }
        foreach ($users as $key => $u) {
            // sumsはループしない
            $users[$key]['counts']['introduce'] = [];
            $users[$key]['counts']['myself'] = [];
            $users[$key]['myself_only_transaction_count'] = 0;
            $users[$key]['own_contacts'] = 0;
            if (empty($u['user_id'])) {
                break;
            }
            foreach ($tmp_results as $tr) {
                // 本部ならなら合計行に追加
                if (intval($u['user_id']) === 1 && intval($u['user_id']) === intval($tr['user_id'])) {
                    if (empty($users['sums']['introduces'][$tr[$display_type]])) {
                        $users['sums']['introduces'][$tr[$display_type]] = 0;
                    }
                    $users['sums']['introduces'][$tr[$display_type]] += $tr['transaction_count'];
                    $users['sums']['introduce'] += $tr['transaction_count'];
                }
                // dd($tr);
                if (intval($u['user_id']) === intval($tr['user_id'])) {
                    // 自己獲得の合計
                    if (empty($users['sums']['myselfs'][$tr[$display_type]])) {
                        $users['sums']['myselfs'][$tr[$display_type]] = 0;
                    }
                    $users['sums']['myselfs'][$tr[$display_type]] += intval($tr['myself_only_transaction_count']);
                    $users['sums']['myself'] += intval($tr['myself_only_transaction_count']);

                    $own_contact = myArrayFilter($own_contacts, 'user_id', $u['user_id']);
                    $total_contact = myArrayFilter($total_contacts, 'user_id', $u['user_id']);
                    $users[$key]['own_contacts'] = empty($own_contact['contact_count']) ? 0 : $own_contact['contact_count'];
                    $users[$key]['total_contacts'] = !empty($total_contact['contact_count']) ? $total_contact['contact_count'] : 0;
                    $users[$key]['myself_only_transaction_count'] = $tr['myself_only_transaction_count'];
                    $users[$key]['counts']['introduce'][$tr[$display_type]] = !empty($introduce['transaction_count']) ? $introduce['transaction_count'] : null;
                    $users[$key]['counts']['myself'][$tr[$display_type]] = $tr['transaction_count'];
                    if ($users[$key]['user_id'] === 1) {
                        $users[$key]['success_contacts'] = $this->adminCompleteContactCount($query_string);
                        $users[$key]['counts']['introduce'][$tr[$display_type]] = $tr['transaction_count'];
                        $users[$key]['counts']['myself'][$tr[$display_type]] = null;
                    }
                }
            } //foreach ($tmp_results as $tr) 

            foreach ($transaction_count_result as $tcr) {
                // 本部依頼（$transaction_count_result）だけが存在する場合
                if (($tcr['user_id'] === $u['user_id']) && empty($users[$key]['counts']['introduce'][$tcr[$display_type]])) {
                    $own_contact = myArrayFilter($own_contacts, 'user_id', $u['user_id']);
                    $total_contact = myArrayFilter($total_contacts, 'user_id', $u['user_id']);
                    $users[$key]['own_contacts'] = empty($own_contact['contact_count']) ? 0 : $own_contact['contact_count'];
                    $users[$key]['total_contacts'] = !empty($total_contact['contact_count']) ? $total_contact['contact_count'] : 0;
                    $users[$key]['counts']['introduce'][$tcr[$display_type]] = intval($tcr['transaction_count']);
                }
            }
        } // foreach(users)
        // dd($users);
        return $users;
    }

    public function analysisContacts($query_string = [])
    {
        $users = $this->analysisUsers($query_string);
        // FCの自己獲得案件数
        $fc_query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            'u.id as user_id',
            'u.company_name',
            'u.status AS user_status',
            'u.role',
            'p.name AS prefecture_name',
            'p.id AS prefecture_id'
        )
            ->whereIn('contacts.status', [1, 3])
            ->where('contacts.own_contact', 1)
            ->where('u.role', 2)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id')
            ->orderBy('prefecture_id', 'ASC')
            ->orderBy('u.id', 'ASC');

        if ($query_string['display'] == 'yearmonth') {
            $fc_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $fc_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $fc_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $fc_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $fc_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $fc_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $fc_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $fc_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        // 本部の問い合わせ数
        // user_id =null も本部案件として処理するので、CASE WHENで分岐
        $admin_query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            DB::raw('(CASE WHEN u.id IS NULL THEN 1 ELSE u.id END) AS user_id'),
            DB::raw('(CASE WHEN u.company_name IS NULL THEN "サンプル株式会社" ELSE u.company_name END) AS company_name'),
            DB::raw('(CASE WHEN u.status IS NULL THEN 1 ELSE u.status END) AS user_status'),
            DB::raw('(CASE WHEN u.role IS NULL THEN 1 ELSE u.role END) AS role'),
            DB::raw('(CASE WHEN p.name IS NULL THEN "愛知県" ELSE p.name END) AS prefecture_name'),
            DB::raw('(CASE WHEN p.id IS NULL THEN 18 ELSE p.id END) AS prefecture_id'),
        )
            ->whereIn('contacts.status', [1, 3])
            ->where('contacts.own_contact', 0)
            ->where(function ($query) {
                $query->orWhere('contacts.user_id', 1)
                    ->orWhere('contacts.user_id', null);
            })
            ->leftJoin('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id');

        if ($query_string['display'] == 'yearmonth') {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $admin_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $admin_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $admin_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        $tmp_results = $admin_query->unionAll($fc_query)->orderBy('role', 'ASC')->orderBy('prefecture_id', 'ASC')->orderBy('user_id')->get();
        // dd($tmp_results);
        $own_contacts = $this->analysisOwnContacts($query_string);
        // 月別データに整形
        $users['sums'] = [];
        $users['sums']['total'] = 0;
        $display_type = $query_string['display'] === 'yearmonth' ? 'month' : 'year';
        foreach ($users as $key => $u) {
            $users[$key]['counts'] = [];
            $users[$key]['sums']['total'] = 0;
            // sumsはループしない
            if (empty($u['user_id'])) {
                break;
            }
            foreach ($tmp_results as $tr) {
                if (intval($u['user_id']) == intval($tr['user_id'])) {
                    // 合計
                    if (empty($users['sums'][$tr[$display_type]])) {
                        $users['sums'][$tr[$display_type]] = 0;
                    }
                    $users['sums'][$tr[$display_type]] = $users['sums'][$tr[$display_type]] + intval($tr['contact_count']);
                    $users['sums']['total'] = $users['sums']['total'] + intval($tr['contact_count']);
                    $own_contact = myArrayFilter($own_contacts, 'user_id', $u['user_id']);
                    $users[$key]['counts'][$tr[$display_type]] = $tr['contact_count'];
                }
            }
        }
        return $users;
        // 月別データに整形
        $results = [];
        $results['sums'] = [];
        $results['sums']['total'] = 0;
        $order = 0;
        if ($query_string['display'] == 'yearmonth') {
            // このままだとFCID順になるので、並べ替え
            foreach ($tmp_results as $key => $tr) {
                if (empty($results['sums'][$tr['month']])) {
                    $results['sums'][$tr['month']] = 0;
                }
                $results['sums'][$tr['month']] = $results['sums'][$tr['month']] + intval($tr['contact_count']);
                $results['sums']['total'] = $results['sums']['total'] + intval($tr['contact_count']);
                $own_contact = myArrayFilter($own_contacts, 'user_id', $tr['user_id']);
                $results[$order]['prefecture_name'] = $tr['prefecture_name'];
                $results[$order]['name'] = $tr['company_name'];
                $results[$order]['user_status'] = $tr['user_status'];
                $results[$order]['own_contacts'] = $own_contact['contact_count'];
                $results[$order]['counts'][$tr['month']] = $tr['contact_count'];
                // 最後はundefindedindexになるので分岐
                if (!empty($tmp_results[$key + 1])) {
                    if ($tr['user_id'] != $tmp_results[$key + 1]['user_id']) $order++;
                }
            }
        } else {
            foreach ($tmp_results as $key => $tr) {
                if (empty($results['sums'][$tr['year']])) {
                    $results['sums'][$tr['year']] = 0;
                }
                $results['sums'][$tr['year']] = $results['sums'][$tr['year']] + intval($tr['contact_count']);
                $results['sums']['total'] = $results['sums']['total'] + intval($tr['contact_count']);
                $own_contact = myArrayFilter($own_contacts, 'user_id', $tr['user_id']);
                $results[$order]['prefecture_name'] = $tr['prefecture_name'];
                $results[$order]['name'] = $tr['company_name'];
                $results[$order]['user_status'] = $tr['user_status'];
                $results[$order]['own_contacts'] = $own_contact['contact_count'];
                $results[$order]['counts'][$tr['year']] = $tr['contact_count'];
                // 最後はundefindedindexになるので分岐
                if (!empty($tmp_results[$key + 1])) {
                    if ($tr['user_id'] != $tmp_results[$key + 1]['user_id']) $order++;
                }
            }
        }
        return $results;
    }

    public function analysisContactCounts($query_string = [])
    {
        $query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            'u.id as user_id'
        )
            ->whereIn('contacts.status', [1, 3])
            ->where('u.role', 2)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id')
            ->orderBy('u.role', 'ASC')
            ->orderBy('prefecture_id', 'ASC')
            ->orderBy('u.id', 'ASC');

        if ($query_string['display'] == 'yearmonth') {
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        // 受注ならFC自己獲得も含める
        if ($query_string['type'] != '2') {
            $query->where('contacts.own_contact', 0)->whereNotNull('contacts.fc_assigned_at');
        }
        if (!empty($query_string['fcs'])) {
            $query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        // 本部は本部顧客のカウント
        $admin_query = $this->query()->select(
            DB::raw('COUNT(*) AS contact_count'),
            'u.id as user_id'
        )
            ->whereIn('contacts.status', [1, 3])
            ->whereIn('contact_type_id', [2, 6])
            ->where(function ($squery) {
                $squery->orWhere('contacts.user_id', 1)
                    ->orWhere('contacts.user_id', null);
            })
            ->where('contacts.quote_details', '材料のみ')
            ->where('u.role', 1)
            ->leftJoin('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id');

        if ($query_string['display'] == 'yearmonth') {
            if (!empty($query_string['start'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            if (!empty($query_string['startyear'])) {
                $admin_query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $admin_query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $admin_query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $admin_query->whereIn('u.prefecture_id', $query_string['prefs']);
        }

        return $admin_query->unionAll($query)->get();
    }

    public function analysisOwnContacts($query_string = [])
    {
        $query = $this->query()->select(DB::raw('COUNT(*) AS contact_count'), 'u.id AS user_id')
            ->whereIn('contacts.status', [1, 3])
            ->where('contacts.own_contact', 1)
            ->where('u.role', 2)
            ->join('users as u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('user_id')
            ->orderBy('prefecture_id', 'ASC');
        if ($query_string['display'] == 'yearmonth') {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.fc_assigned_at, "%Y-%m") AS month'));
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.fc_assigned_at, "%Y") AS year'));
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $query->whereIn('contacts.user_id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $query->whereIn('u.prefecture_id', $query_string['prefs']);
        }
        // dd($query->get());
        return $query->get();
    }

    public function adminContactsAnalysis($query_string = [], $data = [])
    {
        $query = $this->query()->select(DB::raw('COUNT(*) AS contact_count'), 'contacts.pref', 'zipcode')
            // $query = $this->query()->select( 'contacts.pref' )
            ->whereIn('contacts.status', [1, 3])
            ->where('contacts.own_contact', 0)
            // ->leftJoin('prefectures as p', 'p.id', '=', 'u.prefecture_id')
            ->groupBy('pref')
            ->orderBy('pref', 'ASC');
        if ($query_string['display'] == 'yearmonth') {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month');
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } elseif ($query_string['display'] == 'year6') {
            // dd(YEAR(DATE_SUB(contacts.created_at, INTERVAL 6 MONTH)));
            // $query->addSelect(DB::raw('DATE_FORMAT( DATE_SUB(contacts.created_at, INTERVAL -6 MONTH) , "%Y") AS year'))->groupBy('year');
            $query->addSelect(DB::raw('YEAR( DATE_SUB(contacts.created_at, INTERVAL +6 MONTH)) AS year'))->groupBy('year');
            // dd($query);
            // $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS yearmonth'))->groupBy('yearmonth');
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-06-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] + 1 . '-05-31');
            }
        } else {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year');
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        $results = $query->get();
        // dd($results[4]);
        // if($query_string['display'] == 'year6'){
        //     foreach($results AS $r){
        //         \Log::debug(print_r($r, true));
        //     }
        // }

        return $results;
    }

    public function analysisSums($query_string, $data)
    {
        $tmp = [];
        foreach ($data['users'] as $u) {
            // \Log::debug(print_r($u, true));
            // dd($u)
        }
        return $tmp;
    }

    /*==========
      絞り込みをクエリビルダに追加
      $query = クエリビルダ（ ->queryで作った分割可能クエリ）
      $query_string = 絞り込み条件
      返り値 = クエリビルダ
    ===========*/
    private function filterFromQueryString($query = '', $query_string = [])
    {
        if ($query_string['display'] == 'yearmonth') {
            $query->addSelect(DB::raw('DATE_FORMAT(transactions.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $query->where('transactions.created_at', '>=', $query_string['start'] . '-01 00:00:00');
            }
            if (!empty($query_string['end'])) {
                $query->where('transactions.created_at', '<=', $query_string['end'] . '-31 23:59:59');
            }
        } else {
            $query->addSelect(DB::raw('DATE_FORMAT(transactions.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $query->where('transactions.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('transactions.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $query->whereIn('u.id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $query->whereIn('u.prefecture_id', $query_string['prefs']);
        }

        return $query;
    }

    /*==========
      絞り込みをクエリビルダに追加
      $query = クエリビルダ（ ->queryで作った分割可能クエリ）
      $query_string = 絞り込み条件
      返り値 = クエリビルダ
    ===========*/
    private function filterFromQueryStringContacts($query = '', $query_string = [])
    {
        if ($query_string['display'] == 'yearmonth') {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01 00:00:00');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31 23:59:59');
            }
        } else {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        if (!empty($query_string['fcs'])) {
            $query->whereIn('u.id', $query_string['fcs']);
        }
        if (!empty($query_string['prefs'])) {
            $query->whereIn('u.prefecture_id', $query_string['prefs']);
        }

        return $query;
    }

    private function adminCompleteContactCount($query_string = [])
    {
        $query = $this::query()->selectRaw('COUNT(*) AS count')
            ->where('user_id', 1)
            ->where('step_id', '>=', self::STEP_REPORT_COMPLETE)
            ->where('status', 1);
        if ($query_string['display'] == 'yearmonth') {
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31');
            }
        } else {
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01');
            }
            if (!empty($query_string['endyear'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31');
            }
        }
        $result = $query->first();

        return $result['count'];
    }

    public function waitShipment()
    {
        $contacts = $this::select('contacts.*', 'contacts.id AS contact_id', 'u.id AS fc_id', 'u.name AS fc_name', 'u.pref AS user_pref', 'u.city AS user_city', 'u.street AS user_street', 't.id AS transaction_id', 't.address', 't.consignee', 't.created_at AS transaction_created_at', 't.delivery_at', 't.direct_shipping', \DB::raw('COUNT(t.contact_id) AS transaction_count'))
            ->where('contacts.status', 1)
            ->where('contacts.user_id', \Auth::id())
            ->whereIn('step_id', [self::STEP_SHIPPING])
            ->where('t.status', 1)
            ->leftJoin('users AS u', 'u.id', '=', 'contacts.user_id')
            ->leftJoin('transactions AS t', 't.contact_id', '=', 'contacts.id')
            ->groupBy('contacts.id')->get()->toArray();

        $transactions = Transaction::select(
            'c.id AS contact_id',
            'c.contact_type_id',
            'c.surname',
            'c.name',
            'c.company_name',
            'c.pref',
            'c.city',
            'c.street',
            'u.id AS fc_id',
            'u.name AS fc_name',
            'transactions.id AS transaction_id',
            'transactions.address',
            'transactions.consignee',
            'transactions.created_at AS transaction_created_at',
            'transactions.delivery_at',
            \DB::raw('COUNT(transactions.contact_id) AS transaction_count')
        )
            ->where('transactions.user_id', \Auth::id())
            ->where('transactions.status', 1)
            ->where('transactions.transaction_only_shipping_date', null)
            ->where(function ($query) {
                // 通常案件
                $query->orWhere(function ($query) {
                    $query->where('c.step_id', self::STEP_SHIPPING);
                    $query->whereNull('transactions.transaction_only_shipping_date');
                    $query->whereNull('c.shipping_date');
                });
                // 未発送の案件に紐づかない発注書
                $query->orWhere(function ($query) {
                    $query->whereNull('transactions.transaction_only_shipping_date');
                    // $query->whereNull('transactions.shipping_cost');
                    $query->whereNull('transactions.contact_id');
                });
            })
            ->leftJoin('users AS u', 'u.id', '=', 'transactions.user_id')
            ->leftJoin('contacts AS c', 'c.id', '=', 'transactions.contact_id')
            ->groupBy('transactions.id')->get()->toArray();

        return $transactions;
    }
    // ここから案件問い合わせ詳細分析ページ用
    /*==========
      $query_string = 絞り込み条件
      返り値例 = [
        1940 => [
            'count' => 1,
            'month' => '2019-01'
        ],
        1950 => [
            'count' => 2,
            'month' => '2019-01'
        ],
      ]
    ===========*/
    public function analysisContactDetailAges($query_string)
    {
        // contactsテーブルからageそれぞれをカウントする
        $contacts_query = Contact::query()->select(
            'age',
            DB::raw('count(*) as count'),
            // フォーマットがバラバラなので、重複カウントの必要あり
            DB::raw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(age, '年', ''), '代', ''), '年代', ''), '１', '1'), '２', '2'), '３', '3'), '４', '4'), '５', '5'), '６', '6'), '７', '7'), '８', '8'), '９', '9'), '０', '0') AS formatted_age")
        );
        $contacts_query = $this->filterOfCreatedAt($contacts_query, $query_string);
        /*
        if( $query_string['display'] == 'yearmonth'){
            $contacts_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if(!empty($query_string['start'])){
                $contacts_query->where('contacts.created_at', '>=', $query_string['start'] . '-01 00:00:00');
            }
            if(!empty($query_string['end'])){
                $contacts_query->where('contacts.created_at', '<=', $query_string['end'] . '-31 23:59:59');
            }
        }else{
            $contacts_query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
        }
        */

        $contacts = $contacts_query->where('status', 1)
            ->whereNotNull('age')
            ->whereNot('age', '')
            ->groupBy('formatted_age')->orderBy('age', 'ASC')
            ->get()->toArray();

        $results = [];

        // 先に配列を作成
        foreach ($contacts as $value) {
            $age = mb_convert_kana($value['age'], 'n');
            $age = preg_replace('/\D/', '', $age);
            $results[$age] = [];
        }
        //新しい配列に年代別にvalueを入れる
        foreach ($contacts as $key => $value) {
            $age = mb_convert_kana($value['age'], 'n');
            $age = preg_replace('/\D/', '', $age);
            $value['age'] = $age;
            /*
        // まずは$results[$age]の中にネスト配列があるかチェック
            if(!empty($results[$age][0])){
        // $results[$age]をループして'month'か'year'が一致したら、valueを足す
                foreach($results[$age] as $key2 => $value2){
        // もしキー$age内にある
        \Log::debug(print_r("$age . 'もしキーかyearが一致したら、valueを足す'", true));
                    // $month = $results[$age][$key2]['month'];
                    // \Log::debug(print_r($month, true));
                    $age2 = mb_convert_kana($value2['age'], 'n');
                    $age2 = preg_replace('/\D/', '', $age2);
                    \Log::debug(print_r($value2['month'], true));
                    \Log::debug(print_r($value2['month'] == $value['month'], true));
                    if($age === $age2 && $value2['month'] == $value['month']){
                        $results[$age][$key2]['count'] += intval($value2['count']);
                    }
                }
            }else{
                array_push($results[$age], $value);
            }
            */
            array_push($results[$age], $value);
        }
        // $results = arrayToMultiColumn($results, 'month', 'count');

        return $results;
    }

    public function analysisContactDetailSum($query_string = [], $column = '', $list = [])
    {
        $display_type = $query_string['display'] === 'yearmonth' ? 'month' : 'year';
        // サブクエリで目的リストカラムを含むテーブルを先に作成
        $sub_table_query = DB::table('contacts')->select('contacts.id', 'contacts.created_at');
        foreach ($list->values as $key => $value) {
            // 雑草対策が含まれるレコード数
            $sub_table_query->addSelect(DB::raw("SUM(CASE 
                WHEN $column LIKE '%$value%' THEN 1
                ELSE 0
            END) as records_with_$key"));
        }
        /*====== records_with_$keyについて ======
        records_with_0 => 雑草対策
        records_with_1 => ペット・ドッグラン用
        records_with_2 => スポーツ 
        ... etc といった具合にconfigテーブルのkey=6 人工芝の使用用途のvalueを配列にしたときのkeyで集計しています。
        (日本語だと列名に使えないため、keyで指定しています)
        */
        // filterOfCreatdAtでクエリ位パラメーターによって月でソートするか年でソートするかコントロールしています。
        $sub_table_query = $this->filterOfCreatedAt($sub_table_query, $query_string);
        $sub_table = $sub_table_query->where('status', 1)
            ->whereNotNull($column)
            ->whereNot($column, '')
            ->orderBy('created_at', 'ASC');
        $contacts_query = DB::table($sub_table)->select('*', $display_type);
        foreach ($list->values as $key => $value) {
            $contacts_query->groupBy("records_with_$key");
        }
        $contacts = $contacts_query->orderBy('created_at')->get()->toArray();
        $contacts = json_decode(json_encode($contacts), true);
        \Log::debug(print_r($contacts, true));

        $results = [];
        // ここから表示用に整形
        foreach ($list->values as $key => $value) {
            $results[$value] = [];
        }
        /*======$contactsの中身は========
         [0] => Array
        (
            [id] => 31928
            [created_at] => 2022-05-01 04:42:30
            [records_with_0] => 123
            [records_with_1] => 32
            [records_with_2] => 11
            [records_with_3] => 9
            [records_with_4] => 4
            [records_with_5] => 101
            [records_with_6] => 1
            [month] => 2022-05
        )
        [1] => Array
        (
            [id] => 32250
            [created_at] => 2022-06-01 09:05:01
            [records_with_0] => 81
            [records_with_1] => 21
            [records_with_2] => 11
            [records_with_3] => 4
            [records_with_4] => 8
            [records_with_5] => 71
            [records_with_6] => 2
            [month] => 2022-06
        )
    月表示の例 
        $results[検索][0]['month'] = '2022-05';
        $results[検索][0]['count'] = 5;
        のような配列に整形
        */
        foreach ($contacts as $key => $contacts_value) {
            // 項目（雑草対策、ペット・ドッグラン用、スポーツ、etc）の数だけループ
            foreach ($list->values as $list_key => $list_value) {
                // ここで$results[雑草対策][0]['month'] = '2022-05'の値を入れる;
                $insert_array = [
                    'count' => $contacts_value["records_with_$list_key"],
                    'month' => $contacts_value[$display_type],
                    'purpose' => $list_value
                ];
                array_push($results[$list_value], $insert_array);
            }
        }

        return $results;
    }

    private function filterOfCreatedAt($query, $query_string)
    {
        if ($query_string['display'] === 'yearmonth') {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y-%m") AS month'))->groupBy('month')->orderBy('month', 'ASC');
            if (!empty($query_string['start'])) {
                $query->where('contacts.created_at', '>=', $query_string['start'] . '-01 00:00:00');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['end'] . '-31 23:59:59');
            }
        } else {
            $query->addSelect(DB::raw('DATE_FORMAT(contacts.created_at, "%Y") AS year'))->groupBy('year')->orderBy('year', 'ASC');
            if (!empty($query_string['startyear'])) {
                $query->where('contacts.created_at', '>=', $query_string['startyear'] . '-01-01 00:00:00');
            }
            if (!empty($query_string['end'])) {
                $query->where('contacts.created_at', '<=', $query_string['endyear'] . '-12-31 23:59:59');
            }
        }

        return $query;
    }
    // ここまで案件問い合わせ詳細分析ページ用
}
