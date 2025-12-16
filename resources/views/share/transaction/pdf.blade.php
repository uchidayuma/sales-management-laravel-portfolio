@php
    ob_start();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>発注書ダウンロード</title>

    <!-- Styles -->
    <link href="{{ asset('styles/fontawesome/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/layout.min.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/common.min.css') }}" rel="stylesheet">
    <style type="text/css">
      @font-face {
        font-family: ipag;
        font-style: normal;
        font-weight: normal;
        src: url('{{ storage_path('fonts/ipag.ttf') }}') format('truetype');
      }
      @font-face {
        font-family: ipag;
        font-style: bold;
        font-weight: bold;
        src: url('{{ storage_path('fonts/ipagp.ttf') }}') format('truetype');
      }
      body {
        font-family: ipag !important;
        background-image: none;
        font-size : 13px;
        margin: 0;
        padding: 0;
      }
      p {
        font-family: ipag !important;
        background-image: none;
        /* font-size : 12px; */
        font-weight: 400;
      }
      h4 {
        font-family: ipag !important;
        background-image: none;
      }
      table {
        font-size: 13px;
      }
      .thick-border{
        border: solid 3px #333; 
        border-collapse: collapse;
      }
      .thick-border__td{
        padding : 0.5rem 0.5rem;
        border: solid 1px #333; 
        font-weight: 400;
        word-wrap: break-word;
      }
      .thick-border__th{
        padding : 0.5rem 0.5rem;
        border: solid 1px #333;
        font-style: bold;
        font-weight: bold;
        background-color: #ccc;
        word-wrap: break-word;
      }
      .memo-heading{
        font-family: ipag !important;
        background-image: none;
        font-weight: bold;
        margin-bottom: 10px;
      }
      .company-wrapper{
        position: relative;
        overflow: visible;
      }
      .company-wrapper__seal{
        max-width: 80px;
        min-width: 70px;
        position: absolute;
        top: 10%;
        right: 30%;
      }
      .shiba-logo{
        position: absolute;
        top : 0;
        right : 0;
        width : 70px;
        height : 70px;
      }
      .pl30{
        padding-left: 30px;
      }
      .company-wrapper__info{
        margin: 0;
      }
      .memo {
        max-width: 100%;
        overflow-wrap: break-word;
      }
      .break-word{
        word-break: break-word;
      }
      .page-number:after { content: counter(page); }
      .transport-info {
        display: inline-block;
        width: 30%;
        margin-right: 2%;
        text-align: center;
        vertical-align: middle;
      }
    </style>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery/jquery-3.4.1.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/sidebar.js') }}" defer></script>
  </head>

  <body>
    {{-- <div class="p-3"> --}}
    <img class="shiba-logo" src="{{ asset('images/logo-r.jpg') }}" alt="サンプルFCロゴ">

    <section class='mb-2'>
      <h4 class='h4 bold mb-5'>発注書No.{{$id}}</h4>
    @if(!empty($transactions[0]['transaction_only_shipping_date']))
      <div class='transport-info'>
        <table class="table table-bordered">
          <tr>
            <th class=''>{{ !empty($transactions[0]['shipping_date2']) ? '出荷日①' : '出荷日'}}</th>
            <td class="thick-border__td">{{date('Y年m月d日', strtotime($transactions[0]['transaction_only_shipping_date']))}}</td>
          </tr>
          <tr>
            <th class=''>配送業者</th>
            <td class="thick-border__td">{{ returnTransportCompany($transactions[0]['transaction_only_shipping_id']) }}</td>
          </tr>
          <tr>
            <th class=''>追跡番号</th>
            <td class="thick-border__td break-word">{{ $transactions[0]['transaction_only_shipping_number'] }}</td>
          </tr>
        </table>
      </div>
    @endif
    @if(!empty($transactions[0]['shipping_date2']))
      <div class='transport-info'>
        <table class="table table-bordered">
          <tr>
            <th class=''>出荷日②</th>
            <td class="thick-border__td">{{date('Y年m月d日', strtotime($transactions[0]['shipping_date2']))}}</td>
          </tr>
          <tr>
            <th class=''>配送業者</th>
            <td class="thick-border__td">{{ returnTransportCompany($transactions[0]['shipping_id2']) }}</td>
          </tr>
          <tr>
            <th class=''>追跡番号</th>
            <td class="thick-border__td break-word">{{ $transactions[0]['shipping_number2'] }}</td>
          </tr>
        </table>
      </div>
    @endif
    @if(!empty($transactions[0]['shipping_date3']))
      <div class='transport-info'>
        <table class="table table-bordered">
          <tr>
            <th class=''>出荷日③</th>
            <td class="thick-border__td">{{date('Y年m月d日', strtotime($transactions[0]['shipping_date3']))}}</td>
          </tr>
          <tr>
            <th class=''>配送業者</th>
            <td class="thick-border__td">{{ returnTransportCompany($transactions[0]['shipping_id3']) }}</td>
          </tr>
          <tr>
            <th class=''>追跡番号</th>
            <td class="thick-border__td break-word">{{ $transactions[0]['shipping_number3'] }}</td>
          </tr>
        </table>
      </div>
    @endif
    </section>
    <div class='w100per mb-2'>
      <div class='w70 d-inline-block align-top'>
        <table class="table table-bordered mw-100" style="table-layout: fixed;">
      @if(!empty($transactions[0]['contact_id']))
          <tr>
            <th class='w40'>工事名称</th>
            <td class="thick-border__td">案件No.{{ empty($transactions[0]['own_contact']) ? '' : $transactions[0]['user_id'].'-' }}{{$transactions[0]['contact_id']}} &nbsp; {{ customerName($transactions[0]) }} &nbsp; 様<br>{{$transactions[0]['pref'].$transactions[0]['city'].$transactions[0]['street']}} </td>
          </tr>
      @endif
          <tr>
            <th class='w40'>受け取り場所<br>
        @if (!empty($transactions[0]['direct_shipping']))
              <p class="text-danger small">※この資材は直接顧客に発送されます</p>
        @endif
            </th>
            <td class="thick-border__td">{{ $transactions[0]['address']}}</td>
          </tr>
          <tr>
            <th class='w40'>受け取り人</th>
            <td class="thick-border__td">{{ $transactions[0]['consignee']}}</td>
          </tr>
          <tr>
      @if(!empty($transactions[0]['contact_id']))
            <th class='w40'>荷受け人様連絡先TEL </th><td>{{ $transactions[0]['transaction_tel'] }}</td>
      @else
            <th class='w40'>FC電話番号 </th><td>{{ $userdata[0]['tel'] }}</td>
      @endif
          </tr>
          <tr>
            <th class='w40'>納品希望日</th>
            <td class="thick-border__td">
              {{date('Y年m月d日', strtotime($transactions[0]['delivery_at']))}}
            </td>
          </tr>
  @if(!empty($transactions[0]['delivery_at2']))
          <tr>
            <th class='w40'>第2納品希望日</th>
            <td class="thick-border__td">{{date('Y年m月d日', strtotime($transactions[0]['delivery_at2']))}}</td>
          </tr>
  @endif
  @if(!empty($transactions[0]['delivery_at3']))
          <tr>
            <th class='w40'>第3納品希望日</th>
            <td class="thick-border__td">{{date('Y年m月d日', strtotime($transactions[0]['delivery_at3']))}}</td>
          </tr>
  @endif
          <tr class=''>
            <th>その他備考</th><td class='apply-new-line thick-border__td memo'>{!! $transactions[0]['transaction_memo'] !!}</td>
          </tr>
        </table>

        <table class="table table-bordered w70">
          <thead>
            <tr>
              <th scope="col" class="">小計(円)</td>
              <th scope="col" class="">消費税(円)</td>
              <th scope="col" class="">合計金額(円)</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="thick-border__td" id="subTotal">{{ number_format($sub_total) }}</td>
              <td class="thick-border__td" id='tax'>{{ number_format($tax) }}</td>
              <td class="thick-border__td" id='total' class="total">{{ number_format($total_price) }}</td>
            </tr>
          </tbody>
        </table>
      </div><!-- w55per -->
      <div class='company-wrapper w20 d-inline-block align-top ml-5'>
      @if(isset($id))
        <p class='company-wrapper__info mb10'>発注書番号： {{$id}}</p>
      @endif
        <p class='company-wrapper__info mb10'>発注日： {{ date('Y年m月d日', strtotime($transactions[0]['transaction_time'])) }}</p>
        <h4 class='h4 bold'>{{ $transactions[0]['fc_name'] }}</h4>

        <!-- FCの住所を表示 -->
        <p class='company-wrapper__info'>〒{{ $userdata[0]->zipcode }}</p>
        <p class='company-wrapper__info'>{{ $userdata[0]->pref }}{{ $userdata[0]->city }}{{ $userdata[0]->street }}
      </div><!-- w20per -->
    </div>

    <table class="thick-border w100per" id="quotationTable" style="width: 100%">
      <tr>
        <th class="thick-border__th text-center w35per">詳細</th>
        <th class="thick-border__th text-center w10per">数量</th>
        <th class="thick-border__th text-center w10per">単位</th>
        <th class="thick-border__th text-center w10per">単価(円)</th>
        <th class="thick-border__th text-center w10per">金額(円)</th>
      </tr>
@foreach($transactions AS $key => $rc)
      <!-- 手動入力注文なら -->
  @if(empty($rc['product_id']) && $rc['cut']=='0')
      <tr>
        <td class="thick-border__td">{{ $rc['other_product_name'] }}</td>
        <td class="thick-border__td text-right">{{$rc['num']}}</td>
        <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
        <td class="thick-border__td text-right">{{ floatNumberFormat($rc['unit_price'] * $rc['num']) }}</td>
      </tr>
      <!-- 反物売りだった場合 -->
  @elseif($rc['cut'] ==  '0')
      <tr>
        <td class="thick-border__td">{{ $rc['product_name'] }}{{productArea($products,$rc['product_id'])}}</td>
        <td class="thick-border__td text-right">{{$rc['num']}}</td>
        <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price'] * $rc['num']) }}</td>
      </tr>
    @if(!empty($rc['turf_cuts'][0]))
      @foreach($rc['turf_cuts'] as $cut)
      <tr>
        <td class="thick-border__td pl-4">{{productsFilterById($products, $cut['product_id'])['name']}}</td>
        <td class="thick-border__td text-right">{{$cut['num']}}</td>
        <td class="thick-border__td text-center">反分</td>
        <td class="thick-border__td text-right">{{ !empty($cut['unit_price']) ? number_format($cut['unit_price']) : productsFilterById($products, $cut['product_id'])['fc_price']}}</td>
        <td class="thick-border__td text-right">{{ number_format(!empty($cut['unit_price']) ? intval($cut['unit_price']) * floatNumberFormat($cut['num']) : intval(productsFilterById($products, $cut['product_id'])['fc_price']) * floatNumberFormat($cut['num'])) }}</td>
      </tr>
      @endforeach
    @endif
      <!-- 人工芝の切り売りならば -->
  @elseif($rc['cut'] ==  '1' && $rc['product_type_id'] == '1')
      <tr>
        <td class="thick-border__td">{{ $rc['product_name'] . '（' . floatNumberFormat($rc['horizontal'], 2). 'm ×' . floatNumberFormat($rc['vertical'], 2) . 'm）' . (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num']) }}枚</td>
        <td class="thick-border__td text-right">{{ floatNumberFormat($rc['num'] * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}</td>
        <td class="thick-border__td text-center">㎡</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price'] * floatNumberFormat($rc['num']) * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}</td>
      </tr>
    @if(!empty($rc['turf_cuts'][0]))
      @foreach($rc['turf_cuts'] as $cut)
      <tr>
        <td class="thick-border__td pl-4">{{ productsFilterById($products, $cut['product_id'])['name'] }}</td>
        <td class="thick-border__td text-right">{{ empty($cut['cut_set_num']) ? $cut['num'] : $cut['num'] * $cut['cut_set_num'] }}</td>
        <td class="thick-border__td text-center">{{ productsFilterById($products, $cut['product_id'])['unit'] }}</td>
      @if( !array_key_exists('unit_price', $cut) )
        <td class="thick-border__td text-right">{{ number_format($products[$cut['product_id'] - 1]['fc_price']) }}</td>
        @if( empty($cut['cut_set_num']) )
          <td class="thick-border__td text-right">{{ number_format($products[$cut['product_id'] - 1]['fc_price'] * $cut['num'] ) }}</td>
        @else
          <td class="thick-border__td text-right">{{ number_format($products[$cut['product_id'] - 1]['fc_price'] * $cut['num'] * $cuts['cut_set_num'] ) }}</td>
        @endif
      @else
        <td class="thick-border__td text-right">{{ number_format($cut['unit_price']) }}</td>
        @if( empty($cut['cut_set_num']) )
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price'] * $cut['num'] * 1 ) }}</td>
        @else
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price'] * $cut['num'] * $cut['cut_set_num'] ) }}</td>
        @endif
      @endif
        {{-- <td class="thick-border__td text-right">{{ !empty($cut['unit_price']) ? number_format($cut['unit_price']) : productsFilterById($products, $cut['product_id'])['fc_price']}}</td> --}}
        {{-- <td class="thick-border__td text-right">{{ number_format(!empty($cut['unit_price']) ? intval($cut['unit_price']) * floatNumberFormat($cut['num']) : intval(productsFilterById($products, $cut['product_id'])['fc_price']) * floatNumberFormat($cut['num'])) }}</td> --}}
      </tr>
      @endforeach
    @endif
      <!-- 副資材単品売りならば -->
  @elseif($rc['cut'] ==  '1' && $rc['product_type_id'] == '2')
      <tr>
        <td class="thick-border__td">{{ $rc['product_name'] }}</td>
        <td class="thick-border__td text-right">{{$rc['num']}}</td>
        <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
        <td class="thick-border__td text-right">{{ number_format($rc['unit_price'] * floatNumberFormat($rc['num'])) }}</td>
      </tr>
  @endif
@endforeach

@if( (!empty($transactions[0]['shipping_cost']) && $transactions[0]['special_discount']) || (isAdmin() && $transactions[0]['special_discount']) )
      <tr>
        <td colspan="4" class="thick-border__td">特別割引</td>
        <td colspan="1" class="thick-border__td">{{ number_format($transactions[0]['special_discount']) }}円</td>
      </tr>
@endif
@if( (!empty($transactions[0]['shipping_cost']) && $transactions[0]['discount']) || (isAdmin() && $transactions[0]['discount']))
      <tr>
        <td colspan="4" class="thick-border__td">大口割引</td>
        <td colspan="1" class="thick-border__td">{{ number_format($transactions[0]['discount']) }}円</td>
      </tr>
@endif
      <tr>
        <td colspan="4" class="thick-border__td">送料</td>
@if( is_null($transactions[0]['shipping_cost']))
        <td colspan="1" class="thick-border__td">後ほど本部が決定します。</td>
@else
        <td colspan="1" class="thick-border__td">{{ number_format($transactions[0]['shipping_cost']) . '円'}}</td>
@endif
    </table>
  </body>
</html>
@php
    if(config('app.env') != 'circleci'){
        $out = ob_get_contents();
        ob_end_flush();
        file_put_contents('transaction-pdftest.html', $out);
    }
@endphp
