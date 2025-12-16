@component('mail::message')
@include('emails.return-message-fc')
<h1>発注書の内容が変更されました。</h1>
<p>以下のボタンリンクから内容をご確認ください。</p>

<ul>
<li>発注書No： {{ $posts['t']['id']}}</li>
@component('mail::button', ['url' => route('transactions.show', ['id' => $posts['t']['id']] ) ])
発注書を確認
@endcomponent
</ul>

@endcomponent
