@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('styles/quotation/create.min.css?20210812') }}" rel="stylesheet" />
<link href="{{ asset('styles/transaction/create.min.css?20210812') }}" rel="stylesheet" />
<script src="{{ asset('plugins/popper/popper.min.js') }}" defer></script>
@endsection

@section('javascript')
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/canvas2/canvas2.js') }}" defer></script>
<script src="{{ asset('js/common.js?20210812') }}" defer></script>
<script src="{{ asset('js/transaction/helper.js?20240206') }}" defer></script>
<script>
  window.products = @json($products);
  window.turfs = @json($turfs);
  window.subItems = @json($subItems);
  window.salesItems = @json($salesItems);
  window.cutItems = @json($cutItems);
  window.pt =@json(old('pt') ? old('pt') : []);
  window.discount = 0;
  window.subTotal = 0;
  window.totalArea = 0;
  window.transportState = 0; // 最短お届け営業日
  window.user = @json(\Auth::user());
  window.contact = @json($contact);
  window.appEnv = "{{ \App::environment() }}";
  window.officeHoliday = @json($office_holiday);
  window.addressDataNotnull = `{{ !empty($_GET['address']) ? $_GET['address'] : $user->s_pref.$user->s_city.$user->s_street }}`;
  window.addressDataNull = `{{ !empty($_GET['address']) ? $_GET['address'] : $user->pref.$user->city.$user->street}}`;
  window.factoryAddress = `{{config('app.factory_address')}}`
  window.registration = `{{ $registration }}` //案件に紐付いた発注であればtrue
  window.isFactoryPickUp = false;
  window.halfPayment = false;
  window.samples = @json($samplesArr);
  window.alertState = true; //連続でアラートが出るのを防ぐ管理用変数
  window.taxRate = {{ config('app.tax_rate') }};
  window.editFlg = false;
  window.cutTurfInvisibleIds = @json($cut_turf_invisible_ids);
  window.shippingPriceTable = @json($shipping_price_table);
  window.freeShippingItemIds = @json($free_shipping_item_ids);
  console.log(window.freeShippingItemIds);
@if(!empty($editflg))
  window.editFlg = true;
  console.log('editFlg');
  window.addressDataEdit = `{{ $transactions[0]['address'] }}`; 
  window.spDiscount = `{{$transactions[0]['special_discount']}}`;
  window.dbDiscount = `{{$transactions[0]['discount']}}`;
  window.prepaid = `{{$transactions[0]['prepaid']}}`;
  window.transactions = @json($transactions);
  window.editTurf = @json($product_transactions['turf']);
@endif
</script>
@endsection

@section('footer')
<script src="{{ asset('js/transaction/create.js?20240624') }}" defer></script>
@endsection

@section('content')
@if(!empty(config('app.transaction_alert')))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
  <strong>{!! config('app.transaction_alert') !!}</strong>
</div>
@endif
@if(empty($contact->id) && empty($editflg))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
  <strong class='f12 mb-1 d-block'>最短納品日での発注は案件登録が必要です！</strong>
  <p class='f10 mb-2'>この発注ページは案件に紐付かない資材発注ページです</p>
  <button class="btn btn-warning" type="button" data-toggle="collapse" data-target="#collapseRegister" aria-expanded="false" aria-controls="collapseExample">案件登録方法はこちらから確認</button>
</div>
<div class="collapse mb-2 px-2" id="collapseRegister">
  <p class='mb-1'>PDFマニュアルのダウンロードは<a href="https://drive.google.com/file/d/1dyv27I5rlFxhawPA-pUG8_6nwitDh2dz/view?usp=sharing" target="blank">こちら</a>から</p>
  <img src='/images/register.png' class='w60'/>
</div>
@endif
@if($transaction_count != 0)
  <div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
  <strong>追加発注分の発注書です。</strong>
</div>
@endif
<div class='d-flex justify-content-between mb10'>
  {!! $breadcrumbs->render() !!}
  <div class='d-flex'>
    <button class='btn btn-danger mr-2' type='button' onclick="location.reload(true);" data-toggle="tooltip-nonononono" data-placement="left" title="ブラウザキャッシュを削除するボタンです。しばらく利用しなかった際には一度クリックしてください。">キャッシュを削除</button>
  </div>
</div>
{{-- 案件に紐付かない資材発注からかつ、見積書作成済の案件がある時だけ --}}
@if(empty($contact['id']) && !empty($quotation_contacts) && empty($editflg))
<div class='d-flex justify-content-between mb10'>
  <div class="alert alert-warning alert-block w40 mr-3">
    <p>案件の登録がある場合は選択</p>
    <p>※見積書作成済案件に限る</p>
  </div>
  <select id="contact-select" class="form-control" name="t[contact_id]" dusk="contact-select">
    <option class="form-control" value="">紐付けなし</option>
  @foreach($quotation_contacts as $qc)
    <option class="form-control" value="{{$qc['id']}}" {{ !empty($_GET['after_contact_id']) ? selected($qc['id'] == $_GET['after_contact_id']) : ''}}>
      案件No.{{$qc['id'] . ' ' . customerName($qc). ' 依頼日' . date('Y年m月d日', strtotime( !empty($qc->fc_assigned_at) ? $qc->fc_assigned_at : $qc->created_at))}}
    </option>
  @endforeach
  </select>
</div>
@endif
<!-- 発注商品項目テーブル -->
<div id="canvas" class="card bg-white p-5 mb40">
  <h2 class='h2 text-center mb30'>ご発注 依頼書</h2>
  <form id="transactionForm" class='quotation-form br5' action="{{ strpos(url()->current(),'edit') === false ?route('transaction.comfirm') : route('transaction.update', ['id' => $transactionId])  }}" method="POST">
    @csrf
  @if(!empty($_GET['after_contact_id']))
    <input id="contact-id" type='hidden' name='contact_id' value="{{ $_GET['after_contact_id'] }}" />
  @else
    <input id="contact-id" type='hidden' name='contact_id' value="{{ !empty($contact->id) ? $contact->id : null }}" />
  @endif
    <input name="t[direct_shipping]" id="direct_shipping" class="mt-1" type="hidden" value="0">
    <div class='contact-wrapper'>
      <table class="table table-bordered">
  @if(!empty($contact->id))
        <tr class='f10'>
          <th>工事名称</th><td>案件No.{{ !empty($_GET['contact_id']) ? $_GET['contact_id'] : $contact->id }} {{ customerName($contact) }} <br> {{$contact->pref.$contact->city. $contact->street }}</td>
        </tr>
  @endif
        <tr>  
          <th class="w35"><p class='f11'>受け取り場所</p><br>
  @if(!empty($editflg))
            @include('fc.transaction.address-type')
          </th>
          <td><textarea id="receiving-address-edit" class="form-control w100 resize-none" name="t[address]"{{ ($transactions[0]['address'] == config('app.factory_address') ) ? 'readonly' : ''  }} dusk='receive' readonly>{{ !empty($_GET['address']) ? $_GET['address'] : $transactions[0]['address'] }}</textarea></td>
  @elseif(!empty($user->s_pref))
            @include('fc.transaction.address-type')
          </th>
          <td><textarea id="receiving-address-edit" class="form-control w100 resize-none" name="t[address]" dusk='receive' readonly>{{ !empty($_GET['address']) ? $_GET['address'] : '' }}</textarea></td>
  @else
            @include('fc.transaction.address-type')
          </th>
          <td><textarea id="receiving-address-null" class="form-control w100 resize-none" name="t[address]" dusk='receive' rows="3" readonly>{{ !empty($_GET['address']) ? $_GET['address'] : ''}}</textarea></td>
  @endif
        </tr>
        <tr>
          <th><p class='f11'>荷受人</p></th>
  @if(!empty($editflg))
          <td><textarea id="receiving-name" class="form-control w100 resize-none" name="t[consignee]" disk="receive-name" rows="2">{{ $transactions[0]['consignee'] }}</textarea></td>
  @else
          <td><textarea id="receiving-name" class="form-control w100 resize-none" name="t[consignee]" dusk="receive-name" rows="2">{{ !empty($_GET['consignee']) ? $_GET['consignee'] : '' }}</textarea></td>
  @endif
        </tr>
        <tr>
  @if(!empty($editflg))
          <th><p class='f11'>荷受け人様連絡先TEL</p></th><td><input id="receiving-tel" class="form-control w70" name="t[tel]" value="{{ $transactions[0]['transaction_tel'] }}" dusk="tel"></td>
  @else
          <th><p class='f11'>荷受け人様連絡先TEL</p></th><td><input id="receiving-tel" class="form-control w70" name="t[tel]" value="{{ !empty($_GET['tel']) ? $_GET['tel'] : '' }}" dusk="tel"></td>
  @endif
        </tr>
        <tr>
          <th><p class='f11'>支払い方法</p></th>
          <!-- 編集時のチェック切り替えはJSで実装 -->
          <td>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="t[prepaid]" id="prepaid0" value="0" {{checked(!empty($_GET['prepaid']) ? $_GET['prepaid']=='0' : true) }} {{$user->require_prepaid === 1 ? 'disabled' : ''}}>
              <label class="form-check-label pointer" for="prepaid0" dusk='prepaid0'>月末請求書払い（通常支払い）</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="t[prepaid]" id="prepaid1" value="1" {{checked(!empty($_GET['prepaid']) ? $_GET['prepaid']=='1' : false) }} {{$user->require_prepaid === 1 ? 'disabled' : ''}}>
              <label class="form-check-label pointer" for="prepaid1" dusk='prepaid1'>半額前払い</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="t[prepaid]" id="prepaid2" value="2" {{checked(!empty($_GET['prepaid']) ? $_GET['prepaid']=='2' : false) }} {{$user->require_prepaid === 1 ? 'checked' : ''}}>
              <label class="form-check-label pointer" for="prepaid2" dusk='prepaid2'>全額前払い</label>
            </div>
          </td>
        </tr>
        <tr>
  @if(!empty($editflg))
          <th><p class='f11'>その他備考</p></th><td><textarea class="form-control" name="t[memo]" dusk='memo'>{{ $transactions[0]['transaction_memo'] }}</textarea></td>
  @else
          <th><p class='f11'>その他備考</p></th><td><textarea class="form-control" name="t[memo]" dusk='memo'>{{ !empty($_GET['memo']) ? $_GET['memo'] : '' }}</textarea></td>
  @endif
        </tr>
      </table>

      <p class='small mb20'>下記の通り発注申し上げます。</p>
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
            <td id="subTotal"></td>
            <td id='tax'></td>
            <td id='total' class="total"></td>
          </tr>
        </tbody>
      </table>
    </div> <!--  class='contact-wrapper'> -->

    <div class='d-flex justify-content-end mb10'>
@if(!empty($contact->quotation_id))
      <button class='btn btn-primary mr-2' type='button' id="convert-quotation">見積書の内容を発注書に変換</button>
      <a class='btn btn-info d-flex align-items-center' href="{{ route('quotations.show', ['id' => $contact->quotation_id]) }}" target='blank'>見積書を確認</a>
@endif
    </div> <!--  class='contact-wrapper'> -->
    <div class='culc-wrapper p20 mb20'>
      <input type="hidden" name="t[contact_id]" value="{{ !empty($contact->id) ? $contact->id : '' }}">
      <input type="hidden" name="t[sub_total]" value="">
      <input type="hidden" name="t[total]" value="">
      <h4 class='h4'>人工芝反物</h4>
      <table class="table table-responsive-md text-center" id="turf-table">
        <thead>
          <tr>
            <th class="text-center "></th>
            <th class="text-center">詳細</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='product-body'>
        </tbody>
      </table>
      <button type='button' id='js-turf-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>人工芝反物行を追加</button>

      <h4 class='h4'>人工芝切り売り</h4>
      <table class="table table-responsive-md text-center" id="cut-turf-table">
        <thead>
          <tr>
            <th class="text-center"></th>
            <th class="text-center">詳細</th>
            <th class="text-center">幅（m)</th>
            <th class="text-center">長さ（m)</th>
            <th class="text-center">面積（㎡）</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='product-cut-body'>
        </tbody>
      </table>
      <button type='button' id='js-cut-turf-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>人工芝切り売り行を追加</button>

      <h4 class='h4'>副資材まとめ売り</h4>
      <table class="table table-responsive-md text-center">
        <thead>
          <tr>
            <th class="text-center"></th>
            <th class="text-center">詳細</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='sub-body'>
        </tbody>
      </table>
      <button type='button' id='js-sub-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>副資材まとめ売り行を追加</button>

      <h4 class='h4 d-none'>副資材バラ売り</h4>
      <table class="table table-responsive-md text-center js-cut-sub-area d-none">
        <thead>
          <tr>
            <th class="text-center"></th>
            <th class="text-center">詳細</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='cut-sub-body'>
        </tbody>
      </table>
      <br>
      <button type='button' id='js-cut-sub-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>副資材バラ売り行を追加</button>

      <h4 class='h4 js-sales-area d-none'>販促物</h4>
      <table class="table table-responsive-md text-center js-sales-area d-none">
        <thead>
          <tr>
            <th class="text-center"></th>
            <th class="text-center">詳細</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='sales-body'>
        </tbody>
      </table>
      <img id='sales-image' src="" class="sales-image">
      <br>
      <button type='button' id='js-sales-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>販促物行を追加</button>

      <h4 class='h4 js-etc-area d-none'>その他メニューにない商品</h4>
      <table class="table table-responsive-md text-center d-none js-etc-area">
        <thead>
          <tr>
            <th class="text-center"></th>
            <th class="text-center">注文名</th>
            <th class="text-center">数量</th>
            <th class="text-center">単位</th>
            <th class="text-center">単価(円)</th>
            <th class="text-center">金額</th>
            <th class="text-center"></th>
          </tr>
        </thead>
        <tbody id='etc-body'>
        </tbody>
      </table>
      <br>
      <button type='button' id='js-etc-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>その他を追加</button>

      {{-- <div id="test3" class="alert alert-secondary"></div>
      <div id="test" class="alert alert-primary"></div>
      <div id="test2" class="alert alert-danger"></div> --}}
      <h4 class='h4'>送料<span class='f08 ml-2'>*送料は状況により上下することがありますので、予めご了承ください</span></h4>
      {{-- <p class="mb20">{{ isAdmin() ? '発送連絡の際に入力してください。' : '送料は発注後、本部が入力します。'}}</p> --}}
      <section class='d-flex justify-content-start'>
        <p id='price-wrapper' class='d-flex mb20 justify-content-start align-items-center'><input type='number' name="t[shipping_cost]" id='shipping-cost' class="form-control mb20" dusk="shipping-cost" value="{{$transactions[0]['shipping_cost'] ?? 0}}" {{ isAdmin() ? '' : 'readonly'}}/>円</p>
        <div id='charter-message' class='alert alert-warning f10 d-none'>※チャーター便料金は目安になります。納期の件も含め本部までご相談ください。<br>【都度お見積り】</div>
        
        <button class='btn btn-primary ml-5 mb30' type='button' id='js-calc'>送料を計算する</button>
      </section>
    </div> <!-- culc-wrapper -->
    <section id='delivery-wrapper' class='mb20'>
      <h4>納品希望日</h4>
  @if(!empty($editflg))
      <input data-provide="datepicker" class="form-control datepicker js-date w30per pointer" type="datetime" name="t[delivery_at]" value="{{ $transactions[0]['delivery_at'] }}" dusk='delivery' autocomplete="off" {{ isAdmin() ? '' : 'readonly'}} placeholder='クリックして納品希望日を入力'/>
  @else
      <input data-provide="datepicker" class="form-control datepicker js-date w30per pointer" type="datetime" name="t[delivery_at]" value="{{ !empty($_GET['delivery_at']) ? $_GET['delivery_at'] : '' }}" dusk='delivery' autocomplete="off" placeholder='クリックして納品希望日を入力'/>
  @endif
  @if(!empty($editflg) && !empty($transactions[0]['delivery_at2']))
      <h4 class='mt-3'>納品希望日2</h4>
      <input data-provide="datepicker" class="form-control datepicker2 js-date w30per pointer" type="datetime" name="t[delivery_at2]" value="{{ $transactions[0]['delivery_at2'] }}" dusk='delivery2' autocomplete="off" {{ isAdmin() ? '' : 'readonly'}} placeholder='先に第1納品希望日を入力してください'/>
  @else
      <h4 class='d-none mt-3'>納品希望日2</h4>
      <input data-provide="datepicker" class="d-none form-control datepicker2 js-date w30per pointer" type="datetime" name="t[delivery_at2]" value="{{ !empty($_GET['delivery_at2']) ? $_GET['delivery_at2'] : '' }}" dusk='delivery2' autocomplete="off" placeholder='先に第1納品希望日を入力してください'/>
  @endif
  @if(!empty($editflg) && !empty($transactions[0]['delivery_at3']))
      <h4 class='mt-3'>納品希望日3</h4>
      <input data-provide="datepicker" class="form-control datepicker3 js-date w30per pointer" type="datetime" name="t[delivery_at3]" value="{{ $transactions[0]['delivery_at3'] }}" dusk='delivery2' autocomplete="off" {{ isAdmin() ? '' : 'readonly'}} placeholder='先に第2納品希望日を入力してください'/>
  @else
      <h4 class='d-none mt-3'>納品希望日3</h4>
      <input data-provide="datepicker" class="d-none form-control datepicker3 js-date w30per pointer" type="datetime" name="t[delivery_at3]" value="{{ !empty($_GET['delivery_at3']) ? $_GET['delivery_at3'] : '' }}" dusk='delivery3' autocomplete="off" placeholder='先に第2納品希望日を入力してください'/>
  @endif
    </section>
    <!-- FCには見せない値引き -->
  @if(isFc())
    <input type='hidden' class="form-control" id='discount' name='t[discount]' value="{{ !empty($transactions[0]['discount']) ? $transactions[0]['discount'] : 0}}">
    <input type='hidden' class="form-control" id='special-discount' name='t[special_discount]' value="{{ !empty($transactions[0]['special_discount']) ? $transactions[0]['special_discount'] : 0}}">
  @endif
    <p class='text-right mb-1'>*送料の計算をしてから次に進んでください</p>
    <div class='d-flex align-items-center justify-content-end'>
  @if(empty($editflg))
      <button type='button' class='skip-btn btn btn-lg btn-warning mr20' dusk='skip'>この案件は資材を発注しない</button>
  @else
      <input type='hidden' name='t[user_id]' value="{{ !empty($transactions[0]['transaction_user_id']) ? $transactions[0]['transaction_user_id'] : ''}}">
  @endif
      <input type="hidden" id="transport-state" name='transport_state'>
      <input type="submit" class="btn btn-lg btn-primary" value="{{ strpos(url()->current(),'edit') === false ? '発注内容を確認' : '発注書を更新'  }}" dusk='post' id="post-transaction">
    </div>
  </form>
</div><!-- card -->

<!-- 見積もり項目テーブル -->

@endsection
