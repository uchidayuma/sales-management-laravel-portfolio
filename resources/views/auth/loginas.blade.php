@extends('layouts.general-layout')

@section('css')
<link href="{{ asset('styles/auth/login.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="d-flex justify-content-center">
    <form method="POST" action="{{ route('admin.loginasuserid') }}" class="login-area-form py-3 px-5 rounded">
        @csrf
        <!-- ログインbutton -->
        <input class='mb20' type='number' name='user_id' value />
        <div class="text-center">
            <button type="submit" id='submit' class="btn btn-primary w-100">
                {{ __('ログイン') }}
            </button>
        </div>

    </form><!-- login-area-form -->
</div>
@endsection
