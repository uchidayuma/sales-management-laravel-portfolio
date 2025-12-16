@extends('layouts.layout')

@section('before-bootstrap-css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
@endsection

@section('css')
<link href="{{ asset('styles/quotation/create.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">作成者名</th>
      <th scope="col">顧客名</th>
      <th scope="col">住所</th>
      <th scope="col">見積書No</th>
      <th scope="col">編集</th>
      <th scope="col">見積書コピー</th>
    </tr>
  </thead>
  <tbody class="common-table-stripes-row-tbody">
    @foreach($quotations AS $q)
      <tr>
        <td class="common-table-stripes-row-tbody__td p-3"><a href="{{ route('contact.show', ['id' => $q->contact_id ]) }}" dusk="show{{$q->contact_id}}">{{ ($q->user_id != '1' && !empty($q->user_id)) ? $q->user_id.'-'.$q->contact_id : $q->contact_id }}</a></td>
        <td class="common-table-stripes-row-tbody__td">{{ customerName($q) }}</td>
        <td class="common-table-stripes-row-tbody__td">{{ $q->client_name }}</td>
        <td class="common-table-stripes-row-tbody__td">{{ $q->pref . $q->city . $q->street }}</td>
        <td class="common-table-stripes-row-tbody__td"><a href="{{route('quotations.show', ['id' => $q->quotation_id])}}" class="pl-3" dusk='quotation-show'>{{ $q->quotation_id }}</a></td>
      @if(is_null($q->main_user_id)|| $q->main_user_id == $q->user_id)
        <td class="common-table-stripes-row-tbody__td"><a href="{{route('quotations.edit', ['id' => $q->quotation_id])}}" class='btn btn-secondary' {{ $user['id'] == $q->quotation_user_id || isAdmin() ? '' : 'hidden'}}>編集</a></td>
        <td class="common-table-stripes-row-tbody__td"><a href="{{route('quotations.create', ['id' => $q->contact_id , 'copyId' => $q->quotation_id ])}}" class='btn btn-primary' dusk="copy{{$q->quotation_id}}" {{ $user['id'] == $q->quotation_user_id ? '' : 'hidden'}}>コピーして作成</a></td>
      @else
        <td class="common-table-stripes-row-tbody__td" style="cursor: not-allowed;">
          <a href="#" class='btn btn-secondary disabled' {{ $user['id'] == $q->quotation_user_id || isAdmin() ? '' : 'hidden'}}>編集</a>
        </td>
        <td class="common-table-stripes-row-tbody__td" style="cursor: not-allowed;">
          <a href="#" class='btn btn-secondary disabled' {{ $user['id'] == $q->quotation_user_id ? '' : 'hidden'}}>コピーして作成</a>
        </td>
      @endif
      </tr>
    @endforeach
  </tbody>
</table>
@endsection
