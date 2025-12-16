@extends('layouts.layout')

@section('css')
<link href="{{ asset('') }}" type="text/css" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/transaction/payment-invoice.js?20210812') }}" defer></script>
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col"><span class="common-table-stripes-row-thead__th-span">問い合わせ日時</span></th>
      <th scope="col">顧客名</th>
      <th scope="col">金額</th>
      <th scope="col">案件詳細</th>
      <th scope="col">発注書No</th>
      <th scope="col">発注書</th>
      <th scope="col">請求書払い</th>
      <th scope="col">クレカ払い</th>
    </tr>
  </thead>
  <tbody>
@foreach($transactions as $t)
    <tr>
      <td class='js-contact-id bold f12'>{{ $t->id }}</td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span">{{date('Y年m月d日', strtotime($t->created_at))}}</span></th>
      <td class="common-table-stripes-row-tbody__td">{{$t->surname}}{{$t->name}}</td>
      <td class="common-table-stripes-row-tbody__td">{{number_format($t->total + $t->shipping_cost) }}円</td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('contact.show', ['id' => $t->id]) }}" class="btn btn-info px-3" dusk='detail-button'>確認</a>
      </td>
      <td class="common-table-stripes-row-tbody__td">{{ $t->transaction_id }}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('transactions.show', ['id' => $t->transaction_id]) }}" class="btn btn-primary px-3" dusk='transaction-show'>見る</a>
      </td>
      <td class="common-table-stripes-row-tbody__td">
        <form id="form{{$t->transaction_id}}" action="{{ route('transaction.payment.invoice.store', ['contactId' => $t->contact_id, 'transactionId' => $t->transaction_id]) }}" method="POST">
          @csrf
          <button class="btn btn-warning px-3" dusk='transaction-invoice-pay' onclick="return confirm('請求書払いで部材代金を処理します。よろしいですか？')">実行</button>
        </form>
      </td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('transaction.payment.show', ['id' => $t->transaction_id]) }}" class="btn btn-warning px-3" dusk='transaction-show'>実行</a>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
@foreach($transactions as $t)
@endforeach
@endsection
