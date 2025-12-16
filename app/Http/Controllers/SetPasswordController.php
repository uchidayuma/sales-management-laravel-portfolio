<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\User;
use App\Models\PasswordReset;

class SetPasswordController extends MyController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPasswordResetForm($token)
    {
        $tokenData = PasswordReset::where('token', $token)->first();
        //dd($tokenData);

        if (!$tokenData) {
            return redirect()->to('home');
        }

        return view('fc.set-password', compact('token'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        //validation
        $data = request()->validate([
            'password' => 'required|between:8,255|confirmed',
        ]);

        $inputs = $request->all();
        //dd($inputs['token']);
        $token = $inputs['token'];

        $password = $request->password;
        $password_confirm = $request->password_confirm;

        $tokenData = PasswordReset::where('token', $token)->first();
        //dd($tokenData);

        $user = User::where('email', $tokenData->email)->first();
        if (!$user) {
            return redirect()->to('/');
        }
        //dd($user);

        $user->password = Hash::make($password);
        $user->update();

        //ログイン画面へ移行
        return redirect('/');
        //FCを自動でログインさせるなら、以下のコードを使う
        //return Auth::login($user);
    }
}
