@extends('layouts.layout')

@section('content')
{!! $breadcrumbs->render() !!}
<form class="form-inline" action="{{ route('assigned.search') }}">
  <div class="form-group">
  <select name="type" class="form-control">
    <option value="">法人/個人を選択してください</option>[
    @if(isset($_GET["type"]))
    <option value="0" {{ selected($_GET["type"] == 0) }}>法人</option>
    <option value="1" {{ selected($_GET["type"] == 1) }}>個人</option>
    @else
    <option value="0">法人</option>
    <option value="1">個人</option>
    @endif
  </select>
  <select name="contact_type" class="form-control">
    <option value="" >問い合わせ種類を選択してください</option>
    @foreach($contactTypes as $t)
      @if(isset($typeOfContact))
      <option value="{{ $t->id }}" {{ selected($t->id == $typeOfContact) }} >{{ $t->name }}</option>
      @else
      <option value="{{ $t->id }}" >{{ $t->name }}</option>
      @endif
    @endforeach
  </select>
  <input type="text" name="comment" class="form-control" placeholder="キーワードで検索">
  </div>
  <input type="submit" value="検索" class="btn btn-info">
</form>

<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">問い合わせ日時</th>
      <th scope="col">法人/個人</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">詳細</th>
    </tr>
  </thead>
  <tbody>
  @foreach($contacts as $c)
    <tr>
      <th scope="row">{{date('Y年m月d日', strtotime(!empty($c->fc_assigned_at) ? $c->fc_assigned_at : $c->created_at))}}</th>
      <td>{{ $c->type ? '個人': '法人'}}</td>
      <td>{{$c->surname}}{{$c->name}}様</td>
      <td>{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td><a href="{{ route('contact.show', ['id' => $c->id]) }}"><button type="button" class="btn btn-outline-warning">詳細</button></a>
    </tr>
  @endforeach
  </tbody>
</table>

{{ $contacts->appends(request()->query())->links() }}

@endsection