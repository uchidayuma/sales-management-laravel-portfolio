@component('mail::message')
@include('emails.return-message')

<h1>サンプルFCから商品発送のご連絡</h1>

<p>この度は、当社の人工芝をご発注いただき、誠にありがとうございます。</p>
<p>ご入金を確認させていただきました。</p>
<br>

<p>ご注文いただきました商品の発送手配が以下の通り完了しましたので、ご案内いたします。</p>
<p>お荷物の追跡は発送日 午後よりご確認いただけます。</p>
<p>お受け取りにあたり、ご注意いただきたい点がございますのでご確認ください。</p>
<hr>
<p>［人工芝商品をご注文のお客様へ］ </p>
<p>・大型商品のため到着日時のご指定が出来かねる場合がございます。</p>
<p>・配達業者よりお届け日相談のご連絡が入る場合もございますので、ご対応の程宜しくお願いいたします。</p>
<p>・人工芝やパターマットは、商品到着後速やかに広げていただきますようお願いいたします。</p>
<p>　梱包したままの状態で長時間保管されますと、巻癖が取れない（パターマットの場合はしわがよる）等の原因になりますので、十分にご注意くださいませ。</p>
<hr>
<br>
<p>ご不明な点等ございましたら、返信用メールアドレスまたはフリーダイヤル：0120-48-1148まで、お気軽にお問い合わせください。</p>

<p>今後ともサンプルFCをご愛顧賜りますようお願い申し上げます。</p><br>



<h3><span style="display:inline-block;width:70px;">顧客No</span>：{{ $customer['id']}} {{ isCompany($customer) ? $customer['company_name'] . ' ' . '御中' : $customer['surname'].$customer['name'] . ' ' . '様' }}</h3>
<p><span style="display:inline-block;width:70px;">発送日</span>：{{ date('Y年m月d日', strtotime($customer['shipping_date'])) }}</p>
<p><span style="display:inline-block;width:70px;">運送会社</span>：{{ $customer['transport_company'] }}</p>
<p><span style="display:inline-block;width:70px;">追跡番号</span>：{{ $customer['shipping_number'] }}</p>
<p><span style="display:inline-block;width:70px;">見積書No</span>： {{ $customer['quotation_id'] }}</p>

<!-- この部分は、顧客へ資材のみの発送のためTransctionテーブルにレコードが作成されないため dispatch_message のデータが存在しないので 表示されない
ただ、発送連絡に顧客への連絡事項を入力する場所があるため、連絡事項欄をそももそも消すか、contactテーブルに新たにカラムを作る必要があるかもです。 -->
<p><span style="display:inline-block;width:85px;font-weight: bold;">連絡事項</span><br>{{$customer['dispatch_message']}}</p>

@component('mail::button', ['url' => $customer['url']])
荷物を追跡
@endcomponent

@include('emails.return-message')
@endcomponent
