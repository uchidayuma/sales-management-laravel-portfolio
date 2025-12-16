@extends('layouts.layout')

@section('content')

@section('css')
<link href="{{ asset('styles/account/fc-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script>
  window.queryParams = @json($query);
  console.log(window.queryParams);
</script>
<script src="{{ asset('js/users/contract.js?20221209') }}" defer></script>
@endsection

<div class='d-flex justify-content-between align-item-center'>
  {!! $breadcrumbs->render() !!}
  <form id='search-form' class='ranking-form' action="{{ route('users.contracts') }}?{{$queryString}}" method='GET'>
    <div class="form-group d-flex">
      <select class="form-control ranking-form-select mr-2" name='year' style="width:120px;">
    @foreach($pastYear AS $y)
        <option value="{{ $y }}" {{selected($y==$year)? 'selected': ''}}>{{ $y }}年</option>
    @endforeach
      </select>
      <select class="form-control ranking-form-select mr-2" name='month' id='month' style="width:100px;">
    @for($i=1;$i<13;$i++)
        <option value="{{$i}}" {{ selected($i==$month) ? 'selected' : '' }}>{{$i}}月</option>
    @endfor
      </select>
      <button class='btn btn-primary'>絞り込む</button>
    </div>
  </form>
</div>

<div class="fc-list">     
  <table class="common-table-stripes-column mb30">
    <thead class="common-table-stripes-column-thead">
      <tr>
        <th class="">ID</th>
        <th class="keep-all">会社名</th>
        <th class="keep-all">更新日</th>
        <th class="keep-all">エリア開放通知</th>
        <th class="keep-all">同エリアのFC</th>
      </tr>
    </thead>
    <tbody class="common-table-stripes-column-tbody">
@foreach ($fcs as $user)
      <tr class='js-fc'>
        <th class="common-table-stripes-column__th w5"><span class='bold'><a href="{{ route('users.show', ['id' => $user->id]) }}">{{ $user->id }}</a></span></th>
        <td class=""> {{$user->company_name }}</td>
        <td class="js-contract-date common-table-stripes-column__td" contract-date={{date('m-d', strtotime($user->contract_date))}}>{{ date('m月d日', strtotime($user->contract_date)) }}</td>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input js-check" type="checkbox" id="check{{$user['send_id']}}" send-id={{$user['send_id']}} name="" value="" @checked($user['send_status'] === 1)>
            <label class="js-check-label form-check-label pointer" for="check{{$user['send_id']}}">チェックで送る</label>
          </div>
        </td>
        <td class="common-table-stripes-column__td py-2">
          <button class="js-samearea btn btn-info btn-sm px-3" data-toggle="modal" data-target="#same-fc-modal" user-id={{$user->id}} year={{$year}} send-id={{$user['send_id']}}>確認</button>
        </td>
      </tr>
@endforeach
@if(count($fcs) === 0)
      <tr>
        <td colspan="5" class="p-3 common-table-stripes-column__td">エリア開放メール送信予定のFCはありません</td>
      </tr>
@endif
    </tbody>
  </table>
</div>

<!-- 同エリアFCModal -->
<div class="modal fade" id="same-fc-modal" tabindex="-1" role="dialog" aria-labelledby="sameFcgModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3>同エリアの達成状況</h3>
        <p id='same-area-name'></p>
      </div>
      <div class="modal-body">
        <table id='result-table' class="mb30 table d-none">
          <thead class="">
            <tr>
              <th class=""></th>
              <th class="keep-all">{{$year-1}}年</th>
              <th class="keep-all">{{$year}}年</th>
            </tr>
          </thead>
          <tbody id='same-fc-tbody' class="">
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</div>

@endsection