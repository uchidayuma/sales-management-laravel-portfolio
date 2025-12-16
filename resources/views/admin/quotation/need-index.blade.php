@extends('layouts.layout')

@section('before-bootstrap-css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
@endsection

@section('css')
<link href="{{ asset('plugins/dropzone/basic.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/dropzone/dropzone.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/quotation/admin-create.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/dropzone/dropzone.js') }}" defer></script>
<script src="{{ asset('js/contact/admin-quotation.js') }}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
      <th scope="col">案件No</th>
      <th scope="col">問い合わせ日</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">見積もり</th>
      <th scope="col">見積もり一覧</th>
      <th scope="col">送信</th>
    </tr>
  </thead>
  <tbody class="common-table-stripes-row-tbody">
    @foreach($contacts as $c)
    <tr>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a></td>
      <td class="common-table-stripes-row-tbody__td">{{date('Y年m月d日', strtotime($c->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td"><a href="{{ route('quotations.create', ['id' => $c->id]) }}" class="btn btn-primary" dusk='create'>作成</a></td>
      <td class="common-table-stripes-row-tbody__td"><button class="btn btn-secondary js-quotations" contactId="{{ $c->id}}" type='button' data-toggle="modal" data-target="#quotations-modal">確認</button></td>
      <td class="common-table-stripes-row-tbody__td"><button class="btn btn-danger js-submit-modal-open" contactId="{{ $c->id}}" email="{{ $c->email }}" type='button' dusk="dispatch{{ $c->id }}">送信</button></td>
    </tr>
    @endforeach
  </tbody>
</table>

<!-- 見積もり書一覧モーダル -->
<!-- <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="quotations-modal"> -->
  <div class="modal fade" id="quotations-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectModalLabel">案件No.<span class='js-contact-id'></span>の見積もり書一覧</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="js-quotations-body modal-body row p30">
      </div>
    </div>
  </div>
</div>

<!-- 送信用モーダル -->
<div id='remodal' class="remodal mauto" data-remodal-id="modal">
  <button data-remodal-action="close" class="remodal-close"></button>
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  <section class='d-flex'>
    <form id='dispatch-form' class='w70 mr20' action="{{ route('quotations.admin.mail') }}" method="POST">
      <h2 class='h2 mb30'>案件No.<span class='js-contact-id'></span>への見積書送付</h2>
      @csrf
      <input type='hidden' class='js-contact-id' name='contact_id' value=""/>
      <input type='hidden' class='js-email' name='email' value=""/>
      <input type='hidden' class='js-name' name='name' value=""/>
      <h3 class='h4 mb40'>添付する見積書を選択してください*</h3>
      <section class='js-dispatch-body d-flex flex-wrap mb20'></section>
      <textarea class='form-control w100 mb20' name='body' placeholder='顧客への個別メッセージはこちらへ入力' rows='7' dusk='body'></textarea>
      <button data-remodal-action="cancel" class="remodal-cancel btn btn-secondary btn-lg mr30">キャンセル</button>
      <button type='submit' id='dispatch' class="btn btn-primary btn-lg" onclick="return confirm('選択した見積書を送信してよろしいですか？')" dusk='dispatch-submit' disabled>送信</button>
    </form>
    <form action="{{ url('/quotations/file/ajax/upload')}}" method="POST" class="dropzone w30" id="dropzone" enctype="multipart/form-data">
      @csrf
      <input type="hidden" class='js-contact-id-dropzone' name="contact_id" value=""/>
      <input type="file" name="file" hidden/>
    </form>
  </section>
</div>
@endsection
