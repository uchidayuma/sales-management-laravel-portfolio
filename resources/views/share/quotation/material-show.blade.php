      
  <table class="table table-responsive-md mb30" id="turf-table">
    <thead>
      <tr>
        <th class="">商品名</th>
        <th class="">枚数</th>
        <th class="">数量</th>
        <th class="">税抜単価（円）</th>
        <th class="">税抜金額（円）</th>
        <th class="">備考欄</th>
      </tr>
    </thead>
    <tbody id='product-body'>
@foreach($quotations AS $rc)
      <tr>
  @if(empty($rc['product_id']) && $rc['cut']=='0')
  <!-- 手動入力注文なら -->
        <td>{{ $rc['other_product_name'] }}</td>
        <td></td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>({{$rc['unit']}})</td>
        <td>{{ number_format($rc['unit_price']) }}円</td>
        <td>{{ number_format($rc['unit_price'] * $rc['num']) }}円</td>
  @elseif($rc['cut'] ==  '1' && $rc['product_type_id'] == '1')
  <!-- 人工芝の切り売りならば -->
        <td>{{ $rc['product_name'] . '（' . floatNumberFormat($rc['horizontal'], 2). 'm ×' . floatNumberFormat($rc['vertical'], 2) . 'm）' }}</td>
        <td>{{ is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'] }}枚</td>
        <td><span class='f11 bold'>{{ floatNumberFormat($rc['num']) * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num']) }}</span>（㎡）</td>
        <td>{{ number_format($rc['unit_price']) }}円</td>
        <td>{{ number_format($rc['unit_price'] * floatNumberFormat($rc['num']) * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}円</td>
  @elseif($rc['cut'] ==  '1' && $rc['product_type_id'] == '2')
  <!-- 副資材単品売りならば -->
        <td>{{ $rc['product_name'] }}</td>
        <td></td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>（{{ $rc['unit'] }}）</td>
        <td>{{ number_format($rc['unit_price']) }}円</td>
        <td>{{ number_format($rc['unit_price'] * floatNumberFormat($rc['num'])) }}円</td>
  @elseif($rc['cut'] ==  '0')
  <!-- 反物売りだった場合 -->
        <td>{{ $rc['product_name'] }}{{productArea($products,$rc['product_id'])}}</td>
        <td></td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>({{$rc['unit']}})</td>
        <td>{{ number_format($rc['unit_price']) }}円</td>
        <td>{{ number_format($rc['unit_price'] * $rc['num']) }}円</td>
  @endif
        <td>{{ !is_null($rc->memo) ? $rc->memo : '' }}</td>
      </tr>
  @if(!empty($rc['turf_cuts'][0]))
    @foreach($rc['turf_cuts'] AS $cuts)
    <!-- もしカットメニューが付随していれば -->
      <tr class='turf-cuts'>
        <td class='pl30'>{{ productsFilterById($products, $cuts['product_id'])['name'] }}</td>
        <td></td>
        <td>{{ $cuts['num'] * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num']). '（' . productsFilterById($products, $cuts['product_id'])['unit'] .'）'}}</td>
        <td>{{ number_format($cuts['unit_price']) }}円</td>
        <td>{{ number_format($cuts['unit_price'] * $cuts['num'] * (is_null($rc['cut_set_num']) ? 1 : $rc['cut_set_num'])) }}円</td>
        <td>{{ !empty($cuts['memo'])  ? $cuts['memo'] : '' }}</td>
      </tr>
    @endforeach
  @endif
@endforeach
@if(!empty($quotations[0]->discount))
      <tr>
        <td>お値引き</td>
        <td></td>
        <td></td>
        <td>{{ number_format($quotations[0]->discount) }}円</td>
      </tr>
@endif
    </table>
  @if( Route::currentRouteName() == 'quotations.download')
      <div class="memo px-3 pt-1 mb20 my-3 w100 mh-25 border border-dark" name="q[memo]">
        <u class="h5 memo-heading">備考欄</u>
        <p class='memo'>{!! nl2br($quotations[0]->qmemo) !!}</p>
      </div>
  @else
    <textarea class="memo p10 mb20 w100" name="q[memo]" cols="100" rows="5" style='resize: none;' readonly>{{ $quotations[0]->quotation_memo }}</textarea>
  @endif