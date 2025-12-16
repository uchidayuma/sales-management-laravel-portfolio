@extends('layouts.layout')

@section('css')
<link href="{{ asset('') }}" rel="stylesheet" />
@endsection

@section('javascript')
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<p>{{ $sub_transaction }}</p>
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col"><span class="common-table-stripes-row-thead__th-span">問い合わせ日時</span></th>
  @if(isAdmin())
      <th scope="col">担当FC</th>
  @endif
      <th scope="col">名前</th>
      <th scope="col">金額</th>
      <th scope="col">案件詳細</th>
      <th scope="col">発注書</th>
    </tr>
  </thead>
  <tbody>
@foreach($transactions as $t)
    <tr>
      <td class='js-contact-id bold f12'>{{ $t->id }}</td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span">{{date('Y年m月d日', strtotime($t->created_at))}}</span></th>
  @if(isAdmin())
      <td class="common-table-stripes-row-tbody__td js-fc">{{ $t->fc_name }}</td>
  @endif
      <td class="common-table-stripes-row-tbody__td">{{$t->surname}}{{$t->name}}</td>
      <td class="common-table-stripes-row-tbody__td">{{$t->pref}}{{$t->city}}{{$t->street}}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('contact.show', ['id' => $t->id]) }}" class="btn btn-info px-3" dusk='detail-button'>確認</a>
      </td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('transactions.show', ['id' => $t->transaction_id]) }}" class="btn btn-primary px-3" dusk='transaction-show'>見る</a>
      </td>
    </tr>
@endforeach
  </tbody>
</table>
@endsection
