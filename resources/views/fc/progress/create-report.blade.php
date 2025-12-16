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
@endsection

@section('content')
{!! $breadcrumbs->render() !!}

<form method="post" action="{{ route('report.update', ['id' => $contact->id]) }}" class="form" enctype="multipart/form-data">
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
  <div class="form-input">
    <div class="m-sm-3 p-3">
      <div class="d-none d-sm-block flex my-2 align-items-center">
        <p class="title mr10">案件No.{{ $contact->id }}&nbsp;{{ isCompany($contact) ? $contact->company_name : $contact->surname.$contact->name }}&nbsp;{{ isCompany($contact) ? '' : '様' }}</p>
        <p class="sub-title">{{ $contact->pref }}{{ $contact->city }}{{ $contact->street }}</p>
      </div>
      <div class="d-block d-sm-none my-2 align-items-center">
        <p class="h5 mr10">案件No.{{ $contact->id }}<br>{{ isCompany($contact) ? $contact->company_name : $contact->surname.$contact->name }}&nbsp;{{ isCompany($contact) ? '' : '様' }}</p>
        <p class="h6">{{ $contact->pref }}{{ $contact->city }}{{ $contact->street }}</p>
      </div>
      <!--datepicker-->
      <div class="d-flex justify-content-between justify-content-sm-start">
        <p class='card-body__item bold f11 js-date-label d-flex align-items-center'>工事完了日</p>
        <input data-provide="datepicker" class="form-control ml-3 datepicker js-start-date form-input__calendar" type="datetime" name='c[completed_at]' value="{{ old('c.finished_datetime')}}" placeholder="日付を選択" dusk='finish-date'>
      </div>
    </div>
  </div>

  <div class="form-input"> 
    <div class="m-sm-3 p-sm-3">
      <p class='bold js-date-label pl-3 py-1'>施工後画像</p>
      <div class="d-sm-flex justify-content-around">

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>1枚目<span class="text-danger font-weight-bold">【必須】</span><br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または</span></p>
            <img src="">
            <input type="file" class="js-file js-image1" id="image-01" name="c[after_image1]">
            <label for="image-01" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>2枚目<br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または</span></p>
            <img src="">
            <input type="file" class="js-file js-image2" id="image-02" name="c[after_image2]">
            <label for="image-02" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>

        <div class="form-input__body flex">
          <div class="form-input__body-img uploader js-uploader">
            <p class='uploader__description'>3枚目<br><span class="d-none d-md-block">アップロードする画像をドロップ<br/>または</span></p>
            <img src="">
            <input type="file" class="js-file js-image3" id="image-03" name="c[after_image3]">
            <label for="image-03" class="px-xl-5 my-1 btn btn-secondary">画像を選択</label>
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <div class="form-input h-50 form-inputy flex flex-direction-column">
    <div class="m-sm-3 p-3 w-100">
      <p class='bold js-date-label pl-sm-3 py-sm-1'>連絡事項</p>
      <textarea class="form-control mb20" rows="3" name="c[memo]" placeholder="連絡事項">{{ old('c.memo') }}</textarea>
      <input name="c[public]" type="checkbox" id='public' class='mr10' value=1　{{ checked(!empty(old('c.public'))) }}/><label for='public' class='f11 pointer public-label'>画像の広告使用を許可</label>
    </div>
  </div>

  <p class="text-center text-sm-left sp-padding-bottom"><button type="submit" class="px-5 mb30 btn btn-primary" dusk='finish-submit'>完了報告を行う</button></p>

</form>

@endsection
