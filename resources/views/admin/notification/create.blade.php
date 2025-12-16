@extends('layouts.layout') 

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/trumbowyg/trum.css') }}" />
@endsection 

@section('javascript')
@endsection

@section('content')
<h2 class='h2 ml-3'>アラート設定</h2>
<form action="{{ route('notifications.update') }}" method="POST" class="row position-ref full-height p-3">
  @csrf
@foreach($settings AS $setting)
  <div class='col-md-4'>
    <label for="">{{$setting->name }}</label>
    <!-- name属性はnotification_typesテーブルのstep_id -->
    <select class="form-control" name="nt[{{$setting->id}}]" dusk="period{{$setting->id}}">
  @for($i=1;$i<7;$i++)
      <option value="{{$i}}" {{selected($i==$setting->period)}}>{{$i}}営業日</option>
  @endfor
    </select>
  </div>
@endforeach
  <button class='btn btn-primary mt20 ml-3' dusk="submit">設定</button>
</form>

<h2 class='h2 ml-3'>FC案件放置設定</h2>
<p class='m-3 f10'>*FCに依頼したにも関わらず案件詳細を確認しなかった場合、放置と判定される日数</p>
<form action="{{ route('config.leaveday-update') }}" method="POST" class="position-ref full-height p-3 pt-0">
  @csrf
  <select class="form-control w25" name="day" dusk="leave-select">
  @for($i=1;$i<15;$i++)
    <option value="{{$i}}" {{selected($i == $leave_day['value'])}}>{{$i}}日</option>
  @endfor
  </select>
  <button class='btn btn-primary mt20 d-block' dusk="leave-submit">放置日数を設定</button>
</form>
@endsection
