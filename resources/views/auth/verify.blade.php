@extends('layouts.general-layout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('メールアドレスの認証を行ってください。') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('メールアドレス認証の為のリンクが送信されました。') }}
                        </div>
                    @endif

                    {{ __('次に進む前に、メールを確認してください。') }}
                    {{ __('もしメールが受信されていなければ') }}, <a href="{{ route('verification.resend') }}">{{ __('こちらのリンクよりメールの再送を行ってください。') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
