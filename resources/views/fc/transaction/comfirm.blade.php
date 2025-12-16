@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/quotation/create.min.css?20210812') }}" rel="stylesheet" />
<style>
  .company-wrapper__seal {
    position: absolute;
    top: 8rem;
  }
  canvas{
    margin-top: 500vh;
  }
</style>
@endsection

@section('javascript')
<script>
  window.products = @json($products);
  window.pt = @json(old('pt') ? old('pt') : []);
  window.subTotal = 0;
  window.appEnv = "{{ \App::environment() }}";
</script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/canvas2/canvas2.js') }}" defer></script>
<script src="{{ asset('js/jquery/jquery-ui.min.js?20210812') }}" defer></script>
<script src="{{ asset('js/transaction/confirm.js?20210812') }}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}


<!-- 発注商品項目テーブル -->
<div id='canvas' class="card bg-white p-5">
@if($posts['t']['prepaid']=='1')
  <div class="alert alert-warning bold" role="alert">この発注は半額前金での支払いになります</div>
@elseif($posts['t']['prepaid']=='2')
  <div class="alert alert-danger bold" role="alert">この発注は全額前金での支払いになります</div>
@endif
  <div class='mb20 d-flex justify-content-between'>
    <div class='contact-wrapper w60per'>
      <h4 class='quotation-target h4 bold'>サンプル株式会社　御中 </h4>
      <p class='small mb20'>下記の通り発注申し上げます。</p>
      <table class="table table-bordered">
    @if(!empty($contact->step_id))
        <tr>
          <th>工事名称</th>
          <td>案件No.{{ $contact['id']}} {{ customerName($contact) }}:{{$contact->pref.$contact->city.$contact->street}}</td>
        </tr>
    @endif
        <tr>
          <th>受け取り場所</th>
          <td>{{ !empty($posts['t']['address']) ? $posts['t']['address'] : '資材発注' }}</td>
        <tr>
          <th>荷受人
        @if(!empty($posts['t']['direct_shipping']))
            <br>
            <p class="text-danger">※この資材は直接顧客に発送されます</p>
        @endif
          </th>
          <td>{{ $posts['t']['consignee'] }}</td>
        </tr>
        <tr>
          <th>荷受け人様連絡先TEL: </th>
          <td>{{ $posts['t']['tel'] }}</td>
        </tr>
        <tr>
          <th>納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($posts['t']['delivery_at']))}}
          </td>
        </tr>
      @if(!empty($posts['t']['delivery_at2']))
        <tr>
          <th>第2納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($posts['t']['delivery_at2']))}}
          </td>
        </tr>
      @endif
      @if(!empty($posts['t']['delivery_at3']))
        <tr>
          <th>第3納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($posts['t']['delivery_at3']))}}
          </td>
        </tr>
      @endif
        <tr>
          <th>支払い方法</th>
          <td>
        @switch($posts['t']['prepaid'])
        @case('0')
            月末請求書支払い
        @break
        @case('1')
            半額前払い
        @break
        @case('2')
            全額前払い
        @break
        @default
            月末請求書支払い
        @endswitch
          </td>
        </tr>
        <tr>
          <th>その他備考</th>
          <td class='apply-new-line'>{{ $posts['t']['memo'] }}</td>
        </tr>
      </table>

      <table class="table table-bordered">
        <thead>
          <tr>
            <td scope="col" class="">小計(円)</td>
            <td scope="col" class="">消費税(円)</td>
            <td scope="col" class="">合計金額(円)</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td id="subTotal">{{ number_format($posts['t']['sub_total'] + $posts['t']['shipping_cost']) }}</td>
            <td id='tax'>{{ number_format($tax) }}</td>
            <td id='total' class="total">{{ number_format($posts['t']['sub_total'] + $posts['t']['shipping_cost'] + $tax) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class='company-wrapper w30per'>
    @if(isset($id))
      <p class='company-wrapper__info mb10'>発注書番号：</p>
    @endif
      <p class='company-wrapper__info mb10'>発注日： {{date('Y年m月d日')}}</p>
      <h4 class='h4 bold'>{{ $user->company_name }}</h4>
      <p class='company-wrapper__info'>〒{{ $user->zipcode }}</p>
      <p class='company-wrapper__info'>{{ $user->pref }}{{ $user->city }}{{ $user->street }}
    </div>
  @if(!empty($user->seal))
    <img class='company-wrapper__seal' src="/images/seals/{{ $user->seal }}">
  @endif
  </div>

  <table class="table table-responsive-md mb30" id="turf-table">
    <thead>
      <tr>
        <th class="">商品名</th>
        <th class="">数量</th>
        <th class="">単価</th>
        <th class="">金額</th>
      </tr>
    </thead>
    <tbody id='product-body'>
  @foreach($posts['pt'] AS $rc)
      <tr>
        <!-- この辺で商品の種類を分岐 -->
    @if( empty($rc['product_id']) )
        <td>{{ $rc['other_product_name'] }}</td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>({{$rc['unit']}})</td>
    @elseif( isset($rc['num']) )
    @if(!empty($rc['is_cut']) && !empty($rc['parent_id']))
        <td class='pl30 pr0'>{{myArrayFilter($products, 'id', $rc['product_id'])['name']}}</td>
    @else
        <td>{{myArrayFilter($products, 'id', $rc['product_id'])['name']}} {{productArea($products,$rc['product_id'])}}</td>
    @endif
        <td><span class='f11 bold'>{{ !empty($rc['cut_set_num']) ? $rc['cut_set_num'] * $rc['num'] : $rc['num'] }}</span>({{$rc['unit']}})</td>
    @elseif( !empty($rc['product_id']) && !empty($rc['horizontal']) && !empty($rc['vertical']) )
        <td>{{ myArrayFilter($products, 'id', $rc['product_id'])['name'] . '（' . $rc['horizontal']. 'm × ' . $rc['vertical'] . 'm）'}}
            {{ !empty($rc['cut_set_num']) ? $rc['cut_set_num'] .'枚' : '' }}
        </td>
        <td><span class='f11 bold'>{{ !empty($rc['cut_set_num']) ? $rc['cut_set_num'] * $rc['area'] : $rc['area'] }}</span>（㎡）</td>
    @endif
        <td>{{ number_format($rc['unit_price']) }}円</td>
        <td>{{ number_format(!empty($rc['cut_set_num']) ? $rc['cut_set_num'] * $rc['price'] : $rc['price']) }}円</td>
      </tr>
  @endforeach
      <tr>
        <td colspan="2">送料</td>
        <td colspan="2">{{number_format($posts['t']['shipping_cost'])}}円</td>
      </tr>
    </tbody>
  </table>
  <form id="transactionForm" class='quotation-form br5' action="{{ route('transaction.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
  @if(empty($posts['t']['contact_id']) && is_null($contact))
    <input type="hidden" name="t[contact_id]" value="">
  @else
    <input type="hidden" name="t[contact_id]" value="{{ !empty($posts['t']['contact_id']) ? $posts['t']['contact_id'] : $contact['id'] }}">
  @endif
    <input type="hidden" name="t[total]" value="{{ $posts['t']['total'] }}">
    <input type="hidden" name="t[sub_total]" value="{{ $posts['t']['sub_total'] }}">
    <input type="hidden" name="t[memo]" value="{{ $posts['t']['memo'] }}">
    <input type="hidden" name="t[shipping_cost]" value="{{$posts['t']['shipping_cost']}}">
  @if(!$delivery_flag)
    <p class="color-red lead text-right">納品希望日が最短納品日より短くなっています。日付を選択し直してください。</p>
  @endif
    <div class='d-flex align-items-center justify-content-end'>
      <a href="{{ route('create.order', [
        'id' => $posts['t']['contact_id'],
        'contactId' => $posts['t']['contact_id'],
        'contact_id' => !empty($posts['t']['contact_id']) ? $posts['t']['contact_id'] : '',
        'address' => !empty($posts['t']['address']) ? $posts['t']['address'] : '',
        'tel' => !empty($posts['t']['tel']) ? $posts['t']['tel'] : '', 'consignee' => $posts['t']['consignee'], 
        'delivery_at' => $posts['t']['delivery_at'],
        'delivery_at2' => !empty($posts['t']['delivery_at2']) ? $posts['t']['delivery_at2'] : '', 
        'delivery_at3' => !empty($posts['t']['delivery_at3']) ? $posts['t']['delivery_at3'] : '', 
        'address_type' => !empty($posts['t']['address_type']) ? $posts['t']['address_type'] : '',
        'prepaid' => $posts['t']['prepaid'], 'memo' => !empty($posts['t']['memo']) ? $posts['t']['memo'] : '',
        'discount' => !empty($posts['t']['discount']) ? $posts['t']['discount'] : 0,
        'after_contact_id' => !empty($contact['id']) ? $contact['id'] : '',
      ])}}"
      type="button" class="btn btn-lg btn-warning mr20" id="back-button">発注を修正</a>
    @if($delivery_flag)
      <input type="submit" class="btn btn-lg btn-primary" value='発注を確定' id="post-transaction" dusk='commit'>
    @else
      <input type="submit" class="btn btn-lg btn-secondary" value='発注を確定' id="post-transaction" dusk='commit' disabled>
    @endif
    </div>
    <!-- store用のデータ -->
    <input type="file" class='d-none' name="canvas" id="canvas-data" value="">
    <input type='hidden' name="t[address]" value="{{ !empty($posts['t']['address']) ? $posts['t']['address'] : '' }}">
    <input type='hidden' name="t[address_type]" value="{{ !empty($posts['t']['address_type']) ? $posts['t']['address_type'] : 1 }}">
    <input type='hidden' name="t[consignee]" value="{{ !empty($posts['t']['consignee']) ? $posts['t']['consignee'] : '' }}">
    <input type='hidden' name="t[tel]" value="{{ !empty($posts['t']['tel']) ? $posts['t']['tel'] : '' }}">
    <input type="hidden" name="t[prepaid]" value="{{ !is_null($posts['t']['prepaid']) ? $posts['t']['prepaid'] : 0 }}">
    <input type='hidden' name="t[delivery_at]" value="{{ $posts['t']['delivery_at'] }}">
  @if(!empty($posts['t']['delivery_at2']))
    <input type='hidden' name="t[delivery_at2]" value="{{ $posts['t']['delivery_at2'] }}">
  @endif
  @if(!empty($posts['t']['delivery_at3']))
    <input type='hidden' name="t[delivery_at3]" value="{{ $posts['t']['delivery_at3'] }}">
  @endif
    <input type='hidden' name="t[discount]" value="{{ !is_null($posts['t']['discount']) ? $posts['t']['discount'] : 0 }}">
    <input type='hidden' name="t[direct_shipping]" value="{{ !empty($posts['t']['direct_shipping']) ? $posts['t']['direct_shipping'] : 0 }}">
@foreach($posts['pt'] AS $key => $val)
{{-- 手入力商品の場合 --}}
  @if(empty($val['product_id']))
    <input type='hidden' name="pt[{{ $key }}][other_product_name]" value="{{ $val['other_product_name'] }}">
    <input type='hidden' name="pt[{{ $key }}][other_product_price]" value="{{ $val['unit_price'] }}">
    <input type='hidden' name="pt[{{ $key }}][row_id]" value="{{ $val['row_id'] }}">
    {{-- カット人工芝の付属カット --}}
  @elseif(!empty($val['parent_id']))
    <input type='hidden' name="cut[{{ $key }}][parent_id]" value="{{ $val['parent_id'] }}">
    <input type='hidden' name="cut[{{ $key }}][product_id]" value="{{ $val['product_id'] }}">
    <input type='hidden' name="cut[{{ $key }}][unit_price]" value="{{ $val['unit_price'] }}">
    <input type='hidden' name="cut[{{ $key }}][num]" value="{{ $val['num'] }}">
    <input type='hidden' name="cut[{{ $key }}][cut_set_num]" value="{{ !empty($val['cut_set_num']) ? $val['cut_set_num'] : 1 }}">
  @else
    <input type='hidden' name="pt[{{ $key }}][product_id]" value="{{ $val['product_id'] }}">
    <input type='hidden' name="pt[{{ $key }}][unit_price]" value="{{ $val['unit_price'] }}">
    <input type='hidden' name="pt[{{ $key }}][row_id]" value="{{ $val['row_id'] }}">
  @endif
    <!-- 切り売り人工芝の場合 -->
  @if( !empty($val['vertical']) && !empty($val['horizontal']) )
    <input type='hidden' name="pt[{{ $key }}][vertical]" value="{{ $val['vertical'] }}">
    <input type='hidden' name="pt[{{ $key }}][horizontal]" value="{{ $val['horizontal'] }}">
    <input type='hidden' name="pt[{{ $key }}][unit_price]" value="{{ $val['unit_price'] }}">
    <input type='hidden' name="pt[{{ $key }}][num]" value="{{ $val['area'] }}">
    <input type='hidden' name="pt[{{ $key }}][cut_set_num]" value="{{ !empty($val['cut_set_num']) ? $val['cut_set_num'] : 1 }}">
    <input type='hidden' name="pt[{{ $key }}][cut]" value="1">
    <input type='hidden' name="pt[{{ $key }}][unit]" value="㎡">
  @elseif(!empty($val['parent_id']))
  @else
    <input type='hidden' name="pt[{{ $key }}][vertical]" value="">
    <input type='hidden' name="pt[{{ $key }}][horizontal]" value="">
    <input type='hidden' name="pt[{{ $key }}][unit_price]" value="{{ $val['unit_price'] }}">
    <input type='hidden' name="pt[{{ $key }}][num]" value="{{ $val['num'] }}">
    {{-- 副資材のバラ売りの場合は cut=1にする --}}
    <input type='hidden' name="pt[{{ $key }}][cut]" value="{{ !empty($val['is_cut']) ? 1 : 0}}">
    <input type='hidden' name="pt[{{ $key }}][unit]" value="{{ $val['unit'] }}">
  @endif
@endforeach
  </form>
</div><!-- card -->

<!-- 見積もり項目テーブル -->

@endsection
