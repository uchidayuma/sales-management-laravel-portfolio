@component('mail::message')
@include('emails.return-message')

<h1>サンプルFCから商品発送情報変更のご連絡</h1>
<p>この度は、当社の人工芝をご発注いただき、誠にありがとうございます。</p>
<p>先日ご連絡いたしました商品発送情報が、都合により変更となっております。</p>
<p>お手数ではございますが、下記ボタンリンクより最新の発送情報をご確認くださいますようお願いいたします。</p>

<h3><span style="display:inline-block;width:70px;">顧客No</span>：{{ $customer['id']}} {{ $customer['surname'].$customer['name'] }} {{ isCompany($customer) ? '御中' : '様'}}</h3>
<p><span style="display:inline-block;width:70px;">発送日</span>：{{ date('Y年m月d日', strtotime($customer['shipping_date'])) }}</p>
<p><span style="display:inline-block;width:70px;">運送会社</span>：{{ $customer['transport_company'] }}</p>
<p><span style="display:inline-block;width:70px;">追跡番号</span>：{{ $customer['shipping_number'] }}</p>
<p><span style="display:inline-block;width:70px;">見積書No</span>： {{ $customer['quotation_id'] }}</p>

@component('mail::button', ['url' => $customer['url']])
荷物を追跡
@endcomponent

@include('emails.return-message')
@endcomponent
