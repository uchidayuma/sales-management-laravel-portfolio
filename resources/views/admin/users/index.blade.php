@extends('layouts.layout')

@section('content')

@section('css')
<link href="{{ asset('styles/account/fc-list.min.css') }}" rel="stylesheet" />
@endsection

<div class="search-icon">
  <p><i class="search-icon__search fas fa-search px-3"></i>検索</p>
</div>

<form class="common-form flex" action="{{ route('fc.search') }}">
    <p class="common-form__control common-form__span">都道府県で検索</p>
    <input type="text" name="pref" class="form-control-sm common-form__input" placeholder="【例 沖縄県】" required>
    <input type="submit" value="検索" class="btn btn-sm tn-primary">

</form>   
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center">
    <div class="col-4">{!! $breadcrumbs->render() !!}</div>
    <div class="col-4 mt-3">{{ $fc->appends(request()->query())->links() }}</div>
    <!-- 調整用div -->
    <div class="col-4 d-flex justify-content-between align-items-center">
      <a href="{{ route('users.csv.export')}}" class="{{isAdmin() ? 'btn btn-info' : 'd-none'}}" dusk='csv-export'>CSVエクスポート</a>
      <a href="{{ route('users.csv.post.export')}}" class="{{isAdmin() ? 'btn btn-primary' : 'd-none'}}"'>ゆうプリ用CSVエクスポート</a>
    </div>
  </div>
</div>
<div class="fc-list">     
  <table class="common-table-stripes-column mb30">
    <thead class="common-table-stripes-column-thead">
      <tr>
        <th class="">ID</th>
        <th class="keep-all">会社名</th>
        <th class="keep-all">担当エリア</th>
        <th class="keep-all">担当者名</th>
        <th class="keep-all">担当者電話番号</th>
        <th class="">修正</th>
      </tr>
    </thead>
    <tbody class="common-table-stripes-column-tbody">
@foreach ($fc as $user)
  @if($user->status == '2')
      <tr class='fc-deleted'>
        <th class="common-table-stripes-column__th w5"><span class='bold'><a href="{{ route('users.show', ['id' => $user->id]) }}">{{ $user->id }}</a></span></th>
        <td class=""><nobr><span class='bold color-red'>退会済み</span>：{{ $user->company_name }}</nobr></td>
  @elseif($user->status == '3' || $user->status == '4')
      <tr class='opacity5'>
        <th class="common-table-stripes-column__th w5"><span class='bold'><a href="{{ route('users.show', ['id' => $user->id]) }}">{{ $user->id }}</a></span></th>
        <td class=""><nobr>{!! $user->allow_email=='1' ? '<i class="far fa-envelope color-link"></i>' : '<i class="far fa-envelope color-red"></i>' !!} {{  $user->company_name }}</nobr></td>
  @elseif($user->status == '1')
      <tr>
        <th class="common-table-stripes-column__th w5"><span class='bold'><a href="{{ route('users.show', ['id' => $user->id]) }}">{{ $user->id }}</a></span></th>
        <td class=""><nobr>{!! $user->allow_email=='1' ? '<i class="far fa-envelope color-link"></i>' : '<i class="far fa-envelope color-red"></i>' !!} {{$user->company_name }}</nobr></td>
  @endif
        <td class="common-table-stripes-column__td">{{ $user['area_name'] . ' ' . $user['area_content'] }}</td>
        {{-- <td class="common-table-stripes-column__td">{{ $user['area_name'] }}</td> --}}
        <td class="common-table-stripes-column__td"><nobr>{{ $user->staff }}</nobr></td>
        <td class="common-table-stripes-column__td keep-all">{{ $user->s_tel }}</td>
        <td class="common-table-stripes-column__td py-2"><a href="{{ route('users.edit', ['id' => $user->id]) }}" class="btn btn-primary btn-sm px-3">修正</a></td>
      </tr>
@endforeach
    </tbody>
  </table>

  {{ $fc->appends(request()->query())->links() }}
</div>

@endsection