@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/articles/create.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/dashboard.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script>
window.customers = @json($customers);
</script>
<script src="{{ asset('plugins/chartjs/chart.js') }}" defer></script>
<script src="https://www.gstatic.com/charts/loader.js" defer></script>
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('js/analysis/fc-helper.js?20220412') }}" defer></script>
@endsection

@section('footer')
<script src="{{ asset('js/analysis/fc-index.js?20220412') }}" defer></script>
@endsection

@section('content')
<form id='data-form' class="common-form mb10 mt0">
  <section class='d-flex justify-content-between'>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>データ選択</h5>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="city-price" name='type' value='city-price' checked/>
        <label class="form-check-label f11 text-white" for="city-price">市町村と成約価格</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="city-find" name='type' value='city-find'/>
        <label class="form-check-label f11 text-white" for="city-find">市町村と認知経路</label>
      </div>
    </div>
    <div class="mb-2 mr-5 w20">
    </div>
{{-- 
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>Y軸</h5>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-contacts" name='y' value='contacts' checked/>
        <label class="form-check-label f11 text-white" for="y-contacts">問い合わせ件数</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-city" name='y' value='city' />
        <label class="form-check-label f11 text-white" for="y-city">市町村</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-age" name='y' value='age' />
        <label class="form-check-label f11 text-white" for="y-age">年代</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-cognitive" name='y' value='cognitive' />
        <label class="form-check-label f11 text-white" for="y-cognitive">認知経路</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-turf" name='y' value='turf' />
        <label class="form-check-label f11 text-white" for="y-turf">芝種</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" id="y-price" name='y' value='price' />
        <label class="form-check-label f11 text-white" for="y-price">成約金額</label>
      </div>
    </div> --}}
    {{-- <div class="mb-2 mr-2 w10">
      <h5 class='text-white'>X軸Y軸<br/>入れ替え</h5>
      <i class="fas fa-random text-white f14 pointer" onClick="switchData()"></i>
    </div> --}}
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>期間（始まり）</h5>
    {{-- 年月の選択BOX --}}
      <input type="month" name="start" class="form-control" value="" autocomplete="off">
    </div>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>期間（終わり）</h5>
    {{-- 年月の選択BOX --}}
      <input type="month" name="end" class="form-control" value="" autocomplete="off">
    </div>
  </section>
</form>
<div id='canvases' class="d-flex justify-between">
  <div id="stage"></div>
  <div class="content p15">
    <canvas id="bar"></canvas>
  </div>
  <div class="content p15">
    <canvas id="line"></canvas>
  </div>
</div>
@endsection