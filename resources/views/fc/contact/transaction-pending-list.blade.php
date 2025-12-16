@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

@section('javascript')
<script src="{{ asset('js/contact/pending.js') }}" defer></script>
@endsection
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
      <th scope="col">発注書</th>      
    </tr>
  </thead>
  <tbody>
  @foreach($contacts as $c)
    <tr>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}">{{ displayContactId($c) }}</a></td>
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime(!empty($c->fc_assigned_at) ? $c->fc_assigned_at : $c->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td"><a href="" class="btn btn-primary btn-xs disabled" dusk="create{{$c->id}}">作成</a>
    </tr>
  @endforeach
  </tbody>
</table>

<h4>本部発送待ち案件一覧</h4>
@include('fc.contact.transport-pending')

<div class="d-flex justify-content-center">
  {{ $contacts->appends(request()->query())->links() }}
</div>

@endsection
