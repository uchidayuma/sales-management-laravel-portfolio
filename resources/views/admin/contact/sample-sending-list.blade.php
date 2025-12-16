@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/sample-sending-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/contact/sample-send-list.js') }}" defer></script>
@endsection

@section('content')
<div class='d-flex justify-content-between align-items-center'>
  {!! $breadcrumbs->render() !!}
  <form method="POST" action={{ route('contact.csv.sample.export') }} class="">
      @csrf
      <button class='btn btn-info'>ゆうプリ用CSVエクスポート</button>
  </form>
</div>

{{ $sample_list->links() }}
<form method="POST" action={{ route('sample.list.sent') }} class="contact-form">
    @csrf
    <table class="common-table-stripes-row">
    <thead class="common-table-stripes-row-thead">
        <tr>
        <th scope="col" class="check-th"><input type="checkbox" class='mr5' id="all">全て</th>
        <th scope="col">案件No</th>
        <th scope="col">問い合わせ日時</th>
        <th scope="col">名前</th>
        <th scope="col">住所</th>
        <th scope="col">電話番号</th>
        </tr>
    </thead>
    <tbody>
  @foreach($sample_list as $s)    
      <tr>
        <td class="js-contact-id check-td">
          <label class="form-check-label" for="contact-id{{$s->id}}">
            <div class="form-check form-check-inline">
              <input class="form-check-input list mr0" id="contact-id{{$s->id}}" type="checkbox" name="contact_id[]" value="{{ $s->id }}" dusk="check1">
            </div>
            <i class="far fa-hand-point-left"></i>
          </label>
        </td>
        <td class="js-contact-id">
          <a href="{{ route('contact.show', ['id' => $s->id]) }}">{{ displayContactId($s) }}</a>
        </td>
        <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($s->created_at))}}</td>
        <td class="common-table-stripes-row-tbody__td">{{ customerName($s) }} {{ isCompany($s) ? $s->surname.$s->name : ''}}</td>
        <td class="common-table-stripes-row-tbody__td">〒{{ $s->zipcode . ' ' . $s->pref.$s->city.$s->street}}</td>
        <td class="common-table-stripes-row-tbody__td">{{ $s->tel }}</td>
      </tr>
  @endforeach
    </tbody>
    </table>
    <div class="mt-4 justify-content-around"> 
        <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact7-submit'>送付完了</button></p>
    </div>
</form>
{{ $sample_list->links() }}
@endsection