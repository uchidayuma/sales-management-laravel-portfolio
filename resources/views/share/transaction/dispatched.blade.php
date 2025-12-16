@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script>
var dispatches = @json($dispatches);
window.dispatches = dispatches.data;
console.log(window.dispatches);
</script>
<script src="{{ asset('js/transaction/dispatched.js') }}" defer></script>
@endsection

@section('content')

<div class="breadcrumbs d-none d-sm-block">
  {!! $breadcrumbs->render() !!}
</div>
{{-- PCのみ表示 --}}
<div class="d-none d-sm-block">
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">発注書No</th>
      <th scope="col">発送日</th>
      <th scope="col" {{ adminOnlyHidden() }}>担当FC</th>
      <th scope="col">問い合わせ日時</th>
      <th scope="col">納品希望日</th>
      <th scope="col">顧客名</th>
      <th scope="col">住所</th>
      <th scope="col">発送連絡</th>
    </tr>
  </thead>
  <tbody>
@foreach($dispatches as $d)
    <tr>
  @if(!empty($d->id))
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk='detail-button'>{{ displayContactId($d) }}</a></td>
  @else
      <td class='js-contact-id'>対応案件なし</td>
  @endif
      <td class='js-transaction-id'><a href="{{ route('transactions.show', ['id' => $d->transaction_id]) }}" dusk='detail-button'>{{ $d->transaction_id }}</a></td>
  @if(!empty($d->shipping_date))
      <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($d->shipping_date)) }}</td>
  @else
      <td class="common-table-stripes-row-tbody__td">未発送</td>
  @endif
      <td class="common-table-stripes-row-tbody__td js-fc" {{ adminOnlyHidden() }}>{{ $d->fc_name }}</td>
  @if(!empty($d->id))
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($d->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($d->delivery_at)) }}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($d) }}</td>
  @else
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($d->transaction_created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($d->delivery_at)) }}</td>
      <td class="common-table-stripes-row-tbody__td">対応案件なし</td>
  @endif
      <td class="common-table-stripes-row-tbody__td">{{$d->pref}}{{$d->city}}{{$d->street}}</td>
      <td class="common-table-stripes-row-tbody__td">
        <button type="button" class="btn btn-primary modal-open-btn" dusk="modal-show-{{$d->transaction_id}}" data-toggle="modal" data-target="#shipping-modal" shipping-number="{{ $d->shipping_number}}" transport-company="{{ $d->transport_company}}" trakking-url="{{ $d->trakking_url }}" shipping-date="{{ date('Y年m月d日', strtotime($d->shipping_date)) }}" contact-no="{{ $d->id }}" transaction-id="{{ $d->transaction_id}}">確認</button>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
</div>
{{-- スマホ用 リスト作成 --}}
<div class="d-block d-sm-none">
  @foreach($dispatches as $d)
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between my-1">
        <div class="item-left">
          @if(!empty($d->id))
            <p class='js-contact-id'>案　件No.<a href="{{ route('contact.show', ['id' => $d->id]) }}" dusk='detail-button'>{{ displayContactId($d) }}</a></p>
          @else
            <p class='js-contact-id'>案　件No.対応案件なし</p>
          @endif
          <p class='js-transaction-id'>発注書No.<a href="{{ route('transactions.show', ['id' => $d->transaction_id]) }}" dusk='detail-button'>{{ $d->transaction_id }}</a></p>
          <span class="h6">
            @if(!empty($d->shipping_date))
              <p class="mt-2">発送日：{{ date('Y年m月d日', strtotime($d->shipping_date)) }}</p>
            @else
              <p class="mt-2">発送日：未発送</p>
            @endif
              <p class="js-fc" {{ adminOnlyHidden() }}>{{ $d->fc_name }}</p>
            @if(!empty($d->id))
              <p class="js-name">名　前：{{ customerName($d) }}</p>
            @else
              <p>名　前：対応案件なし</p>
            @endif
            @if(!empty($d->id))
              <p class="">住　所：{{$d->pref}}{{$d->city}}{{$d->street}}</p>
            @else
              <p>住　所：対応案件なし</p>
            @endif
          </span>
        </div>
        <div class="item_left pl-3 d-flex align-items-center justify-content-center">
          <button type="button" class="btn btn-primary modal-open-btn" dusk="modal-show-{{$d->transaction_id}}" data-toggle="modal" data-target="#shipping-modal" shipping-number="{{ $d->shipping_number}}" transport-company="{{ $d->transport_company}}" trakking-url="{{ $d->trakking_url }}" shipping-date="{{ date('Y年m月d日', strtotime($d->shipping_date)) }}" contact-no="{{ $d->id }}" transaction-id="{{ $d->transaction_id}}">発送連絡<br>詳細</button>
        </div>
      </li>
    </ul>
  @endforeach
</div>
{{ $dispatches->appends(request()->query())->links() }}
<!-- Modal -->
<div class="modal fade" id="shipping-modal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title d-none" id="shippingModalLabel">案件No.<span id='modal-no'></span>の発送情報</h5>
          <h5 class="modal-title-only d-none" id="shippingModalLabel">発注書No.<span id='t-id'></span>発送情報</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
        <h4 class='text-primary modal-heading1 d-none f11'>1個目の発送情報</h2>
        <p class="mb20 f10">発送日：<span id='shipping-date'></span></p>
        <div class="form-group mb-5">
          <label class='f11'>配送業者</label>
          <p class="form-control mb20" id='transport-company'></p>
          <div class="form-group" id='numbers'>
            <label>問い合わせ番号</label>
          </div>
          <a href="" id="js-trans-link" class="btn btn-primary" target='blank'>荷物を追跡</a>
        </div>
        <h4 class='text-primary second d-none f11'>2個目の発送情報</h2>
        <p class="mb20 second d-none f10">発送日：<span id='shipping-date2'></span></p>
        <div class="form-group second d-none mb-5">
          <label class='f10'>配送業者</label>
          <p class="form-control mb20" id='transport-company2'></p>
          <div class="form-group" id='numbers2'>
            <label>問い合わせ番号</label>
          </div>
          <a href="" id="js-trans-link2" class="btn btn-warning" target='blank'>荷物を追跡</a>
        </div>
        <h4 class='text-primary third d-none f11'>3個目の発送情報</h2>
        <p class="mb20 third d-none f10">発送日：<span id='shipping-date3'></span></p>
        <div class="form-group third d-none mb-5">
          <label class='f10'>配送業者</label>
          <p class="form-control mb20" id='transport-company3'></p>
          <div class="form-group" id='numbers3'>
            <label>問い合わせ番号</label>
          </div>
          <a href="" id="js-trans-link3" class="btn btn-danger" target='blank'>荷物を追跡</a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</div>
@endsection
