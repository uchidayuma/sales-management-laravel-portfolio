@extends('layouts.layout')

@section('css')
@endsection

@section('javascript')
<script src="{{ asset('plugins/popper/popper.min.js') }}"></script>
<script src="{{ asset('js/fcapplyarea/index.js') }}" defer></script>
<script>
  window.fcApplyAreas = @json($fc_apply_areas);
  console.log(window.fcApplyAreas);
</script>
@endsection

@section('content')

<div class='d-flex justify-content-between align-items-start mb-4'>
  {!! $breadcrumbs->render() !!}
</div>

@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form action="{{ route('settings.fcapplyareas.store') }}" class='p-2 mb-4 d-flex align-items-start' method="post" enctype="multipart/form-data">
    @csrf
  <input class='form-control w15 mr-2' type='text' name='area[name]' placeholder="エリア名" dusk='input-name' required/>
  <textarea class='form-control w50 mr-2' name='area[content]' placeholder="エリア詳細（豊田市・瀬戸市・尾張旭市・・・・）" dusk='input-content' required></textarea>
  <button class='btn btn-primary' dusk='input-submit'>FC担当エリアを追加</button>
</form>
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">エリアID</th>
      <th scope="col">エリア名</th>
      <th scope="col">エリア内容</th>
      <th scope="col" style="white-space: nowrap;">エリア編集</th>
    </tr>
  </thead>
  <tbody>
@foreach($fc_apply_areas AS $area)
    <tr>
      <td class="common-table-stripes-row-tbody__td">{{$area['id']}}</td>
      <td class="common-table-stripes-row-tbody__td" style="white-space: nowrap;">{{$area['name']}}</td>
      <td class="common-table-stripes-row-tbody__td">{{$area['content']}}</td>
      <td class="common-table-stripes-row-tbody__td d-flex">
        <button id="{{$area['id']}}" class='btn btn-info edit-btn open-modal mr-1' dusk="{{'modal-open-'.$area['id']}}" data-toggle="modal" data-target="#area-modal">編集</button>
        <form action="{{ route('settings.fcapplyareas.destroy', ['id' => $area['id']]) }}" method="post">
          @csrf
      @if(is_null($area['is_apply']))
          <button class="btn btn-danger delete-btn" dusk="delete-area{{$area['id']}}">削除</button>
      @else
          <button class="btn btn-secondary disabled" dusk='delete-area' data-trigger="hover" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?" disabled>削除</button>
      @endif
        </form>
      </td>
    </tr>
@endforeach
  </tbody>
</table>

{{-- 変更モーダル --}}
<div class="modal fade" id="area-modal" tabindex="-1" role="dialog" aria-labelledby="areaModalLabel" aria-hidden="true">
  <form id='update-form' class='mb-4' action="" method="post">
    @csrf
    <!-- メール用データ -->
    <input type='hidden' name='area-id' value="" />
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="areaModalLabel">エリア情報の変更</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
          <div class="form-group mb20 shipping-cost-group">
            <label class='f12'>エリア名</label>
            <input id='area-name' type="text" class="form-control" name="area[name]" dusk='area-name' value="">
          </div>
          <div class="form-group mb20">
            <p><label class='f12'>エリア詳細</label></p>
            <textarea id='area-content'class="form-control" name="area[content]" autocomplete="off" dusk='area-content' rows="5"></textarea>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button class="btn btn-primary" dusk='update-area'>エリア情報を更新</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection