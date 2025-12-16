@extends('layouts.layout') 
@section('css')
<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/jsgrid/jsgrid-theme.min.css')}}" rel="stylesheet"/>
<link href="{{ asset('plugins/jsgrid/jsgrid.min.css')}}" rel="stylesheet"/>
<link href="{{ asset('plugins/month-picker/month-picker.css') }}" rel="stylesheet"/>
<link href="{{ asset('plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/analysis/index.min.css?20210930') }}" rel="stylesheet" />
@endsection 

@section('javascript')
<script src="{{ asset('plugins/dayjs/day.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/month-picker/month-picker.js') }}" defer></script>
<script src="{{ asset('plugins/jsgrid/jsgrid.min.js')}}" defer></script>
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}" defer></script>
<script src="{{ asset('js/analysis/index.js?20210913') }}" defer></script>
<script type="text/javascript">
  window.data = @json($data);
  window.queryString = @json($query_string);
  window.totalTransactionRate = 0; //合計行の受注率
</script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<h4 class="mt-4 mb-2 ml-2 h4">絞り込み条件</h4>
@if($errors->has('end') || $errors->has('endyear'))
<div class="alert alert-warning">{{ !empty($errors->first('end')) ? $errors->first('end') : $errors->first('endyear') }}</div>
@endif
<form class="common-form mb10 mt0 d-none" id="filtering-form" action="{{ route('analysis.index') }}">
  <section class='d-flex'>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>都道府県</h5>
      <select name='prefs[]' id="select-pref" class="form-control mr5 w-100" aria-placeholder="都道府県" dusk='select-pref' multiple="multiple"> 
      @foreach($prefectures AS $p)
        <option value="{{ $p->id }}" {{selected(in_array($p->id, $query_string['prefs']))}}>{{ $p['name'] }}</option>
      @endforeach
      </select>
    </div>
    <div class="mb-2 mr-5 w20">
      <h5 class='color-white'>FC</h5>
      <select name='fcs[]' class="select-fc form-control mr5" aria-placeholder='FC' dusk='select-fc' multiple="multiple"> 
      @foreach($users AS $u)
        <option value="{{ $u->id }}" {{selected(in_array($u->id, $query_string['fcs']))}} isremove="{{ $u->status == 2 ? true : false }}">{{ $u['name'] }}</option>
      @endforeach
      </select>
    </div>
    <div class="mb-2 mr-2 w10">
      <h5 class='color-white'>表示単位</h5>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="display" id="yearmonth" value='yearmonth' {{checked($query_string['display']=='yearmonth')}}>
        <label class="form-check-label f11 text-white" for="yearmonth">年月</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="display" id="year" value='year' {{checked($query_string['display']=='year')}}>
        <label class="form-check-label f11 text-white" for="year">年</label>
      </div>
    </div>
    <div class="mb-2 mr-2 w20">
      <h5 class='color-white'>期間（始まり）</h5>
    {{-- 年月の選択BOX --}}
      <input type="month" name="start" class="{{ $query_string['display'] == 'yearmonth' ? 'form-control' : 'form-control d-none' }}" value="{{ !empty($query_string['start']) ? $query_string['start'] : '' }}" autocomplete="off" id="start-yearmonth">
    {{-- 年だけの選択BOX --}}
      <select class="{{ $query_string['display'] == 'year' ? 'form-control' : 'form-control d-none' }}" name="startyear" id="start-year">
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
      <select class="{{ $query_string['display'] == 'year' ? 'form-control' : 'form-control d-none' }}" name="endyear" id="end-year">
        <option value="">---</option>
    @for($i=2012; $i<date('Y', strtotime('+1 year')); $i++)
        <option value="{{$i}}" {{selected($i==$query_string['endyear'])}}>{{$i}}年</option>
    @endfor
      </select>
    </div>
  </section>
  <section class='d-flex mb-3'>
    <group class="inline-radio w-50">
      <div class='mr-2 pointer'><input class='p-2 h-100 pointer' type="radio" name="type" value="1" dusk='type1' {{checked($query_string['type']==1)}}><label>依頼件数</label></div>
      <div class='mr-2 pointer'><input class='p-2 h-100 pointer' type="radio" name="type" value="2" dusk='type2' {{checked($query_string['type']==2)}}><label>受注件数</label></div>
      <div class='mr-2 pointer'><input class='p-2 h-100 pointer' type="radio" name="type" value="3" dusk='type3' {{checked($query_string['type']==3)}}><label>問い合わせ件数</label></div>
    </group>
  </section>
  <input type="submit" value="検索" class="btn btn-primary w10" dusk='filter-contact'>
</form>
<section>
  <button class="btn btn-primary mb-2 mr-2" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">各要素の定義</button>
  <button id='toggle-hidden-fc' class="btn btn-secondary mb-2" type="button" >退会済みFCを表示</button>
</section>
<div class="collapse bg-white" id="collapseExample">
  <table class="table table-bordered">
    <tbody>
  @if( empty($query_string['type']) || $query_string['type'] === '1' )
      <tr>
        <td class='bold w15'>依頼</td>
        <td>本部からFCにシステムから依頼した案件数</td>
      </tr>
      <tr>
        <td class='bold w15'>本部の依頼</td>
        <td>本部案件（材料のみの依頼）案件数</td>
      </tr>
  @elseif($query_string['type'] === '2')
      <tr>
        <td class='bold w15'>依頼</td>
        <td>本部からの依頼案件で発注まで進んで案件数</td>
      </tr>
      <tr>
        <td class='bold' scope="col">自己</td>
        <td>自己獲得案件で発注まで進んで案件数</td>
      </tr>
      <tr>
        <td class='bold' scope="col">依頼受注率</td>
        <td>本部からの依頼案件で発注まで進んで案件数 / 本部からの依頼案件数</td>
      </tr>
      <tr>
        <td class='bold' scope="col">自己受注率</td>
        <td>自己獲得で発注まで進んで案件数 / 自己獲得案件数</td>
      </tr>
      <tr>
        <td class='bold' scope="col">依頼受注率（本部）</td>
        <td>売上が立った案件数 / 本部担当案件数</td>
      </tr>
  @else
      <tr>
        <td class='bold w15'>問い合わせ数</td>
        <td>FCが自己獲得した案件数</td>
      </tr>
      <tr>
        <td class='bold w15'>本部の問い合わせ数</td>
        <td>HPからの問い合わせ＋TELでの依頼案件数</td>
      </tr>
  @endif
    </tbody>
  </table>
</div>
<div id="result"></div>
@endsection
