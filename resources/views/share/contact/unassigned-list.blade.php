@extends('layouts.layout')

@section('before-bootstrap-css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
@endsection

@section('css')
<link href="{{ asset('styles/contact/unassigned-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/contact/unassigned-list.js') }}" defer></script>
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
@endsection


@section('content')

{!! $breadcrumbs->render() !!}

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">問い合わせ日時</th>
      <th scope="col">依頼種別</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">FCを選択</th>
      <th scope="col">編集</th>
      <th scope="col" {{ adminOnlyHidden()}}>見積もり</th>
      {{-- <th scope="col">失注</th> --}}
    </tr>
  </thead>
  <tbody class="common-table-stripes-row-tbody">
  @foreach($contacts as $c)
    <tr>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a></td>
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($c->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.assign', ['id' => $c->id]) }}" dusk="assign{{$c->id}}"><button type="button" class="btn btn-info">FCを選択</button></a>
      <td class="common-table-stripes-row-tbody__td"><a type="button" href="{{ route('assigned.edit', ['id' => $c->id ]) }}" class="edit-buttn btn btn-warning" dusk="contact-edit">編集</a></td>
    @if(( isAdmin() && $c->contact_type_id == '2' && $c->quote_details == '材料のみ') || ( isAdmin() && $c->contact_type_id == '6'  && $c->quote_details == '材料のみ'))
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('quotations.create', ['id' => $c->id]) }}" class="btn btn-primary btn-sm px-3">作成</a>
      </td>
    @else
      <td {{adminOnlyHidden()}}></td>
    @endif
    </tr>
  @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center pt-3">
  {{ $contacts->appends(request()->query())->links() }}
</div>

@endsection
