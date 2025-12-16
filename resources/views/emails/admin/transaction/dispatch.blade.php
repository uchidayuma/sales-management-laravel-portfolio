@component('mail::message')
@include('emails.return-message-fc')
<h1>FC本部より出荷手配完了のご連絡です。</h1>

<p>システムからご確認をお願いいたします。</p>

<p><span style="display:inline-block;width:85px;">発注書番号</span>：{{ !empty($transaction['id']) ? $transaction['transaction_id'] .'　案件No.'.(displayContactId($transaction)).'　'.(customerName($transaction)).'　'.(isCompany($transaction) ? '御中' : '様邸') : $transaction['transaction_id'].'　資材発注'}}分</p> 
@if($transaction['shipping_date2'] || $transaction['shipping_date3'])
  <p>*この発送は<span style='color: #d9534f; font-weight:bold;'>{{$transaction['number']}}つ目</span>の発送です。</p>
@endif
@switch($transaction['number'])
    @case('1')
      <p><span style="display:inline-block;width:85px;">発送日</span>：{{ date('Y年m月d日', strtotime(!is_null($transaction['transaction_only_shipping_date']) ? $transaction['transaction_only_shipping_date'] : $transaction['shipping_date'])) }}</p>
      <p><span style="display:inline-block;width:85px;">納品希望日</span>：{{ date('Y年m月d日', strtotime($transaction['delivery_at'])) }}</p>
      <p><span style="display:inline-block;width:85px;">運送会社</span>：{{ $transaction['transport_company'] }}</p>
      <p><span style="display:inline-block;width:85px;">追跡番号</span>：{{ is_null($transaction['shipping_number']) ? $transaction['transaction_only_shipping_number'] : $transaction['shipping_number']  }}</p>
      <p><span style="display:inline-block;width:85px;font-weight: bold;">連絡事項</span><br>{!! nl2br($transaction['dispatch_message']) !!}</p>
        @break
    @case('2')
      <p><span style="display:inline-block;width:85px;">発送日</span>：{{ date('Y年m月d日', strtotime($transaction['shipping_date2'])) }}</p>
      <p><span style="display:inline-block;width:85px;">納品希望日</span>：{{ !empty($transaction['delivery_at2']) ? date('Y年m月d日', strtotime($transaction['delivery_at2'])) : '' }}</p>
      <p><span style="display:inline-block;width:85px;">運送会社</span>：{{ $transaction['transport_company'] }}</p>
      <p><span style="display:inline-block;width:85px;">追跡番号</span>：{{ $transaction['shipping_number2']  }}</p>
      <p><span style="display:inline-block;width:85px;font-weight: bold;">連絡事項</span><br>{!! nl2br($transaction['dispatch_message2']) !!}</p>
        @break
    @case('3')
      <p><span style="display:inline-block;width:85px;">発送日</span>：{{ date('Y年m月d日', strtotime($transaction['shipping_date3'])) }}</p>
      <p><span style="display:inline-block;width:85px;">納品希望日</span>：{{ !empty($transaction['delivery_at3']) ? date('Y年m月d日', strtotime($transaction['delivery_at3'])) : '' }}</p>
      <p><span style="display:inline-block;width:85px;">運送会社</span>：{{ $transaction['transport_company'] }}</p>
      <p><span style="display:inline-block;width:85px;">追跡番号</span>：{{ $transaction['shipping_number3']  }}</p>
      <p><span style="display:inline-block;width:85px;font-weight: bold;">連絡事項</span><br>{!! nl2br($transaction['dispatch_message3']) !!}</p>
        @break
    @default
      <p><span style="display:inline-block;width:85px;">発送日</span>：{{ date('Y年m月d日', strtotime(!is_null($transaction['transaction_only_shipping_date']) ? $transaction['transaction_only_shipping_date'] : $transaction['shipping_date'])) }}</p>
      <p><span style="display:inline-block;width:85px;">納品希望日</span>：{{ date('Y年m月d日', strtotime($transaction['delivery_at'])) }}</p>
      <p><span style="display:inline-block;width:85px;">運送会社</span>：{{ $transaction['transport_company'] }}</p>
      <p><span style="display:inline-block;width:85px;">追跡番号</span>：{{ is_null($transaction['shipping_number']) ? $transaction['transaction_only_shipping_number'] : $transaction['shipping_number']  }}</p>
      <p><span style="display:inline-block;width:85px;font-weight: bold;">連絡事項</span><br>{!! nl2br($transaction['dispatch_message']) !!}</p>
@endswitch


<ul>
@component('mail::button', ['url' => route('transactions.show', ['id' => $transaction['transaction_id']] ) ])
発注書を確認
@endcomponent
@component('mail::button', ['url' => $transaction['url']])
荷物を追跡
@endcomponent
</ul>

@component('mail::button', ['url' => route('dispatched.list')] )
発送済み資材リストを確認
@endcomponent

@endcomponent
