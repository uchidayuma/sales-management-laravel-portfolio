@extends('layouts.layout')

@section('css')
  <link href="{{ asset('styles/notification/index.min.css') }}" rel="stylesheet">
@endsection
@section('javascript')
<script src="{{ asset('js/notification/index.js') }}" defer></script>
@endsection
@section('content')
{!! $breadcrumbs->render() !!}
<div class="flex-center position-ref full-height" style='position: relative;'>
  <div class="content form-group">
    <table class="common-table-stripes-row">
      <thead class="common-table-stripes-row-thead">
        <tr>
          <th scope="col">案件No</th>
          <!-- <th scope="col">問い合わせ日時</th> -->
          <th scope="col">通知日時</th>
          <th scope="col">通知種類</th>
          <th scope="col">名前</th>
          <th scope="col">住所</th>
        </tr>
      </thead>
      <tbody>
  @foreach($notifications as $n)
      <tr>
        <td class="common-table-stripes-row-tbody__td p-3"><a href="{{ route('contact.show', ['id' => $n->contact_id]) }}" dusk="detail{{$n->id}}">{{ displayContactId($n) }}</a></td>
        <!-- <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($n->created_at)) }}</td> -->
        <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime($n->notificate_date)) }}</td>
        <td class="common-table-stripes-row-tbody__td">{{ $n->type_name }}</td>
        <td class="common-table-stripes-row-tbody__td">
          <p class="common-table-stripes-item">{{ isCompany($n) ?  $n->company_name : $n->surname.$n->name }}</p>
        </td>
        <td class="common-table-stripes-row-tbody__td">{{ $n->pref.$n->city. $n->street }}</td>
      </tr> <!-- common-table-stripes__tr -->
  @endforeach
    </table>
</tbody>
  </div>
</div>
@endsection