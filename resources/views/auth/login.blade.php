@extends('layouts.general-layout')

@section('css')
<link href="{{ asset('styles/auth/login.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="d-sm-flex justify-content-sm-center">
    <div>
        <form method="POST" action="{{ route('login') }}" class="login-area-form py-3 px-0 px-sm-5 rounded">
            
            @csrf
            <!-- logo画像 -->
            <div class="login-area-form__logo p-3 text-center">
                <img class="login-area-form__logo-img" src="{{ asset('images/logo.jpg') }}" alt="logo"/>
            </div>
            <!-- ID入力 -->
            <div class="login-area-form__input p-2 m-2 rounded">
                <i class="far fa-user login-area-form__input-icon mr-1 mr-sm-3 p-2 rounded d-none d-sm-inline"></i>
                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="ID" dusk="login-mail">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>
            <!-- Password入力 -->
            <div class="login-area-form__input p-2 m-2 rounded">
                <i class="fas fa-lock login-area-form__input-icon mr-1 mr-sm-3 p-2 rounded d-none d-sm-inline"></i>
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password" dusk="login-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>
            <!-- ログイン情報を保持するかのチェックボックス -->
            <div class="text-right mr-2">
                <div class="form-check my-3 my-sm-0">
                    <input class="form-check-input width-button scale_1-5" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <p class="form-check-label mr-4 pr-2 mr-sm-3" for="remember">
                        {{ __('ログイン情報を保持') }}
                    </p>
                </div>
            </div>
            <!-- ログインbutton -->
            <div class="text-center">
                <button type="submit" id='submit' class="btn btn-primary w95per">
                    {{ __('ログイン') }}
                </button>
            </div>

        </form><!-- login-area-form -->
        
        <div class="password-fix rounded my-3">
            <i class="fas fa-caret-right pl-3 rounded"></i>
            @if (Route::has('password.request'))
                <a class="password-fix__a" href="{{ route('password.request') }}">
                    {{ __('パスワードが分からない') }}
                </a>
            @endif
        </div>
       
    </div>
</div>
@endsection
