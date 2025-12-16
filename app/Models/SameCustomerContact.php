<?php

namespace App\Models;

use Illuminate\Support\Arr;
use App\Models\Contact;

class SameCustomerContact extends MyModel
{
    protected $table = 'same_customer_contacts';

    public function contactInsert($refId, $args)
    {
        foreach ($args as $key => $val) {
            $this->insert([
                ['ref_contact_id' => $refId, 'contact_id' => $val['id']],
                ['ref_contact_id' => $val['id'], 'contact_id' => $refId],
            ]);
        }
    }

    public function contactUpdate($refId, $args)
    {
        $sameIds = explode(',', $args);
        $pastResults = $this::where('ref_contact_id', $refId)->get('contact_id');
        $pastSames = [];
        foreach ($pastResults as $same) {
            $pastSames[] = $same['contact_id'];
        }
        // 紐付けを解除された案件IDを抽出
        $deleteSames = array_diff($pastSames, $sameIds);
        // 解除が20件以上なら処理中断
        // dd($deleteSames);
        $addSames = array_diff($sameIds, $pastSames);
        if (count($deleteSames) > 20 || count($addSames) > 20) {
            throw new \Exception("同一顧客の変更は20件以内にしてください");
        }
        // dd($addSames);
        $this->transStart();
        try {
            $contact_model = new Contact();
            $contact_model->where('id', $refId)->update(['main_user_id' => null]);
            // 紐付けを外されたものは削除　→　変更した案件と紐づいている側もレコードを削除
            foreach ($deleteSames as $delete) {
                $this::where('ref_contact_id', $delete)->where('contact_id', $refId)->delete();
                $contact_model->where('id', $delete)->update(['main_user_id' => null]);
            }
            // いったん自分がRefIdになっているレコードを削除
            $this::where('ref_contact_id', $refId)->delete();
            // 全削除でなければ再インサート
            if (!$sameIds[0] == '') {
                foreach ($sameIds as $val) {
                    $this::insert(['ref_contact_id' => $refId, 'contact_id' => $val]);
                }
                foreach ($addSames as $val) {
                    // 紐付けられた側のレコードも追加
                    $this::insert(['ref_contact_id' => $val, 'contact_id' => $refId]);
                }
            }
            $this->transCommit();
        } catch (Exception $e) {
            \Log::debug(print_r($e->getMessage(), true));
            $this->transRollback();
        }
    }

    // 同一顧客をまとめる関数
    public function summarizeSameCostomerIds($contact_id, $start = null, $end = null)
    {
        $query = $this->query();
        $query->select('contact_id')->where('ref_contact_id', $contact_id)->leftJoin('contacts as c', 'c.id', '=', 'same_customer_contacts.contact_id');
        if (!empty($start) && !empty($end)) {
            $query->whereBetween('c.created_at', [$start, $end]);
        }
        $same_customers = $query->get();
        $return_array = [];
        foreach ($same_customers as $sc) {
            array_push($return_array, $sc['contact_id']);
        }
        return $return_array;
    }

    // アクティブな同一顧客いるか？
    // 引数＝contacts.id
    // 返り値 boolean
    public function activeSameCustomer($id)
    {
        return $this->where(function ($query) use ($id) {
            $query->orWhere('contact_id', $id)->orWhere('ref_contact_id', $id);
        })
            ->where('c.status', 1)->where('c.step_id', '<', self::STEP_COMPLETE)
            ->whereNotNull('c.user_id')
            ->join('contacts as c', 'contact_id', '=', 'c.id')
            ->exists();
    }
}
