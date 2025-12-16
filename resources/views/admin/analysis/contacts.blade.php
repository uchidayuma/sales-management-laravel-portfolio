@extends('layouts.layout') 
@section('css')
<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/jsgrid/jsgrid-theme.min.css')}}" rel="stylesheet"/>
<link href="{{ asset('plugins/jsgrid/jsgrid.min.css')}}" rel="stylesheet"/>
<link href="{{ asset('plugins/month-picker/month-picker.css') }}" rel="stylesheet"/>
<link href="{{ asset('plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/analysis/contacts.min.css?20220714') }}" rel="stylesheet" />
@endsection 

@section('javascript')
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/month-picker/month-picker.js') }}" defer></script>
<script src="{{ asset('plugins/jsgrid/jsgrid.min.js')}}" defer></script>
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}" defer></script>
<script src="{{ asset('js/analysis/contacts.js?20220707') }}" defer></script>
<script type="text/javascript">
  window.data = @json($data);
  window.queryString = @json($query_string);
</script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<h4 class="mt-4 mb-2 ml-2 h4">絞り込み条件</h4>
@if($errors->has('end') || $errors->has('endyear'))
<div class="alert alert-warning">{{ !empty($errors->first('end')) ? $errors->first('end') : $errors->first('endyear') }}</div>
@endif
<form class="common-form mb10 mt0 d-none" id="filtering-form" action="{{ route('analysis.contacts') }}">
  <section class='d-flex'>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>表示単位</h5>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="display" id="yearmonth" value='yearmonth' {{checked($query_string['display']=='yearmonth')}}>
        <label class="form-check-label f11 text-white" for="yearmonth">年月</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="display" id="year" value='year' {{checked($query_string['display']=='year')}}>
        <label class="form-check-label f11 text-white" for="year">年間（1/1～12/31）</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="display" id="year6" value='year6' {{checked($query_string['display']=='year6')}}>
        <label class="form-check-label f11 text-white" for="year6">年間（6/1～5/31）</label>
      </div>
    </div>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>期間（始まり）</h5>
    {{-- 年月の選択BOX --}}
      <input type="month" name="start" class="{{ $query_string['display'] == 'yearmonth' ? 'form-control' : 'form-control d-none' }}" value="{{ !empty($query_string['start']) ? $query_string['start'] : '' }}" autocomplete="off" id="start-yearmonth">
    {{-- 年だけの選択BOX --}}
      <select class="{{ $query_string['display'] == 'year' || $query_string['display'] == 'year6' ? 'form-control' : 'form-control d-none' }}" name="startyear" id="start-year">
        <option value="">---</option>
    @for($i=2012; $i<date('Y', strtotime('+1 year')); $i++)
        <option value="{{$i}}" {{selected($i==$query_string['startyear'])}}>{{$i}}年</option>
    @endfor
      </select>
    </div>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>期間（終わり）</h5>
    {{-- 年月の選択BOX --}}
      <input type="month" name="end" class="{{ $query_string['display'] == 'yearmonth' ? 'form-control' : 'form-control d-none' }}" value="{{ !empty($query_string['end']) ? $query_string['end'] : '' }}" autocomplete="off" id="end-yearmonth">
    {{-- 年だけの選択BOX --}}
      <select class="{{ $query_string['display'] == 'year' || $query_string['display'] == 'year6' ? 'form-control' : 'form-control d-none' }}" name="endyear" id="end-year">
        <option value="">---</option>
    @for($i=2012; $i<date('Y', strtotime('+1 year')); $i++)
        <option value="{{$i}}" {{selected($i==$query_string['endyear'])}}>{{$i}}年</option>
    @endfor
      </select>
    </div>
    <div class="mb-2 ml-4 mr-2 w20 d-flex align-items-center">
      <input type="submit" value="検索" class="btn btn-lg btn-primary" dusk='filter-contact'>
    </div>
  </section>
  {{-- <section class='d-flex mb-3'>
  </section>
  <input type="submit" value="検索" class="btn btn-primary w10" dusk='filter-contact'> --}}
</form>
<div id="result"></div>
@endsection
