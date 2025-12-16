@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
@endsection

@section('javascript')
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/transaction/dispatch.js') }}" defer></script>
<script src="{{ asset('js/transaction/delete.js') }}" defer></script>
@endsection

@section('content')
<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">発注書No</th>
      <th scope="col"><span class="common-table-stripes-row-thead__th-span">発注日</span></th>
      <th scope="col">納品希望日</th>
      <th scope="col">名前</th>
      <th scope="col">納品先住所</th>
      <th scope="col">発送連絡</th>
    </tr>
  </thead>
  <tbody>
<!-- 通常発注 -->
@foreach($contacts as $c)
    <tr>
  @if(!empty($c['contact_id']))
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $c['id']]) }}" dusk='detail-button'>{{ displayContactId($c) }}</a></td>
  @else
      <td class='js-contact-id'>対応案件なし</td>
  @endif
      <td class="common-table-stripes-row-tbody__td">
        <a class="" href="{{ route('transactions.show', ['id' => $c['transaction_id']]) }}" dusk="{{ !empty($c['step_id']) ? 'show-detail' : 'show-detail-only' }}">{{ $c['transaction_id'] }}</a>
      </td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span p0">{{date('Y年m月d日', strtotime($c['transaction_created_at']))}}</span></th>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span p0">{{date('Y年m月d日', strtotime($c['delivery_at']))}}</span></th>
      <td class="common-table-stripes-row-tbody__td">{{ !empty($c['consignee']) ? $c['consignee'] : $c['surname'] .$c['name'] }}</td>
      <td class="common-table-stripes-row-tbody__td">{{ !empty($c['address']) ? $c['address'] : $c['user_pref'] . $c['user_city'] . $c['user_street'] }}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a class="btn btn-primary" 
            href="{{ route('transactions.show', ['id' => $c['transaction_id']]) }}"
            id="{{ 'input-' . $c['transaction_id'] }}"
            dusk="{{ !empty($c['step_id']) ? 'show-detail' : 'show-detail-only' }}"
        >
        入力</a>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
 <!-- 本部見積もり案件 -->
<div class="breadcrumbs">
  <ul itemscope="" itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs">
    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class=""><a itemprop="item" href="/transactions"><span itemprop="name"><i class="fas fa-hourglass"></i>発注</span></a><meta itemprop="position" content="1"> <span class="divider">&gt;</span></li>
    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class=" active"><span itemprop="name">顧客発送待ち案件一覧</span><meta itemprop="position" content="2"></li>
  </ul>
</div>
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">発注書No or 見積書No</th>
      <th scope="col"><span class="common-table-stripes-row-thead__th-span">発注日</span></th>
      <th scope="col">納品希望日</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">発送連絡</th>
    </tr>
  </thead>
  <tbody>
<!-- 本部が見積もりして、商談成立した顧客 -->
@foreach($customers as $c)
    <tr>
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $c['contact_id']]) }}" dusk='detail-button'>{{ $c['contact_id'] }}</a></td>
      <td class='common-table-stripes-row-tbody__td'><a href="{{ route('quotations.show', ['id' => $c['quotation_id'] ]) }}" target="blank">見積もり書No.{{$c['quotation_id'] }}<i class="fas fa-external-link-alt ml5"></i></a></td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span">{{!is_null($c['contracted_at']) ? date('Y年m月d日', strtotime($c['contracted_at'])) : ''}}</span></th>
      <td class="common-table-stripes-row-tbody__td"></td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{ $c['pref'] . $c['city'] . $c['street'] }}</td>
      <td class="common-table-stripes-row-tbody__td">
        <button class="btn btn-primary modal-open-btn" type='button'  data-toggle="modal" data-target="#shipping-modal"  dusk="admin-dispatch{{$c['id']}}">入力</button>
      </td>
    </tr>
@endforeach
<!-- FCが見積もりして、発注書を作成した顧客 -->
@foreach($customersFc as $fc)
    <tr>
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $fc['id']]) }}" dusk='detail-button'>{{ displayContactId($fc) }}</a></td>
      <td class='common-table-stripes-row-tbody__td'><a href="{{ route('transactions.show', ['id' => $fc['transaction_id']]) }}" target="blank">発注書No.{{$fc['transaction_id'] }}<i class="fas fa-external-link-alt ml5"></i></a></td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span">{{!is_null($fc['transaction_created_at']) ? date('Y年m月d日', strtotime($fc['transaction_created_at'])) : ''}}</span></th>
      <td class="common-table-stripes-row-tbody__td">
        {{date('Y年m月d日', strtotime($fc['delivery_at']))}}
        {!! !is_null($fc['delivery_at2']) ? '<br>' . date('Y年m月d日', strtotime($fc['delivery_at2'])) : '' !!}
        {!! !is_null($fc['delivery_at3']) ? '<br>' . date('Y年m月d日', strtotime($fc['delivery_at3'])) : '' !!}
      </td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($fc) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{ $fc['pref'] . $fc['city'] . $fc['street'] }}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a  class="btn btn-primary modal-open-btn" href="{{ route('transactions.show', ['id' => $fc['transaction_id']]) }}" dusk='fc-dispatch'>入力</a>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
<!-- 発送Modal -->
<div class="modal fade" id="shipping-modal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
  <form action="{{ route('dispatch.store') }}" method="post">
    @csrf
    <!-- FC顧客への発送か確認するため用 このモーダルから発送連絡する際は0しか存在しないのでデフォルトで0を設定 -->
    <input type='hidden' name='direct_shipping' value='0' />
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="shippingModalLabel">案件No.<span id='modal-no'></span>：<span id='modal-customer'></span>への発送事項を入力</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type='hidden' id='contact-id' value='' name='contact_id'>
          <input type='hidden' id=''>
          <div class="form-group">
            <label class='f12'>配送業者</label>
            <select class="form-control" name='shipping_id' dusk='shipping-company'>
        @foreach($deliveries AS $d)
              <option value="{{ $d->id }}">{{ $d['transport_company'] }}</option>
        @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class='f12'>問い合わせ番号</label>
            <input type="text" class="form-control" placeholder="1111,1111,1111（複数入力はカンマで区切る）" name='shipping_number' dusk='number'>
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>発送日</label></p>
            <input type="datetime" class="datepicker form-control" name='shipping_date' placeholder="発送日を入力" dusk='shipping-date' autocomplete='off' required/>
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>個別連絡事項（メールに記載されます）</label></p>
            <textarea class='form-control' name='dispatch_message' dusk='create-message'></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button class="btn btn-primary" onclick="return confirm('顧客に発送連絡を送信してよろしいですか？')" dusk='admin-submit'>発送連絡を確定</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
