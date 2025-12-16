@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
@endsection


@section('javascript')
<script src="{{ asset('js/contact/pending.js') }}" defer></script>
@endsection

@section('content')
<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

@if(!is_string($contacts))
<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">依頼日</th>
    @if(isFC())
      <th scope="col">依頼種別</th>
    @endif
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">商談結果登録</th>
    @if(isFC())
      <th scope="col">状態</th>
      <th scope="col">見積もり</th>
    @endif
    </tr>
  </thead>
  <tbody>

@foreach($contacts as $c)
  @if(alertInView($c->updated_at, $c->alert_date))
    <tr class='alert-tr'>
      <td class="js-contact-id" id="{{ $c->id }}"><a href="{{ route('contact.show', ['id' => $c->id]) }}">{{ displayContactId($c) }}</a><span class='alert-tr__label keep-all'>至急対応！</span></td>
  @else
    <tr>
      <td class="js-contact-id" id="{{ $c->id }}"><a href="{{ route('contact.show', ['id' => $c->id]) }}">{{ displayContactId($c) }}</a></td>
  @endif
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime(!empty($c->fc_assigned_at) ? $c->fc_assigned_at : $c->created_at))}}</td>
  @if(isFC())
      <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
  @endif
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <!--商談結果登録モーダル-->
      <td class="common-table-stripes-row-tbody__td">
        <button type="button" dusk="select-modal{{$c->id}}" class="btn btn-success btn-xs open" data-toggle="modal" data-target="#pending-modal">商談結果登録</button>
      </td>
  @if(isFC())
      <td>{{ $c->status == '3' ? '顧客返答待ち' : '入力待ち'}}</td>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('quotations.create', ['id' => $c->id]) }}" class="btn btn-secondary btn-xs">追加作成</a></td>
  @endif
    </tr>
@endforeach
  </tbody>
</table>

<p><button type="button" class="px-xl-5 mt-4 btn btn-light" href="#" onClick="history.back(); return false;">戻る</button></p>

<div class="d-flex justify-content-center">
  {{ $contacts->appends(request()->query())->links() }}
</div>

<!-- Modal -->
<div class="modal fade" id="pending-modal" tabindex="-1" role="dialog" aria-labelledby="pendingModal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title">商談結果を登録してください</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('pending.post') }}" id="js-form" method="POST" data-enctype="multipart/form-data">
          @csrf
          <input type='hidden' name='type' value="1">
          <input type='hidden' id='contact-id' name='id' value=''>
          <button class="btn btn-info pending-submit mr20" type="2" id="dealUnsuccessful" dusk='bussiness-cancel'>商談不成立</button>
        @if(isFc())
          <button class="btn btn-warning pending-submit mr20" type="0" id="pending" dusk='keep-pending'>顧客返答待ち</button>
        @endif
          <button class="btn btn-success pending-submit mr20" type="1" id="dealSuccessful" dusk='quotation-success'>商談成立</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Modal-->

  @else
  {{ $contacts}}
  @endif

@endsection
