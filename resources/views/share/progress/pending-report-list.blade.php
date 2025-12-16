@extends('layouts.layout')

@section('content')
{!! $breadcrumbs->render() !!}
{{-- PCでのみ表示 --}}
<div class="d-none d-sm-block">
  <table class="common-table-stripes-row">
    <thead class='common-table-stripes-row-thead'>
      <tr>
        <th scope="col">案件No</th>
        <th scope="col">依頼日</th>
        <th scope="col">依頼種別</th>
        <th scope="col">名前</th>
        <th scope="col">住所</th>
        <th scope="col">完了報告</th>
        <th scope="col">追加発注</th>
      </tr>
    </thead>
    <tbody class='common-table-stripes-row'>
  @foreach($list AS $rc)
      <tr>
        <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $rc->id]) }}">{{ displayContactId($rc) }}</a></td>
        <td class="common-table-stripes-row-tbody__td">{{ date('Y年m月d日', strtotime(!empty($rc->fc_assigned_at) ? $rc->fc_assigned_at : $rc->created_at))}}</td>
        <td class="common-table-stripes-row-tbody__td">訪問見積もり</td>
        <td class="common-table-stripes-row-tbody__td">{{ isCompany($rc) ? $rc->company_name . ' ' . $rc->surname.$rc->name : $rc->surname.$rc->name }}</td>
        <td class="common-table-stripes-row-tbody__td">{{ $rc->pref.$rc->city.$rc->street }}</td>
        <td class="common-table-stripes-row-tbody__td"><a href="{{ route('report.create', ['id' => $rc->id]) }}" class='btn btn-warning' dusk="create-{{ $rc->id }}">作成</a></td>
        <td class="common-table-stripes-row-tbody__td"><a class="btn btn-success" href="{{ route('create.order', ['contactId' => $rc->id]) }}" role="button" dusk="contact{{ $rc->id }}" {{  subTransactionLimitDate($rc->id) ? '':'hidden'}}>追加発注</a></td>
      </tr>
  @endforeach
    </tbody>
  </table>
</div>
{{-- スマホ用のリスト作成 --}}
<div class="d-block d-sm-none">
@foreach($list AS $rc)
    <ul class="list-group">
      <li class="list-group-item d-flex justify-content-between my-1">
        <div Class="item_left">
          <span class="h6">
          ID.<a href="{{ route('contact.show', ['id' => $rc->id]) }}">{{ displayContactId($rc) }}</a><br>
          {{ isCompany($rc) ? $rc->company_name . ' ' . $rc->surname.$rc->name : $rc->surname.$rc->name }}<br>
          </span>
          {{ $rc->pref.$rc->city.$rc->street }}<br>
        </div>
        <div class="item_left pl-3 d-flex align-items-center justify-content-center">
          <p class="text-right"><a href="{{ route('report.create', ['id' => $rc->id]) }}" class='btn btn-warning' dusk="create-{{ $rc->id }}">作成</a></p>
        </div>
      </li>
    </ul>
@endforeach
</div>

{{ $list->links() }}

@endsection