@extends('layouts.general-layout')

@section('css')
<link href="{{ asset('styles/account/registration.min.css') }}" rel="stylesheet"> 
@endsection

@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">パスワード変更</div>

        @if (session('change_password_error'))
          <div class="container mt-2">
            <div class="alert alert-danger">
              {{session('change_password_error')}}
            </div>
          </div>
        @endif

        <div class="card-body">
          <form method="POST" action="{{ route('users.changepassword', ['id' => $fc->id]) }}">
            @csrf
            <div class="form-group">
              <label for="current">
                現在のパスワード
              </label>
              <div>
                <input id="current" type="password" class="form-control" name="current-password" dusk='now_pass' required autofocus>
              </div>
            </div>
            <div class="form-group">
              <label for="password">
                新しいのパスワード（8〜255文字の文字列）
              </label>
              <div>
                <input id="password" type="password" class="form-control" name="new-password" dusk='new_pass1' required>
                @if ($errors->has('new-password'))
                  <span class="help-block">
                    <strong>{{ $errors->first('new-password') }}</strong>
                  </span>
                @endif
              </div>
            </div>
            <div class="form-group">
              <label for="confirm">
                新しいのパスワード（確認用）
              </label>
              <div>
                <input id="confirm" type="password" class="form-control" name="new-password_confirmation" dusk='new_pass2' required>
              </div>
            </div>
            <div>
              <button type="submit" class="btn btn-primary" dusk='submit'>変更</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection