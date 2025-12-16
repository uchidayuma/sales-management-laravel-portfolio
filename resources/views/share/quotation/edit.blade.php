@extends('layouts.layout')

@section('before-bootstrap-css')
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
@endsection

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('styles/quotation/create.min.css?20210812') }}" rel="stylesheet" />
<link href="{{ asset('styles/quotation/material-create.min.css?20230113') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script>
  window.products = @json($products);
  window.pq =@json(old('pq') ? old('pq') : []);
  window.quotations =@json($quotations);
  window.subTotal = 0;
  window.discount = {{ !empty($quotations[0]['discount']) ? $quotations[0]['discount'] : 0}}
  window.user = @json(\Auth::user());
  window.appEnv = "{{ \App::environment() }}";
  window.quotationTaxOption = @json($quotation_tax_option);
</script>
  @if($quotations[0]['type']==1)
<script>
  window.allProducts = @json($products);
  window.turfs = @json($turfs);
  window.subItems = @json($subItems);
  window.cutItems = @json($cutItems);
</script>
  @endif
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('plugins/dropzone/dropzone.js') }}" defer></script>
<script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('js/quotation/helper.js?20230323') }}" defer></script>
  @if($quotations[0]['type']==0)
<script src="{{ asset('js/quotation/create.js?20210812') }}" defer></script>
  @elseif($quotations[0]['type']==1)
<script src="{{ asset('js/quotation/material-create.js?20220602') }}" defer></script>
  @endif
@endsection

@section('content')
{!! $breadcrumbs->render() !!}

<!-- 見積もり項目テーブル -->
<div class="card bg-white p-5">
@if ($errors->any())
  <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <ul>
  @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
  @endforeach
    </ul>
  </div>
@endif
@if($quotations[0]->type=='0')
  <form id="quotationForm" class='quotation-form br5' action="{{ route('quotations.update', ['id' => $quotations[0]['contact->id']] ) }}" method="POST">
@elseif($quotations[0]->type=='1')
  <form id="materialQuotationForm" class='quotation-form br5' action="{{ route('quotations.material-update', ['id' => $quotations[0]['contact->id']] ) }}" method="POST">
@endif
    @csrf
    <input id='js-add' type='hidden' value='0' name='add' />
    <input id='quotation_id' type='hidden' value="{{ $quotations[0]['quotation_id'] }}" name='q[id]' />
    <input type='hidden' value="{{ $quotations[0]['quotation_user_id'] }}" name='row_userid' />
    <h3 class="text-center font-weight-bold text-uppercase py-4 bg-white"><input type='text' name='q[name]' class='form-control' dusk="title" value="{{ old('q.name') ? old('q.name') : $quotations[0]['quotation_name'] }}" placeholder='見積もり書名を入力'></h3>
    <div class='mb20 d-flex justify-content-between'>
      <div class='client-wrapper w45per'>
        <h4 class='quotation-target h4 bold'><input type='text' class='form-control' value="{{ $quotations[0]['client_name'] }}" placeholder="見積もり相手の名前" name='q[client_name]' dusk='name'></h4>
        <p class='small mb20'>下記の通りお見積もり申し上げます。</p>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td scope="col" class="">小計(円)</td>
              <td scope="col" class="">消費税(円)</td>
              <td scope="col" class="">合計金額(円)</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td id="subTotal">{{ number_format($quotations[0]['sub_total']) }}</td>
              <td id='tax'>{{ number_format($quotations[0]['sub_total'] * 0.1) }}</td>
              <td id='total' class="total">{{ number_format($quotations[0]['total']) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class='company-wrapper w30per'>
        <p class='company-wrapper__info d-flex align-items-center mb10'>見積日：<input data-provide="datepicker" class="form-control datepicker js-date w70per" type="datetime" name="q[created_at]" value="{{ old('q.created_at') ? old('q.created_at') : date('Y-m-d', strtotime($quotations[0]['created_quotation'])) }}"/></p>
        <p class='company-wrapper__info'><label>見積もり書No:</label> {{ $quotations[0]['quotation_id'] }}</p>
        <h4 class='h4 bold'>{{ $user->company_name }}</h4>
        <p class='company-wrapper__info d-flex align-items-center mb-1'>担当者：<input type="text" name="q[staff_name]" value="{{ !empty($quotations[0]['staff_name']) ? $quotations[0]['staff_name'] : '' }}" class="form-control w70"/></p>
        <p class='company-wrapper__info'>tel:{{ !empty($quotations[0]->fc_tel) ? $quotations[0]->fc_tel : '' }}</p>
        <p class='company-wrapper__info'>〒{{ $quotations[0]->fc_zipcode }}</p>
        <p class='company-wrapper__info'>{{ $quotations[0]->fc_pref }}{{ $quotations[0]->fc_city }}{{ $quotations[0]->fc_street }}
      @if($user->seal)
        <img class='company-wrapper__seal' src="/images/seals/{{ $user->seal }}">
      @endif
      </div>
    </div>

    <div class='culc-wrapper p20 mb20'>
      <input type="hidden" name="q[contact_id]" value="{{ $quotations[0]['contact_id'] }}">
      <input type="hidden" name="q[sub_total]" value="">
      <input type="hidden" name="q[total]" value="">
      <div id="table" class="table-editable">
    @if($quotations[0]->type == 0)
      @include('share.quotation.nomal-edit')
    @elseif($quotations[0]->type == 1)
      @include('share.quotation.material-edit')
    @endif
        <!-- <div class='d-flex'>
          <p class='plus-button mb20 js-plus'><i class="fas fa-plus-circle"></i> 商品行を追加</p>
      @if(empty($quotations[0]['discount']))
          <p class='plus-button mb20 js-discount-plus ml20'><i class="fas fa-plus-circle"></i> 値引き行を追加</p>
      @endif
        </div> -->
        <textarea class="memo p10 mb20" name="q[memo]" rows="5" placeholder="備考欄">{{ $quotations[0]['quotation_memo'] }}</textarea>
      </div>
    </div>
    <input type="submit" class="btn btn-info align-right" id="{{$quotations[0]->type=='0' ? 'post-quotation' : 'post-material-quotation'}}" value='見積もり書を更新'>
  </form>
</div><!-- card -->

<!-- 見積書アップモーダル -->
<div class="remodal w80" data-remodal-id="upload-modal" data-remodal-options="closeOnOutsideClick: false">
  <button data-remodal-action="close" class="remodal-close" dusk="remodal-close"></button>
  <h2 class='h2 mb30'>見積書アップロード</h2>
  <form action="{{ route('quotations.ajax.parse') }}" method="POST" class="dropzone dz-clickable" id="dropzone" enctype="multipart/form-data">
    @csrf
    <input type="hidden" class="js-contact-id-dropzone" name="contact_id" value="">
    <input type="file" name="file" hidden="">
    <div class="dz-default dz-message"><button class="dz-button" type="button">ここをクリックか、ドラッグ＆ドロップしてください</button></div>
  </form>
</div>
<!-- 見積もり項目テーブル -->

@endsection
