@extends('layouts.layout')

@section('css')
<!-- <link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" /> -->
<link href="{{ asset('styles/contact/customers-list.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>
{{ $contacts->links() }}

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">問い合わせ日時</th>
      <th scope="col">依頼種別</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">状態</th>
    </tr>
  </thead>
  <tbody>
  @foreach($contacts as $c)
    <tr>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a></td>
      <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($c->created_at)) }}</td>
      <td class="common-table-stripes-row-tbody__td"><img class="contact-type__label" src="/images/icons/contact-types/{{$c->contact_type_id}}.png"></td>
      <td class="common-table-stripes-row-tbody__td">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td d-flex flex-nowrap">{!! !empty($c->sample_send_at) ? sampleSend($c->sample_send_at) : ''!!}{!! returnStepLabel($c->step_id, $c->cancel_step) !!}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center">
  {{ $contacts->links() }}
</div>

@endsection
