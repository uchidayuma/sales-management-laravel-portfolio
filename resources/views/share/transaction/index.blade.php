@extends('layouts.layout')

@section('css')
@endsection

@section('javascript')
<script src="{{ asset('js/transaction/index.js') }}" defer></script>
<script src="{{ asset('js/transaction/delete.js') }}" defer></script>
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<!-- 昇降順変更Select -->
<form id="submit_form" class="text-right"action="{{ route('transactions') }}">
  <strong class="mr-3 h5">並び順変更</strong>
  <select class="custom-select w20 my-1" id="submit_select_delivery_date" name="orderType" dusk='order-select'>
    <option value="descendingOrderTime" {{ !empty($orderType) &&  $orderType == 'descendingOrderTime' ? 'selected' : '' }}>発注日時 降順 (新しい順)</option>
    <option value="ascendingOrderTime" {{ !empty($orderType) && $orderType ==  'ascendingOrderTime' ? 'selected' : '' }}>発注日時 昇順 (古い順)</option>
    <option value="descendingDeliveryPreferredDate" {{ !empty($orderType) &&  $orderType == 'descendingDeliveryPreferredDate' ? 'selected' : '' }}>納品希望日 降順 (新しい順)</option>
    <option value="ascendingDeliveryPreferredDate" {{ !empty($orderType) && $orderType ==  'ascendingDeliveryPreferredDate' ? 'selected' : '' }}>納品希望日 昇順 (古い順)</option>
  </select>
</form>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">発注書No</th>
      <th scope="col px-2">
        <a class="text-body" href="./transactions?orderType={{ sortOderType($orderType,'ascendingOrderTime','descendingOrderTime') }}">発注日時{!! sortOderIcon($orderType, 'ascendingOrderTime') !!}</a>
      </th>
      <th scope="col px-2">
      <a class="text-body" href="./transactions?orderType={{ sortOderType($orderType,'ascendingDeliveryPreferredDate','descendingDeliveryPreferredDate') }}">納品希望日{!! sortOderIcon($orderType, 'ascendingDeliveryPreferredDate') !!}</a>
      </th>
  @if(isAdmin())
      <th scope="col">担当FC</th>
  @endif
      <th scope="col">顧客名</th>
      <th scope="col">金額</th>
      <th scope="col">発注書</th>
  @if(isFc())
      <th scope="col">追加発注</th>
  @endif
  @if(isAdmin())
      <th scope="col">削除</th>
  @endif
    </tr>
  </thead>
  <tbody>
@foreach($transactions as $key => $t)
    <tr dusk="row{{ $key }}">
  @if(!empty($t->contact_id))
      <td class='js-contact-id bold f12 a' id="{{ $t->transaction_id}}"><a href="{{ route('contact.show', ['id' => $t->id]) }}" dusk='detail-button'>{{ displayContactId($t) }}</a></td>
  @else
      <td class='js-contact-id' id="{{ $t->transaction_id }}"><p class="f09">対応案件なし</p></td>
  @endif
      <td scope="row" class='js-transaction-id' id="{{ $t->transaction_id }}"><a href="{{ route('transactions.show', ['id' => $t->transaction_id]) }}">{{ $t->transaction_id }}</a></td>
      <td scope="row text-center">{{date('Y年m月d日', strtotime($t->transaction_created_at))}}</td>
      <td scope="row text-center">{{date('Y年m月d日', strtotime( !empty($t->delivery_at) ? $t->delivery_at : $t->transaction_only_delivery_at))}}</td>
    @if(isAdmin())
      <td class="common-table-stripes-row-tbody__td js-fc">{{ $t->fc_name }}</td>
    @endif
      <td class="common-table-stripes-row-tbody__td">{{ isCompany($t) ? $t->company_name . ' ' . $t->surname .$t->name : $t->surname .$t->name }}</td>
      <td class="common-table-stripes-row-tbody__td">{{ number_format( transactionPrice($t) ) }}円</td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('transactions.show', ['id' => $t->transaction_id]) }}" class="btn btn-info px-3" dusk='transaction-show'>確認</a>
      </td>
    @if(isFc())
      <td class="common-table-stripes-row-tbody__td"><a class="btn btn-success" href="{{ route('create.order', ['contactId' => $t->id]) }}" role="button" dusk="contact{{ $t->id }}" {{  subTransactionLimitDate($t->id) ? '':'hidden'}}>追加発注</a></td>
    @endif
    @if(isAdmin())
      <td class="common-table-stripes-row-tbody__td">
        <button type="button" dusk="delete" class="btn btn-danger px-3 open delete-btn">削除</button>
      </td>
      @endif
    </tr>
@endforeach
  </tbody>
</table>
<form id='delete-form' action="{{ route('transactions.destroy') }}" method="post" enctype="multipart/form-data">
  <input id='delete-id' type='hidden' name='transaction_id' value=''/>
  @csrf
</form>
@endsection
