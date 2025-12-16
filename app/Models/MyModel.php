<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MyModel extends Model
{
    const STEP_ASSIGN = 1;
    const STEP_APPOINT = 2;
    const STEP_ONSITE_CONFIRM = 3;
    const STEP_QUOTATION = 4;
    const STEP_RESULT = 5;
    const STEP_TRANSACTION = 6;
    const STEP_SHIPPING_COST_INPUT = 7;
    const STEP_FC_PAYMENT = 8;
    const STEP_SHIPPING = 9;
    const STEP_COMPLETE = 10;
    const STEP_REPORT_COMPLETE = 11;
    const STEP_CANCELATION = 99;
    const STEP_PAST_CUSTOMER = 100;

    public function findById($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    public function results($where = null, $sort = null, $order = null, $limit = null, $offset = null)
    {
        $limit = $limit ? $limit : 10000;
        $offset = $offset ? $offset : 0;
        $sort = $sort ? $sort : 'id';
        $order = $order ? $order : 'DESC';

        $results = DB::table($this->table)
            ->offset($offset)
            ->limit($limit)
            ->orderBy($sort, $order)
            ->when($where, function ($query) use ($where) {
                return $query->where($where);
            })
            ->get();

        return $results;
    }

    public function getField($where, $column)
    {
        $array = DB::table($this->table)->where($where)->get();

        if (empty($array[0])) {
            return false;
        }

        return $array[0]->$column;
    }

    public function transStart()
    {
        return \DB::beginTransaction();
    }

    public function transCommit()
    {
        return \DB::commit();
    }

    public function transRollback()
    {
        return \DB::rollback();
    }
}
