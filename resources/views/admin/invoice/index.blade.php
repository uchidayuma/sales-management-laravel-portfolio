@extends('layouts.layout')

@section('css')
@endsection

@section('javascript')
<script src="{{ asset('js/invoice/index.js') }}" defer></script>
@endsection

@section('content')

<div class='d-flex justify-content-between align-item-center'>
  {!! $breadcrumbs->render() !!}
  <p class='profit f12 mr10 ml-auto mr10 ml-auto lh-lg' style="line-height:3;">売掛金：¥{{ number_format($profit) }}</p>
  <form class='ranking-form' action="{{ route('invoices.index') }}?{{$queryString}}" method='GET'>
    <div class="form-group d-flex">
      <select class="form-control ranking-form-select mr-2" name='year' style="width:120px;">
    @foreach($pastYear AS $y)
        <option value="{{ $y }}" {{selected($y==$year)? 'selected': ''}}>{{ $y }}年</option>
    @endforeach
      </select>
      <select class="form-control ranking-form-select mr-2" name='month' id='month' style="width:100px;">
    @for($i=1;$i<13;$i++)
        <option value="{{$i}}" {{ selected($i==$month) ? 'selected' : '' }}>{{$i}}月</option>
    @endfor
      </select>
      <button class='btn btn-primary'>絞り込む</button>
    </div>
  </form>
</div>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">FCID</th>
      <th scope="col">FC会社名(FC名)</th>
      <th scope="col">発注書No</th>
      {{-- <th scope="col">金額</th> --}}
    </tr>
  </thead>
  <tbody>
@foreach($invoices as $i)
  @if(!empty($i['transactions'][0]['id']) ||  $i['user']['invoice_payments_type']!='0')
    <tr>
      <td class='js-fc-id bold f12 p-2 pl-3' id="{{ $i['user']['id'] }}" style="width:4em;">{{ $i['user']['id'] }}</td>
      <td scope="row" style="width:42em;"><a class='f11' href="{{ route('users.show', ['id' => $i['user']['id']]) }}" target='blank'>{{ $i['user']['company_name'] }}（{{ $i['user']['name'] }}）  <i class="fas fa-external-link-alt"></i></a></td>
      <td class="common-table-stripes-row-tbody__td">
    @if(!empty($i['transactions'][0]['id']))
      @foreach($i['transactions'] as $t)
        <a href="{{ route('transactions.show', ['id' => $t['id']]) }}" class="mr-3 {{ $t['status'] != 1 ? 'text-danger' : ''}}" target="blank" dusk="transaction_id-{{ $t['id'] }}">{{ $t['id'] }}</a>
      @endforeach
    @elseif($i['user']['invoice_payments_type']=='0')
        <p>請求なし</p>
    @elseif($i['user']['invoice_payments_type']=='1')
        <p>HP掲載料のみ</p>
    @elseif($i['user']['invoice_payments_type']=='2')
        <p>ブランド使用料のみ</p>
    @endif
      </td>
    </tr>
  @endif
@endforeach
  </tbody>
</table>
@endsection
