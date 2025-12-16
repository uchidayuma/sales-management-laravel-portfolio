<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function loginAs(){
        if(isFc()){
            return redirect('/');
        }
        return view('auth.loginas');
    }

    public function loginAsUserId(Request $request){
        Auth::loginUsingId($request->input('user_id') ,true);

        $user = Auth::user();
        return redirect('/')->with('success', $user->company_name.'としてログインしました。');
    }
}
