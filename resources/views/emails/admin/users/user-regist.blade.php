@component('mail::message')
# 登録を完了するために、パスワードの設定を行ってください。

@component('mail::button', ['url' => route('fc.password.reset', ['token' => $token]) ])
パスワードを設定する
@endcomponent

<strong>
{{ $inputs['fc']['name'] }}さん、<br>
サンプルFCオペレーションシステムへようこそ!

</strong>
@include('emails.return-message-fc')
@endcomponent
