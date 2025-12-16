@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/quotation/create.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script>
  window.cancelAble = @json($cancelAble);
  window.transactions = @json($transactions);
  console.log(window.transactions[0]);
  // 入力ボタンの制御 ↓デフォルトは1つだけ
  window.inputState = 1;
</script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('js/transaction/show.js?20220818') }}" defer></script>
@endsection

@section('content')
@if(isFc() && $is_cancelable )
<div class="alert alert-danger alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>変更可能期限を過ぎたため、発注内容の変更ならびにキャンセルはお受けできません。何卒ご了承ください。 <br> もし キャンセルを希望される場合は<a class="btn btn-link" href="https://test.com/1ypr1iIAaIGDmHO64RNCKC4fd5KMf-9sl?usp=sharing" target=”_blank”>こちら</a>をクリックし【キャンセル・変更依頼届】をダウンロードした後、本部までお送りください。</strong>
</div>
@endif
<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<section class='actions mb-3 d-flex'>
  @if(isAdmin() && !is_null($transactions[0]['shipping_date']) )
  <button type="button" class="btn btn-info mr-3" data-toggle="collapse" data-target="#collapseInfo" aria-expanded="false" aria-controls="collapseInfo" dusk='shipping-info'>発送情報を表示</button>
  <button id='edit-cost1' class="btn btn-primary mr-3 open-modal" delivery-number="1" dusk='shipping-edit' data-toggle="modal" data-target="#shipping-edit-modal">送料と追跡番号を修正</button>
  @elseif(isAdmin())
  <button id='input-cost1' class="btn btn-primary open-modal mr-3" dusk='input-cost'
    delivery-number="1" data-toggle="modal" data-target="#shipping-modal">送料と追跡番号を入力</button>
  @endif
  @if(isAdmin())
  {{-- 第2納品希望日 --}}
  <button id='input-cost2' class="btn btn-warning open-modal mr-3 d-none" dusk='input-cost2'
    delivery-number="2" data-toggle="modal" data-target="{{ $transactions[0]['shipping_date2'] ? '#shipping-edit-modal' : '#shipping-modal' }}">発送2の送料と追跡番号を{{ $transactions[0]['shipping_date2'] ? '修正' : '入力' }}</button>
  {{-- 第3納品希望日 --}}
  <button id='input-cost3' class="btn btn-danger open-modal mr-3 d-none" dusk='input-cost3'
    delivery-number="3" data-toggle="modal" data-target="{{ $transactions[0]['shipping_date3'] ? '#shipping-edit-modal' : '#shipping-modal' }}">発送3の送料と追跡番号を{{ $transactions[0]['shipping_date3'] ? '修正' : '入力' }}</button>
  <button id='add-input' class="btn btn-info mr-3 d-none" dusk='add-input'>発送連絡を追加</button>
  @endif
  @if( !is_null($transactions[0]['shipping_cost']))
  <a class="btn btn-primary mr-3 ml-auto" href="{{route('transactions.download', ['id' => $id] )}}" target="blank">PDFを表示・印刷</a>
  @endif
</section>

@if(isAdmin() && ($transactions[0]['prepaid'] == 2 || $transactions[0]['prepaid'] == 1 ))
<button class="btn btn-secondary ml30 mb-3 open-modal" dusk='input-shipping-cost-modal-show' data-toggle="modal" data-target="#input-shipping-cost-modal">送料のみを入力（更新）</button>
@endif
{{-- 発送情報表示モーダル --}}
<ul class="list-group collapse w50 mb20" id="collapseInfo">
  @if($transactions[0]['delivery_at2'])
  <li class="list-group-item f11 bold text-primary">1個目の発送</li>
  @endif
  <li class="list-group-item f11"><span class='bold'>配送業者</span>：{{ !empty($transactions[0]['shipping_id']) ? returnTransportCompany($transactions[0]['shipping_id']): returnTransportCompany($transactions[0]['transaction_only_shipping_id']) }}</li>
  @if(empty($transactions[0]['shipping_date']) && empty($transactions[0]['transaction_only_shipping_date']))
  <li class="list-group-item f11"><span class='bold'>発送日</span>： 発送前</li>
  @else
  <li class="list-group-item f11"><span class='bold'>発送日</span>：{{ !empty($transactions[0]['transaction_only_shipping_date']) ? date('Y年m月d日', strtotime($transactions[0]['transaction_only_shipping_date'])) : date('Y年m月d日', strtotime($transactions[0]['shipping_date'])) }}</li>
  @endif
  <li class="list-group-item f11"><span class='bold'>追跡番号</span>：{{ !empty($transactions[0]['transaction_only_shipping_number']) ? $transactions[0]['transaction_only_shipping_number']: $transactions[0]['shipping_number']}}</li>
  @if($transactions[0]['shipping_date2'])
  <li class="list-group-item f11 bold text-primary">2個目の発送</li>
  <li class="list-group-item f11"><span class='bold'>配送業者</span>：{{ returnTransportCompany($transactions[0]['shipping_id2']) }}</li>
  <li class="list-group-item f11"><span class='bold'>発送日</span>：{{ date('Y年m月d日', strtotime($transactions[0]['shipping_date2'])) }}</li>
  <li class="list-group-item f11"><span class='bold'>追跡番号</span>：{{ $transactions[0]['shipping_number2']}}</li>
  @endif
  @if($transactions[0]['shipping_date3'])
  <li class="list-group-item f11 bold text-primary">3個目の発送</li>
  <li class="list-group-item f11"><span class='bold'>配送業者</span>：{{ returnTransportCompany($transactions[0]['shipping_id3']) }}</li>
  <li class="list-group-item f11"><span class='bold'>発送日</span>：{{ date('Y年m月d日', strtotime($transactions[0]['shipping_date3'])) }}</li>
  <li class="list-group-item f11"><span class='bold'>追跡番号</span>：{{ $transactions[0]['shipping_number3']}}</li>
  @endif
</ul>
{{-- 発送情報表示モーダルここまで --}}

<div class="card bg-white p-5">
  @if($transactions[0]['prepaid']=='2')
  <div class="alert alert-danger" role="alert">この発注は全額前金です</div>
  @elseif($transactions[0]['prepaid']=='1')
  <div class="alert alert-warning" role="alert">この発注は半額前金です</div>
  @endif
  <div class='mb20 d-flex justify-content-between'>
    <div class='contact-wrapper w60per'>
      <h4 class='quotation-target h4 bold'>サンプル株式会社 御中 </h4>
      <p class='small mb20'>下記の通り発注申し上げます。</p>
      <table class="table table-bordered">
        <tr>
          @if(!empty($transactions[0]['contact_id']))
          <th class='w40'>工事名称</th>
          <td>案件No.{{ empty($transactions[0]['own_contact']) ? '' : $transactions[0]['user_id'].'-' }}{{$transactions[0]['contact_id']}} &nbsp; {{ customerName($transactions[0]) }} &nbsp; 様<br>{{$transactions[0]['pref'].$transactions[0]['city'].$transactions[0]['street']}} </td>
          @endif
        </tr>
        <tr>
          <th class='w40'>受け取り場所<br>
            @if (!empty($transactions[0]['direct_shipping']))
            <p class="text-danger">※この資材は直接顧客に発送されます</p>
            @endif
          </th>
          <td>{{ $transactions[0]['address']}}</td>
        </tr>
        <tr>
          <th class='w40'>受け取り人</th>
          <td>{{ $transactions[0]['consignee']}}</td>
        </tr>
        <tr>
          @if(!empty($transactions[0]['contact_id']))
          <th class='w40'>荷受け人様連絡先TEL </th>
          <td>{{ $transactions[0]['transaction_tel'] }}</td>
          @else
          <th class='w40'>FC電話番号 </th>
          <td>{{ $transactions_table->tel }}</td>
          @endif
        </tr>
        <tr>
          <th class='w40'>納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($transactions[0]['delivery_at']))}}
          </td>
        </tr>
        @if(!empty($transactions[0]['delivery_at2']))
        <tr>
          <th class='w40'>第2納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($transactions[0]['delivery_at2']))}}
          </td>
        </tr>
        @endif
        @if(!empty($transactions[0]['delivery_at3']))
        <tr>
          <th class='w40'>第3納品希望日</th>
          <td>
            {{date('Y年m月d日', strtotime($transactions[0]['delivery_at3']))}}
          </td>
        </tr>
        @endif
        <tr>
          <th>その他備考</th>
          <td class='apply-new-line'>{{ $transactions[0]['transaction_memo'] }}</td>
        </tr>
      </table>

      <table class="table table-bordered w70">
        <thead>
          <tr>
            <td scope="col" class="">小計(円)</td>
            <td scope="col" class="">消費税(円)</td>
            <td scope="col" class="">合計金額(円)</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td id="subTotal">{{ number_format($sub_total) }}</td>
            <td id='tax'>{{ number_format($tax) }}</td>
            <td id='total' class="total">{{ number_format($total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class='company-wrapper w30per'>
      @if(isset($id))
      <p class='company-wrapper__info mb10'>発注書番号： {{$id}}</p>
      @endif
      <p class='company-wrapper__info mb10'>発注日： {{ date('Y年m月d日', strtotime($transactions[0]['transaction_time'])) }}</p>
      <h4 class='h4 bold'>{{ $transactions[0]['fc_name'] }}</h4>

      <!-- FCの住所を表示 -->
      <p class='company-wrapper__info'>〒{{ $userdata[0]->zipcode }}</p>
      <p class='company-wrapper__info'>{{ $userdata[0]->pref }}{{ $userdata[0]->city }}{{ $userdata[0]->street }}

        @if(!empty($transactions[0]['seal']))
        <img class='company-wrapper__seal' src="/images/seals/{{ $transactions[0]['seal'] }}">
        @endif
    </div>
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
      @foreach($transactions AS $rc)
      <tr class='f10'>
        @if(!$rc['product_id'])
        <!-- 手動入力注文なら -->
        <td>{{ $rc['other_product_name'] }}</td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>({{$rc['unit']}})</td>
        <td>{{ number_format($rc['other_product_price']) }}円</td>
        @elseif($rc['cut'] == '0' && $rc['product_type_id'] != 4)
        <!-- 反物売りだった場合 -->
        <td class="{{ $rc['product_type_id'] == 4 || $rc['product_type_id'] == 5 ? 'pl30 f08' : ''}}">{{ $rc['product_name'] }}{{productArea($products,$rc['product_id'])}}</td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>({{$rc['unit']}})</td>
        <td>{{ number_format( !is_null($rc['pt_unit_price']) ? $rc['pt_unit_price']: $rc['fc_price']) }}円</td>
        @elseif($rc['product_type_id'] == '3')
        <!-- 販促物ならば -->
        <td>{{ $rc['product_name'] }}</td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>（{{ $rc['unit'] }}）</td>
        <td>{{ number_format($rc['pt_unit_price'] ? $rc['pt_unit_price'] : $rc['cut_fc_price']) }}円</td>
        @elseif($rc['cut'] == '1' && $rc['product_type_id'] == '1')
        <!-- 人工芝の切り売りならば -->
        <td class="{{ $rc['product_type_id'] == 4 || $rc['product_type_id'] == 5 ? 'pl30 f08' : ''}}">
          {{ $rc['product_name'] . '（' . floatNumberFormat($rc['horizontal'], 2). 'm ×' . floatNumberFormat($rc['vertical'], 2) . 'm）' }}
          {{ $rc['cut_set_num'] > 1 ? $rc['cut_set_num'] .'枚' : '1枚'}}
        </td>
        <td><span class='f11 bold'>{{ floatNumberFormat( !empty($rc['cut_set_num']) ? $rc['cut_set_num'] * $rc['num'] : $rc['num']) }}</span>（㎡）</td>
        <td>{{ number_format($rc['pt_unit_price'] ? $rc['pt_unit_price'] : $rc['cut_fc_price']) }}円</td>
        @elseif($rc['cut'] == '1' && $rc['product_type_id'] == '2')
        <!-- 副資材単品売りならば -->
        <td>{{ $rc['product_name'] }}</td>
        <td><span class='f11 bold'>{{$rc['num']}}</span>（{{ $rc['unit'] }}）</td>
        <td>{{ number_format($rc['pt_unit_price'] ? $rc['pt_unit_price'] : $rc['cut_fc_price']) }}円</td>
        @endif
        {{-- 金額 --}}
        <td>{{ number_format(!empty($rc['cut_set_num']) ? $rc['cut_set_num'] * $rc['price'] : $rc['price']) }}円</td>
      </tr>
      @if(!empty($rc['turf_cuts'][0]))
      @foreach($rc['turf_cuts'] AS $cuts)
      <!-- もしカットメニューが付随していれば -->
      <tr class='turf-cuts'>
        <td class='pl30'>{{ productsFilterById($products, $cuts['product_id'])['name'] }}</td>
        <td>{{ !empty($cuts['cut_set_num']) ? $cuts['cut_set_num'] * $cuts['num'] : $cuts['num'] . '（' . productsFilterById($products, $cuts['product_id'])['unit'] .'）'}}</td>
        {{-- $cut['unit_price']がnullなのは過去データ用 --}}
        @if( !array_key_exists('unit_price', $cuts) )
        <td class=''>{{ number_format($products[$cuts['product_id'] - 1]['fc_price']) }}円</td>
        @if( empty($cuts['cut_set_num']) )
        <td>{{ number_format($products[$cuts['product_id'] - 1]['fc_price'] * $cuts['num'] ) }}円</td>
        @else
        <td>{{ number_format($products[$cuts['product_id'] - 1]['fc_price'] * $cuts['num'] * $cuts['cut_set_num'] ) }}円</td>
        @endif
        @else
        <td class=''>{{ number_format($cuts['unit_price']) }}円</td>
        @if( empty($cuts['cut_set_num']) )
        <td>{{ number_format($cuts['unit_price'] * $cuts['num'] * 1 ) }}円</td>
        @else
        <td>{{ number_format($cuts['unit_price'] * $cuts['num'] * $cuts['cut_set_num'] ) }}円</td>
        @endif
        @endif
      </tr>
      @endforeach
      @endif
      @endforeach

      @if( (!empty($transactions[0]['transaction_only_shipping_date']) && $transactions[0]['special_discount']) || (isAdmin() && $transactions[0]['special_discount']) )
      <tr>
        <td colspan="3">特別割引</td>
        <td colspan="1">{{ number_format($transactions[0]['special_discount']) }}円</td>
      </tr>
      @endif
      @if( (!empty($transactions[0]['transaction_only_shipping_date']) && $transactions[0]['discount']) || (isAdmin() && $transactions[0]['discount']))
      <tr>
        <td colspan="3">大口割引</td>
        <td colspan="1">{{ number_format($transactions[0]['discount']) }}円</td>
      </tr>
      @endif
      <tr>
        <td colspan="3">送料</td>
        @if( is_null($rc['shipping_cost']))
        <td colspan="1">後ほど本部が決定します。</td>
        @else
        <td colspan="1">{{ number_format($rc['shipping_cost']) . '円'}}</td>
        @endif
      </tr>
    </tbody>
  </table>
  <div class='d-flex align-items-center justify-content-end'>
    <a class="px-xl-5 btn btn-lg btn-secondary mr-3" href="{{route('transactions')}}">戻る</a>
    @if(isFc() && $cancelAble)
    <form id='delete-form' action="{{ route('transactions.delete', ['transactionId' => $transaction_id]) }}" method="post" enctype="multipart/form-data">
      <input id='delete-id' type='submit' class="btn btn-lg btn-secondary mr20" value='発注をキャンセル' dusk='cancel-transaction' />
      @csrf
    </form>
    @endif
    @if( (isFc() && $cancelAble) || isAdmin() )
    <a href="{{ route('transactions.edit', ['transactionId' => $transaction_id]) }}" class="btn btn-lg btn-warning mr20">発注を修正</a>
    @endif
  </div>
</div><!-- Card -->

<!-- 編集用モーダル -->
<div class="modal fade" id="shipping-edit-modal" tabindex="-1" role="dialog" aria-labelledby="shippingEditModalLabel" aria-hidden="true">
  <form action="{{ route('shipping.update', ['transactionId' => $transaction_id]) }}" method="post">
    @csrf
    <!-- メール用データ -->
    <input type='hidden' name='userid' value="{{ $userdata[0]['id'] }}" />
    <input type='hidden' name='m[transaction_id]' value="{{ $transactions[0]['id'] }}" />
    <input type='hidden' name='m[transport_company]' value="" />
    <input type='hidden' name='m[trakking_url]' value="" />
    {{-- ↓ 分納において、いくつ目の発送なのかを決める変数 --}}
    <input type='hidden' name='number' value="1" />
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          @if(!empty($transactions[0]['contact_id']))
          <h5 class="modal-title" id="shippingModalLabel">
            案件No.{{ empty($transactions[0]['own_contact']) ? '' : $transactions[0]['user_id'].'-' }}
            {{ $transactions[0]['contact_id'] }}：{{ $transactions[0]['fc_name'] }}
            の発送情報を変更
          </h5>
          @else
          <h5 class="modal-title" id="shippingModalLabel">発送情報の変更</h5>
          @endif
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type='hidden' id='contact-id' value="{{ $transactions[0]['contact_id'] }}" name='contact_id'>
          <input type='hidden' id='transaction-id' value="{{ $id }}" name='transaction_id'>
          <div class="form-group mb20">
            <label class='f12'>送料（税抜き）</label>
            <input type="number" class="form-control" value="{{ !empty($transactions[0]['shipping_cost']) ? $transactions[0]['shipping_cost'] : 0 }}" name='shipping_cost' dusk='edit-cost' required>
          </div>
          <div class="form-group mb20">
            <label class='f12'>配送業者</label>
            <select class="form-control" name='shipping_id' dusk='edit-shipping-company'>
              <option value="">選択してください</option>
              @foreach($deliveries AS $d)
              <option value="{{ $d->id }}" {{ !empty($transactions[0]['shipping_id']) ? selected( $d->id == $transactions[0]['shipping_id']) : selected( $d->id == $transactions[0]['transaction_only_shipping_id'])}}>{{ $d->transport_company }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>発送日</label></p>
            <input type="datetime" class="datepicker form-control" name='shipping_date' placeholder="発送日を入力" value="{{ !empty($transactions[0]['transaction_only_shipping_date']) ? $transactions[0]['transaction_only_shipping_date']: $transactions[0]['shipping_date']}}" dusk='edit-shipping-date' required autocomplete="off" />
          </div>
          <div class="form-group mb20">
            <label class='f12'>問い合わせ番号</label>
            <input type="text" class="form-control" placeholder="問い合わせ番号" value="{{ !empty($transactions[0]['transaction_only_shipping_number']) ? $transactions[0]['transaction_only_shipping_number'] : $transactions[0]['shipping_number'] }}" name='shipping_number' dusk='edit-number' required autocomplete="off" />
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>個別連絡事項（メールに記載されます）</label></p>
            <textarea class='form-control' name='dispatch_message'>{{ !empty($transactions[0]['dispatch_message']) ? $transactions[0]['dispatch_message'] : '' }}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button class="btn btn-primary" dusk='edit-cost-submit'>送料更新と連絡（FCに自動メール通知）</button>
        </div>
      </div>
    </div>
  </form>
</div><!-- Modal -->

<!-- 発送情報入力Modal -->
@if(!empty($transactions[0]))
<div class="modal fade" id="shipping-modal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
  <form action="{{ route('dispatch.store') }}" method="post">
    @csrf
    <!-- メール用データ -->
    <input type='hidden' name='userid' value="{{ $userdata[0]['id'] }}" />
    {{-- ↓ 分納において、いくつ目の発送なのかを決める変数 --}}
    <input type='hidden' name='number' value="1" />
    <input type='hidden' name='m[transaction_id]' value="{{ $transactions[0]['id'] }}" />
    <input type='hidden' name='m[transport_company]' value="" />
    <input type='hidden' name='m[trakking_url]' value="" />
    <!-- FC顧客への発送か確認するため用 -->
    <input type='hidden' name='direct_shipping' value="{{ $transactions_table['direct_shipping'] }}" />
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          @if( $transactions[0]['prepaid'] == 2 && !is_null($transactions[0]['shipping_cost'] ))
          <h5 class="modal-title" id="shippingModalLabel">{{ $transactions[0]['fc_name'] }}への発送情報を入力<span id='shipping-number'></span></h5>
          @elseif(!empty($transactions[0]['contact_id']))
          <h5 class="modal-title" id="shippingModalLabel">案件No.{{ $transactions[0]['contact_id'] }}：{{ $transactions[0]['fc_name'] }}への送料を入力<span id='shipping-number'></span></h5>
          @else
          <h5 class="modal-title" id="shippingModalLabel">{{ $transactions[0]['fc_name'] }}への送料を入力<span id='shipping-number'></span></h5>
          @endif
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type='hidden' id='contact-id' value="{{ $transactions[0]['contact_id'] }}" name='contact_id'>
          <input type='hidden' id='transaction-id' value="{{ $id }}" name='transaction_id'>
          <div class="form-group mb20 shipping-cost-group">
            <label class='f12'>送料（税抜き）</label>
            <input type="number" class="form-control" placeholder="1200" name='shipping_cost' dusk='create-cost' value="{{ !is_null($transactions[0]['shipping_cost']) ? $transactions[0]['shipping_cost'] : '' }}">
          </div>
          <div class="form-group mb20">
            <label class='f12'>配送業者</label>
            <select class="form-control" name='shipping_id' dusk='create-shipping-company' required>
              <option value="">選択してください</option>
              @foreach($deliveries AS $d)
              <option value="{{ $d->id }}">{{ $d->transport_company }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>発送日</label></p>
            <input type="datetime" class="datepicker form-control" name='shipping_date' placeholder="発送日を入力" autocomplete="off" dusk='create-shipping-date' required />
          </div>
          <div class="form-group mb10">
            <label class='f12'>問い合わせ番号</label>
            <input type="text" class="form-control" placeholder="123412341234（複数番号はカンマ区切り）" name='shipping_number' autocomplete="off" dusk='create-number' required />
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>個別連絡事項（メールに記載されます）</label></p>
            <textarea class='form-control' name='dispatch_message' dusk='create-message'></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button class="btn btn-primary" dusk='create-cost-submit'>送料確定と発送連絡（FCに自動通知）</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endif
<!-- 全額前金送料入力Modal -->
@if(!empty($transactions[0]))
<div class="modal fade" id="input-shipping-cost-modal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
  <form action="{{ route('input.shipping-cost') }}" method="post">
    @csrf
    <!-- メール用データ -->
    <input type='hidden' name='userid' value="{{ $userdata[0]['id'] }}" />
    <input type='hidden' name='m[transaction_id]' value="{{ $transactions[0]['id'] }}" />
    <input type='hidden' name='m[transport_company]' value="" />
    <input type='hidden' name='m[trakking_url]' value="" />
    <!-- FC顧客への発送か確認するため用 -->
    <input type='hidden' name='direct_shipping' value="{{ $transactions_table['direct_shipping'] }}" />
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          @if(!empty($transactions[0]['contact_id']))
          <h5 class="modal-title" id="shippingModalLabel">案件No.{{ $transactions[0]['contact_id'] }}：{{ $transactions[0]['fc_name'] }}への送料を入力</h5>
          @else
          <h5 class="modal-title" id="shippingModalLabel">{{ $transactions[0]['fc_name'] }}への送料を入力</h5>
          @endif
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type='hidden' id='contact-id' value="{{ $transactions[0]['contact_id'] }}" name='contact_id'>
          <input type='hidden' id='transaction-id' value="{{ $id }}" name='transaction_id'>
          <div class="form-group mb20">
            <label class='f12'>送料（税抜き）</label>
            <input type="number" class="form-control" placeholder="1200" name='shipping_cost' dusk='shipping-cost' value="{{ !empty($transactions[0]['shipping_cost']) ? $transactions[0]['shipping_cost'] : 0}}">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
            <button class="btn btn-primary" dusk='shipping-cost-submit'>送料確定</button>
          </div>
        </div>
      </div>
  </form>
</div>
@endif
@endsection