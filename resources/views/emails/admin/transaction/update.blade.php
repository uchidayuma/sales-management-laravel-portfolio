@component('mail::message')

<h1>FCが発注の内容を変更しました。</h1>
<p>商品の準備をする前に必ず内容をご確認ください。</p>

<ul>
<li>発注書No： {{ $posts['t']['id']}}</li>
@component('mail::button', ['url' => route('transactions.show', ['id' => $posts['t']['id']] ) ])
発注書を確認
@endcomponent
<li>発注書を変更したFC： {{$posts['user']['name']}}</li>
</ul>

@endcomponent
