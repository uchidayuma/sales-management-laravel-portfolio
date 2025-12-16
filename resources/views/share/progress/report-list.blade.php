@extends('layouts.layout')

@section('content')
{!! $breadcrumbs->render() !!}
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">完了日</th>
      <th scope="col">問い合わせ日</th>
      <th scope="col">依頼種別</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
    </tr>
  </thead>
  <tbody class="common-table-stripes-row-tbody">
    @foreach($list as $c)
  @if(isAdmin() && $c->public == 1)
      <tr class='bg-success text-white'>        
        <td class="common-table-stripes-row-tbody__td p-3"><a href="{{ route('contact.show', ['id' => $c->id]) }}" class='text-primary'>{{ displayContactId($c) }} <i class="fas fa-images text-white ml10"></i> <span class='text-white'>OK</span></a></td>
  @else
      <tr>        
        <td class="common-table-stripes-row-tbody__td p-3"><a href="{{ route('contact.show', ['id' => $c->id]) }}">{{ displayContactId($c) }}</a></td>
  @endif
        <td class="common-table-stripes-row-tbody__td">{{ !empty($c->completed_at) ? date('Y年m月d日', strtotime($c->completed_at)) : ''}}</td>
        <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($c->created_at))}}</td>
        <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
        <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
        <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      </tr>
    @endforeach
  </tbody>
</table>

{{ $list->links() }}

@endsection