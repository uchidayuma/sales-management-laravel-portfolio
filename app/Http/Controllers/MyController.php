<?php

namespace App\Http\Controllers;

use Auth;

class MyController extends Controller
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

    public function __construct()
    {
        $this->breadcrumbs = new \App\Services\Breadcrumbs();
        $this->breadcrumbs->setDivider('>');

        //全コントローラーに現ログインユーザー情報を渡す
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }
}
