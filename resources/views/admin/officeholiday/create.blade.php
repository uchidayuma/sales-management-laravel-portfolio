@extends('layouts.layout') 

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/trumbowyg/trum.css') }}" />
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
@endsection 

@section('javascript')
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/common.js?20210818') }}" defer></script>
<script src="{{ asset('js/setting/office-holiday.js?20210818') }}" defer></script>
<script>
  window.holidayCount = 1;
</script>
@endsection

@section('content')
<h2 class='h2 mb-2'>会社休日設定</h2>
<p class='f10 mb-3'>この画面で設定した日が会社休日となり、納品希望日に反映されます</p>
<p class='f10 mb-3 color-red'>※土日祝日以外の休日を設定してください</p>
<section class='holidays mb-3 p-3 row justify-content-start align-items-center'>
@foreach( $holidays as $key => $h)
  <div class='col-md-3 m-2 mb-3 d-flex align-items-center'>
    <input data-provide="datepicker" id="{{ $h['id'] }}" class="form-control datepicker js-date pointer mr-2" type="datetime" value="{{ $h['holiday'] }}" dusk='' autocomplete="off" placeholder=''/>
    <i class="fas fa-times fa-2x js-delete pointer" id="{{ $h['id'] }}"></i>
  </div>
@endforeach
</section>
<button class='btn btn-primary ml-3 js-add-holiday'>会社休日を追加</button>
@endsection
