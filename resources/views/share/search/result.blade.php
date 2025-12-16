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

<!--顧客検索フォーム-->
<div class="search-icon">
  <i class="search-icon__search fas fa-search"></i>
</div>

  <form class="common-form" action="{{ route('search.result') }}">
    <input type="text" name="name" class="form-control-sm common-form__input" placeholder="顧客名">
    <input type="number" name="tel" class="form-control-sm common-form__input" placeholder="電話番号">
    <input type="text" name="pref" class="form-control-sm common-form__input" placeholder="都道府県">
    <input type="submit" value="検索" class="btn-sm px-3 btn btn-primary">
  </form>

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

@if(isset($contacts))

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">No</th>
      <th scope="col"><span class="common-table-stripes-row-thead__th-span">問い合わせ日時</span></th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">電話番号</th>
      <th scope="col">詳細</th>
    </tr>
  </thead>
  <tbody>
@foreach($contacts as $c)
    <tr>
      <td class='js-contact-id'>{{ $c->id }}</td>
      <th scope="row"><span class="common-table-stripes-row-tbody__th-span">{{date('Y年m月d日', strtotime($c->created_at))}}</span></th>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->tel}}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('contact.show', ['id' => $c->id]) }}" class="btn btn-info px-3" dusk='detail-button'>詳細</a>
      </td>
    </tr>
@endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center">
  {{ $contacts->appends(request()->query())->links() }}
</div>

@endif

@endsection
