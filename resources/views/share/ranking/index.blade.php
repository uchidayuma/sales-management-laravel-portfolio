@extends('layouts.layout')

@section('css')
  <link href="{{ asset('styles/ranking/index.min.css') }}" rel="stylesheet">
@endsection

@section('javascript')
<script src="/js/ranking/index.js" defer></script>
@endsection

@section('content')
<div class='d-flex justify-content-between'>
  {!! $breadcrumbs->render() !!}
  <form class='ranking-form' action="{{ route('rankings.index') }}?{{$queryString}}" method='GET'>
    <input type='hidden' name='order' value="{{$order}}">
    <div class="form-group d-flex">
      <select class="form-control ranking-form-select" name='year'>
    @foreach($pastYear AS $year)
        <option value="{{$year}}" {{selected($query['year']==$year)? 'selected': ''}}>{{$year}}年</option>
    @endforeach
      </select>
      <select class="form-control ranking-form-select" name='month' id='month'>
        <option value='' class="ranking-form-select__none">年間</option>
    @for($i=1;$i<13;$i++)
        <option value="{{$i}}" {{ !empty($query['month']) ? selected($i==$query['month']) : '' }}>{{$i}}月</option>
    @endfor
      </select>
      <button class='btn btn-primary'>絞り込む</button>
    </div>
  </form>
</div>
  <table class="common-table-stripes-row mb30">
  @foreach($rankings as $key => $fc)
    <tr class="rank-tr">
      <td class="rank">
        <p class="">
    @if(!array_key_exists('page', $_GET) || $_GET['page']=='1')
      @switch($key)
        @case(0)
              <i class="fas fa-trophy color-gold rank__trophy"></i><span class="rank__number rank__number--top3 color-white bold">{{ $key + 1 }}</span>
          @break
        @case(1)
              <i class="fas fa-trophy color-silver rank__trophy"></i><span class="rank__number rank__number--top3 color-white bold">{{ $key + 1 }}</span>
          @break
        @case(2)
            <i class="fas fa-trophy color-copper rank__trophy"></i><span class="rank__number rank__number--top3 color-white bold">{{ $key + 1 }}</span>
          @break
        @default
            <span class="rank__number color-black bold">{{ $key + 1 }}</span>
      @endswitch
    @else
            <span class="rank__number color-black bold">{{ intval($_GET['page'] - 1) * 50 + ( $key + 1) }}</span>
    @endif
        </p>
      </td>
      <td class=""><a href="/users/{{ $fc->id }}">{{ $fc->company_name}}</a></td>
    @if(isAdmin())
      <td class="">{{ $fc->pref}}{{ $fc->city}}{{ $fc->street}}</td>
    @else
      <td class="w60"></td>
    @endif
    <!-- ランキング項目によって分岐 -->
    @if(isAdmin() && $order=='sales')
      <td class="f11 bold">{{ number_format($fc->sales) }}円</td>
    @elseif(isAdmin() && $order=='number')
      <td class="f11 bold">{{ number_format($fc->number) }}件</td>
      <td class="f11 bold">{{ number_format($fc->total_area) }}㎡</td>
    @endif
    </tr>
  @endforeach
  </table>
  @if(isAdmin())
    {{ $rankings->links() }}
  @endif
@endsection