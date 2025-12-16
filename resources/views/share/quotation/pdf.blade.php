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
    <title>見積書確認</title>

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
        font-size : 12px;
        margin: 0;
        padding: 0;
      }
      p {
        font-family: ipag !important;
        background-image: none;
        font-size : 12px;
        font-weight: 400;
      }
      h4 {
        font-family: ipag !important;
        background-image: none;
      }
      table {
        font-size: 11px;
      }
      .thick-border{
        border: solid 3px #333; 
        border-collapse: collapse;
      }
      .thick-border__td{
        padding : 0.5rem 0.5rem;
        border: solid 1px #333; 
        font-weight: 400;
      }
      .thick-border__th{
        padding : 0.5rem 0.5rem;
        border: solid 1px #333;
        font-style: bold;
        font-weight: bold;
        background-color: #ccc;
      }
      .memo-heading{
        font-family: ipag !important;
        background-image: none;
        font-weight: bold;
        margin-bottom: 10px;
      }
      .payee{
        font-size: 11px;
        border: 1px solid #666;
      }
      .memo{
        font-family: ipag !important;
        background-image: none;
        font-size: 12px;
        font-weight: 400;
        height: auto;
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
        width : 120px;
        height : 120px;
      }
      .quotation-name{
        width: 70%;
        margin: 0 10%;
      }
      .pl30{
        padding-left: 30px;
      }
      .company-wrapper__info{
        margin: 0;
      }
      .page-number:after { content: counter(page); }
    </style>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery/jquery-3.4.1.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/sidebar.js') }}" defer></script>
  </head>

  <body>
<script type="text/php">
  echo $PAGE_COUNT;
</script>
    {{-- <div class="p-3"> --}}
      <img class="shiba-logo" src="{{ asset('images/logo-r.jpg') }}" alt="サンプルFCロゴ">
      <h3 class="text-center font-weight-bold text-uppercase py-3 bg-white quotation-name">{{ $quotations[0]->quotation_name }}</h3>

      {{-- <div class='w100per h-25' style="margin-bottom: -20px;"> --}}
      <div class='w100per'>
        <div class='w55per d-inline-block'>
          <u class='quotation-target h4 bold'>{{ $quotations[0]->client_name }}</u>
          <p class='small mb10'>下記の通りお見積もり申し上げます。</p>
          <table class="thick-border mb-2">
            <tr>
              <th scope="col" class="thick-border__th">10％対象計</th>
              <th scope="col" class="thick-border__th">消費税（10％）</th>
              <th scope="col" class="thick-border__th">税込合計金額</th>
            </tr>
            <tr>
              <td id="subTotal" class="thick-border__td px-2">{{ number_format($quotations[0]->sub_total) }}(円)</td>
              <td id='tax' class="thick-border__td px-2">{{ number_format($quotations[0]->total - $quotations[0]->sub_total) }}(円)</td>
              <td id='total' class="total thick-border__td text-center px-2">{{ number_format($quotations[0]->total) }}(円)</td>
            </tr>
          </table>
      @if(!empty($quotations[0]['payee']))
          <p class='mb0'>お振込先</p>
          <p class="payee p10 mb-3">{!! !empty($quotations[0]['payee']) ? nl2br($quotations[0]['payee']) : '' !!}</p>
      @else
          <p class='mb70'></p>
          <br>
      @endif
        </div>

        <div class='w40per float-right company-wrapper'>
          <p class='d-flex align-items-center mb0'>見積日：{{ date('Y年m月d日', strtotime($quotations[0]['created_quotation'])) }}</p>
          <p class='company-wrapper__info mb0'>見積書番号：{{ $quotations[0]->quotation_id }}</p>
          <p class='company-wrapper__info d-flex align-items-center mb10'>見積書有効期限：{{ !empty($quotations[0]['effective_date']) ? $quotations[0]['effective_date'] : '1ヶ月' }}</p>
          <h6 class='h4 bold'>{{ $quotations[0]->company_name }}</h6>
        @if(!is_null($quotations[0]->staff_name))
          <p class='company-wrapper__info d-flex align-items-center mb-1'>担当者：{{$quotations[0]->staff_name}}</p>
        @endif
          <p class='company-wrapper__info'>〒{{ $formatted_zip_code = substr($quotations[0]->fc_zipcode, 0, 3) . '-' . substr($quotations[0]->fc_zipcode, 3, 4); }}</p>
          <p class='company-wrapper__info'>{{ $quotations[0]->fc_pref }}{{ $quotations[0]->fc_city }}{{ $quotations[0]->fc_street }}
          <p class='company-wrapper__info'>tel:{{ !empty($quotations[0]->fc_tel) ? $quotations[0]->fc_tel : '' }}</p>

      @if(!empty($quotations[0]->seal))
          <img class='company-wrapper__seal' src={{ !empty($quotations[0]->seal) ? url("/images/seals/".$quotations[0]->seal) : '' }}>
      @endif
      @if(!is_null($quotations[0]->qualified_business_number))
          <p class='company-wrapper__info'>登録番号:T{{$quotations[0]->qualified_business_number}}</p>
      @endif
        </div>
      </div>
    {{-- <div class='w100per'> --}}
      <table class="thick-border w100per" id="quotationTable" style="width: 100%">
  @if($quotations[0]->type==0)
        <!-- 施工見積もり -->
        {{-- 最初だけタイトル行を追加 --}}
        <tr>
          <th class="thick-border__th text-center w35per">詳細</th>
          <th class="thick-border__th text-center w10per">数量</th>
          <th class="thick-border__th text-center w10per">単位</th>
          <th class="thick-border__th text-center w10per">税抜単価（円）</th>
          <th class="thick-border__th text-center w10per">税抜金額（円）</th>
          <th class="thick-border__th text-center w25per">備考欄</th>
        </tr>
    @foreach($quotations as $key => $q)
      {{-- @switch(true) --}}
        {{-- もし14行以上の見積もりがある場合は次ページへ続くメッセージと次ページの先頭にheader 設置 14行目以降は 22 行ごとに入れ込む  --}}
      @if( $key + $pdf_payee_count + $pdf_memo_count === 16 && ($pdf_product_count + $pdf_memo_count + $pdf_payee_count > 15) || $key > 15 && ($key+$pdf_payee_count - 18) % 25 === 0 )
        <tr style="page-break-after:always;font-size:15px;">
          <td class="thick-border__td text-right" colspan="6">次ページに続きます</td>
        </tr>
        <tr>
          <th class="thick-border__th text-center w35per">詳細</th>
          <th class="thick-border__th text-center w10per">数量</th>
          <th class="thick-border__th text-center w10per">単位</th>
          <th class="thick-border__th text-center w10per">税抜単価（円）</th>
          <th class="thick-border__th text-center w10per">税抜金額（円）</th>
          <th class="thick-border__th text-center w25per">備考欄</th>
        </tr>

      @endif
      {{-- @default --}}
        <tr>
          <td class="thick-border__td">{{ $q->product_name ? $q->product_name : $q->name }}</td>
          <td class="thick-border__td text-right">{{ floatNumberFormat($q->num) }}</td>
          <td class="thick-border__td text-center">{{ environmentalCharacterConversion($q->unit) }}</td>
          <td class="thick-border__td text-right">{{ number_format( !empty($q->quotation_unit_price) ? $q->quotation_unit_price : $q->outer_unit_price ) }}</td>
          <td class="thick-border__td text-right">{{ number_format($q->price) }}</td>
          <td class="thick-border__td text-left">{{ !empty($q->memo) ? $q->memo : '' }}</td>
        </tr>
      {{-- @endswitch --}}
    @endforeach

    @if(!empty($quotations[0]->discount))
        <tr>
          <td class="thick-border__td">お値引き</td>
          <td class="thick-border__td text-right"></td>
          <td class="thick-border__td text-right"></td>
          <td class="thick-border__td text-right"></td>
          <td class="thick-border__td text-right">{{ number_format($quotations[0]->discount) }}</td>
          <td></td>
        </tr>
    @endif
  @else
  <!-- ここから材料販売見積もり -->
        <tr>
          <th class="thick-border__th text-center w35per">詳細</th>
          <th class="thick-border__th text-center w10per">数量</th>
          <th class="thick-border__th text-center w10per">単位</th>
          <th class="thick-border__th text-center w10per">税抜単価（円）</th>
          <th class="thick-border__th text-center w10per">税抜金額（円）</th>
          <th class="thick-border__th text-center w25per">備考欄</th>
        </tr>
    @foreach($quotations AS $key => $rc)
        {{-- もし15行以上の見積もりがある場合は次ページへ続くメッセージと次ページの先頭にheader 設置 15行目以降は 27行ごとに入れ込む  --}}
      {{-- $keyから15（1ページめ）を引いて27で割った余りが0なら2ページめが満杯 --}}
      @if( ($key + $pdf_payee_count + $pdf_memo_count === 25 && ($pdf_product_count + $pdf_memo_count + $pdf_payee_count > 15)) || ($key > 15 && ($key+$pdf_payee_count - 18) % 25 === 0) )
        <tr style="page-break-after:always; font-size:15px;">
          <td class="thick-border__td text-right" colspan="6">次ページに続きます</td>
        </tr>
        @if(!empty($quotations[$key]))
          <tr>
            <th class="thick-border__th text-center w35per">詳細</th>
            <th class="thick-border__th text-center w10per">数量</th>
            <th class="thick-border__th text-center w10per">単位</th>
            <th class="thick-border__th text-center w10per">税抜単価（円）</th>
            <th class="thick-border__th text-center w10per">税抜金額（円）</th>
            <th class="thick-border__th text-center w25per">備考欄</th>
          </tr>
        @endif
      @endif
        {{-- @default --}}
        <!-- 手動入力注文なら -->
      @if(empty($rc['product_id']) && $rc['cut']=='0')
        <tr>
          <td class="thick-border__td">{{ $rc['other_product_name'] }}</td>
          <td class="thick-border__td text-right">{{$rc['num']}}</td>
          <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
          <td class="thick-border__td text-right">{{ floatNumberFormat($rc['unit_price'] * $rc['num']) }}</td>
          <td class="thick-border__td text-left">{{ !empty($rc['memo']) ? $rc['memo'] : '' }}</td>
        </tr>
        <!-- 反物売りだった場合 -->
      @elseif($rc['cut'] ==  '0')
        <tr>
          <td class="thick-border__td">{{ $rc['product_name'] }}{{productArea($products,$rc['product_id'])}}</td>
          <td class="thick-border__td text-right">{{$rc['num']}}</td>
          <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($rc['unit_price'] * $rc['num']) }}</td>
          <td class="thick-border__td text-left">{{ !empty($rc['memo']) ? $rc['memo'] : '' }}</td>
        </tr>
        <!-- 反物のカットは反物ごとに表示 -->
        @if(!empty($rc['turf_cuts'][0]))
          @foreach($rc['turf_cuts'] as $cut)
        <tr>
          <td class="thick-border__td">__反物カット賃</td>
          <td class="thick-border__td text-right">{{$rc['num']}}</td>
          <td class="thick-border__td text-center">{{ floatNumberFormat($cut['num']) }}反分</td>
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price'] * floatNumberFormat($cut['num'])) }}</td>
          <td class="thick-border__td text-left">{{ empty($cut['memo']) ? '' : $rc['memo'] }}</td>
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
          <td class="thick-border__td text-left">{{ !empty($rc['memo']) ? $rc['memo'] : '' }}</td>
        </tr>
        <!-- 角R加工とカップ穴だけは1行として表示 -->
        @foreach($rc['turf_cuts'] as $cut)
          @if($cut['unit'] != 'm')
        <tr>
          <td class="thick-border__td">{{ $products[$cut['product_id'] - 1]['name'] }}</td>
          <td class="thick-border__td text-right">{{ number_format($cut['num'] * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}</td>
          <td class="thick-border__td text-center">箇所</td>
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($cut['unit_price'] * floatNumberFormat($cut['num']) * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}</td>
          <td class="thick-border__td text-left">{{ empty($rc['memo']) ? '' : $rc['memo'] }}</td>
        </tr>
          @endif
        @endforeach
        <!-- 切り売り人工芝と副資材販売の間ならば（カット人工芝の次の行ならば -->
        @if(!empty($quotations[$key+1]))
          @if( ($quotations[$key+1]['product_type_id']=='2' || empty($quotations[$key+1]['product_id']) && $quotations[$key+1]['cut']=='0') && !empty($cut_total) )
        <tr>
          <td class="thick-border__td">カット賃</td>
          <td class="thick-border__td text-right">{{ floatNumberFormat($cut_total_length) }}</td>
          <td class="thick-border__td text-center">m</td>
          <td class="thick-border__td text-right" colspan="2">{{ number_format($cut_total) }}</td>
          <td class="thick-border__td"></td>
        </tr>
        @endif
      @endif
        <!-- カット賃が最後尾に来る場合は別で書く -->
      @if( empty($quotations[$key+1]) && !empty($cut_total) )
        <tr>
          <td class="thick-border__td">カット賃</td>
          <td class="thick-border__td text-right">{{ floatNumberFormat($cut_total_length) }}</td>
          <td class="thick-border__td text-center">m</td>
          <td class="thick-border__td text-right" colspan="2">{{ number_format($cut_total) }}</td>
          <td class="thick-border__td"></td>
        </tr>
      @endif
        <!-- 副資材単品売りならば -->
      @elseif($rc['cut'] ==  '1' && $rc['product_type_id'] == '2')
        <tr>
          <td class="thick-border__td">{{ $rc['product_name'] }}</td>
          <td class="thick-border__td text-right">{{$rc['num']}}</td>
          <td class="thick-border__td text-center">{{ environmentalCharacterConversion($rc['unit']) }}）</td>
          <td class="thick-border__td text-right">{{ number_format($rc['unit_price']) }}</td>
          <td class="thick-border__td text-right">{{ number_format($rc['unit_price'] * floatNumberFormat($rc['num'])) }}</td>
          <td class="thick-border__td text-left">{{ !empty($rc['memo']) ? $rc['memo'] : '' }}</td>
        </tr>
      @endif
      </tr>
      {{-- @break --}}
      {{-- @endswitch --}}
    @endforeach

    @if(!empty($quotations[0]->discount))
        <tr>
          <td class="thick-border__td">お値引き</td>
          <td class="thick-border__td text-right" colspan='4'>{{ number_format($quotations[0]->discount) }}円</td>
          <td></td>
        </tr>
    @endif
  @endif
  <!-- 材料販売見積もりおわり -->
    </table>
    {{-- 材料見積もりと施工見積もりで分岐している $cut_row_count === 0なら施工見積もり--}}
  @if($cut_row_count === 0)
    {{-- 備考欄が入り切らない時は、次ページに備考欄があると表示して次ページに備考欄を表示 --}}
    {{-- 見積もり行数とメモ行数の合計が12以上かつ、見積もり行から12引いて、22で割、あまりにメモの行数を足した値が22以上の場合 --}}
    {{-- 商品行が2ページ分以上かつ、備考欄が入り切らない（備考欄だけ次ページ）の場合 --}}
    @if($pdf_quotation_row_and_memo_count > 12 && (($pdf_product_count - 12) % 22) + $pdf_memo_count > 22)
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    {{-- 商品行が1ページ分だけど、備考欄ではみ出る場合 --}}
    @elseif($pdf_quotation_row_and_memo_count > 12 && $pdf_quotation_row_and_memo_count < 15 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @elseif($pdf_product_count === 15 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @elseif($pdf_product_count > 9 && $pdf_memo_count > 3 && $pdf_quotation_row_and_memo_count < 22 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @endif
  @elseif($cut_row_count === 1)
    {{-- 2ページ目が備考欄だけの場合 --}}
    {{-- @if($pdf_product_count === 14 && $pdf_payee_count > 2 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @elseif($pdf_product_count === 13 && (($pdf_product_count - 12) % 22) + $pdf_memo_count < 22 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @elseif($pdf_quotation_row_and_memo_count > 12 && (($pdf_product_count - 12) % 22) + $pdf_memo_count > 22 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @elseif($pdf_product_count > 9 && $pdf_memo_count > 3 && $pdf_quotation_row_and_memo_count < 22 && !empty($quotations[0]['quotation_memo'])))
        <p style="page-break-after:always;font-size:20px;">※次のページに備考欄があります</p>
    @endif --}}
  @endif
      {{-- </div> --}}
  @if(!empty($quotations[0]['quotation_memo']))
      <div class="memo px-3 pt-1 mb20 mt-3 mh-25 border border-dark mb20" name="q[memo]">
        <u class="h5 memo-heading">備考欄</u>
        <p class='memo'>{!! nl2br($quotations[0]['quotation_memo']) !!}</p>
      </div>
  @endif
    {{-- </div> --}}
  </body>
</html>
@php
    if(config('app.env') != 'production' && config('app.env') != 'circleci'){
        $out = ob_get_contents();
        ob_end_flush();
        file_put_contents('pdftest.html', $out);
    }
@endphp
