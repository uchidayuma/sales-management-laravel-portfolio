@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/assigned-list.js') }}" defer></script>
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">依頼日</th>
      <th scope="col">依頼種別</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
    @if(isFc())
      <th scope="col">見積もり</th>
    @endif
    </tr>
  </thead>
  <tbody>
@foreach($contacts as $c)
  @if(alertInView($c->updated_at, $c->alert_date))
    <tr class='alert-tr'>
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk='detail-button'>{{ displayContactId($c) }}</a><span class='alert-tr__label'>至急対応！</span></td>
  @else
    <tr>
      <td class='js-contact-id'><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk='detail-button'>{{ displayContactId($c) }}</a></td>
  @endif
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($c->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
    @if(isFc())
      <td class="common-table-stripes-row-tbody__td" {{(is_null($c->main_user_id) || $c->main_user_id == $c->user_id) ? '' : 'style="cursor: not-allowed;"'}} >
      @if(is_null($c->main_user_id) || $c->main_user_id == $c->user_id)
        <a href="" dusk="create-button" class="btn btn-success px-3 disabled" disabled>作成</a> 
      @else
        <a href="#" dusk="create-button" class="btn btn-secondary px-3 disabled">作成</a> 
      @endif
      </td>
    @endif
    </tr>
@endforeach
  </tbody>
</table>
@endsection
