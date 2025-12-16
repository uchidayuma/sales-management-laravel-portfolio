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
<form id="submit_form" class="text-right"action="{{ route('transactions.admin.dispatched') }}">
  <strong class="mr-3 h5">並び順変更</strong>
  <select class="custom-select w20 my-1" id="submit_select_delivery_date" name="orderType" dusk='order-select'>
    <option value="descendingDeliveryDate" {{ !empty($orderType) &&  $orderType == 'descendingDeliveryDate' ? 'selected' : '' }}>発送日 降順 (新しい順)</option>
    <option value="ascendingDeliveryDate" {{ !empty($orderType) && $orderType ==  'ascendingDeliveryDate' ? 'selected' : '' }}>発送日 昇順 (古い順)</option>
    <option value="descendingDeliveryPreferredDate" {{ !empty($orderType) &&  $orderType == 'descendingDeliveryPreferredDate' ? 'selected' : '' }}>納品希望日 降順 (新しい順)</option>
    <option value="ascendingDeliveryPreferredDate" {{ !empty($orderType) && $orderType ==  'ascendingDeliveryPreferredDate' ? 'selected' : '' }}>納品希望日 昇順 (古い順)</option>
  </select>
</form>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件<br>No</th>
      <th scope="col">発注書<br>No</th>
      <th scope="col">PDF</th>
      <th scope="col" nowrap>
        <a class="text-body" href="./dispatched?orderType={{ sortOderType($orderType,'ascendingDeliveryDate','descendingDeliveryDate') }}">発送日{!! sortOderIcon($orderType, 'ascendingDeliveryDate') !!}</a>
      </th>
      <th scope="col" nowrap>
        <a class="text-body" href="./dispatched?orderType={{ sortOderType($orderType,'ascendingDeliveryPreferredDate','descendingDeliveryPreferredDate') }}">納品希望日{!! sortOderIcon($orderType, 'ascendingDeliveryPreferredDate') !!}</a>
      </th>
      <th scope="col" class="keep-all">担当FC</th>
      <th scope="col">顧客名</th>
      <th scope="col">金額</th>
      <th scope="col">削除</th>
    </tr>
  </thead>
  <tbody>
@foreach($transactions as $key => $t)
<tr class="{{ $t->transactions_status != 1 ? 'bg-danger' : '' }}" dusk="row{{ $key }}">
  @if(!empty($t->contact_id))
      <td class='js-contact-id' id="{{ $t->transaction_id}}"><a href="{{ route('contact.show', ['id' => $t->id]) }}" dusk='detail-button'>{{ displayContactId($t) }}</a></td>
  @else
      <td class='js-contact-id' id="{{ $t->transaction_id }}"><p>対応案件なし</p></td>
  @endif
      <td scope="row" class='js-transaction-id' id="{{ $t->transaction_id }}"><a href="{{ route('transactions.show', ['id' => $t->transaction_id]) }}" dusk="transaction_id-{{ $t->transaction_id }}">{{ $t->transaction_id }}</a></td>
      <td class=''><a class='btn btn-primary' href="{{ route('transactions.download', ['id' => $t->transaction_id]) }}" target="blank">印刷</a></td>
      <td class="common-table-stripes-row-tbody__td keep-all">{{ !empty($t->shipping_date) ? date('Y年m月d日', strtotime($t->shipping_date)) : '' }}</td>
      <td class="common-table-stripes-row-tbody__td keep-all">{{ date('Y年m月d日', strtotime($t->delivery_at)) }}</td>
      <td class="common-table-stripes-row-tbody__td js-fc keep-all">{{ $t->fc_name }}</td>
      <td class="common-table-stripes-row-tbody__td keep-all">{{ isCompany($t) ? $t->company_name . ' ' . $t->surname .$t->name : $t->surname .$t->name }}</td>
      <td class="common-table-stripes-row-tbody__td keep-all">{{ number_format($t->total + $t->shipping_cost) }}円</td>
      <td class="common-table-stripes-row-tbody__td">
        <button type="button" dusk="select-modal{{$t->id}}" class="btn btn-danger px-2 delete-btn">削除</button>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
{{ $transactions->appends(request()->query())->links() }}
<form id='delete-form' action="{{ route('transactions.destroy') }}" method="post" enctype="multipart/form-data">
  <input id='delete-id' type='hidden' name='transaction_id' value=''/>
  @csrf
</form>
@endsection
