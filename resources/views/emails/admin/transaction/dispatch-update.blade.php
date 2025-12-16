@component('mail::message')
@include('emails.return-message-fc')
<h1>FC本部から資材発送情報変更の連絡が届きました。</h1>
@if(!empty($transaction['delivery_at2']) || !empty($transaction['delivery_at3']))
<p><span style='color: #d9534f; font-weight:bold;'>{{$transaction['number']}}</span>番目の発送情報が変更されました。</p>
@endif
<p>変更された情報は下記ボタンからシステムでご確認ください。</p>
<ul>
<li>発注書No： {{ $transaction['transaction_id']}}</li>
@component('mail::button', ['url' => route('transactions.show', ['id' => $transaction['transaction_id']] ) ])
発注書を確認
@endcomponent
@component('mail::button', ['url' => $transaction['url']])
荷物を追跡
@endcomponent
</ul>

<p>{{$transaction['dispatch_message']}}</p>
@component('mail::button', ['url' => route('dispatched.list')] )
発送済み資材リストを確認
@endcomponent

@endcomponent
