@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="{{ asset('styles/progress/create-report.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/report/create.js') }}" defer></script>
<script src="{{ asset('js/progress/add-before-images.js' )}}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}

<form method="post" action="{{ route('post.before.report') }}" enctype="multipart/form-data">
  @csrf

@if($errors->any())
  <div class="error alert alert-warning">
    <ul class="validation-ul">
      @foreach($errors->all() as $message)
        <li>{{ $message }}</li>
      @endforeach
    </ul>
  </div><!-- error alert alert-warning -->
@endif
@if(!empty($contacts[0]))
    <h5 class="mb20">現場確認報告をする案件を選択してください。</h5>
    @foreach($contacts as $key => $c)
        <div class="form-check mb10 p10 f11">
          <input class="form-check-input ml0" type="radio" name="c[id]" id="radios{{$key}}" value="{{$c->id}}" dusk='select'>
          <label class="form-check-label pl20" for="radios{{$key}}">
            案件No.{{$c->id}} 依頼日：{{ date('Y年m月d日', strtotime( !empty($c->fc_assigned_at) ? $c->fc_assigned_at : $c->created_at)) }} {{ customerName($c)}} {{ $c->pref }}{{ $c->city }}{{ $c->street }}
          </label>
          <a class='ml20' href="{{ route('contact.show', ['id' => $c->id]) }}" target="blank">案件情報を確認<i class="ml5 color-link pointer fas fa-external-link-alt"></i></a>
        </div>
    @endforeach
@else
        <h2 id="noOptions">該当する案件がありません。</h2> 
@endif

@if(!empty($contacts[0]))
  <div class="form-input">
      <div class="d-sm-flex justify-content-around">

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>1枚目<span class="text-danger font-weight-bold">【必須】</span><br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または<span></p>
            <img src="">
            <input type="file" class="js-file js-image1" id="image-01" name="c[before_image1]">
            <label for="image-01" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>2枚目<br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または</span></p>
            <img src="">
            <input type="file" class="js-file js-image2" id="image-02" name="c[before_image2]">
            <label for="image-02" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>3枚目<br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または</span></p>
            <img src="">
            <input type="file" class="js-file js-image3" id="image-03" name="c[before_image3]">
            <label for="image-03" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>

      </div>
    </div>
      <p class="text-center text-sm-left sp-padding-bottom"><button type="submit" class="px-5 btn btn-primary" id="register" dusk='dusk-submit'>登録</button></p>
@else

@endif
  
</form>

@endsection

