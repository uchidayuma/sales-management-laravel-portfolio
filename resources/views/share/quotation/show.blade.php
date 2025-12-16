@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/quotation/create.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/quotation/create.js') }}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}

<a href="{{ route('quotations.download', ['id' => $quotations[0]->quotation_id]) }}" class='btn btn-primary mb20 mr10' target='blank' dusk='pdf-button'>PDFで表示・ダウンロード</a>
@if($isMainFc == true || isAdmin())
<a href="{{ route('quotations.edit', ['id' => $quotations[0]->quotation_id]) }}" class='btn btn-secondary mb20' dusk='edit'>編集</a>
@endif

<!-- 見積もり項目テーブル -->
<div class="card bg-white p-5">
    <h3 class="text-center font-weight-bold text-uppercase py-4 bg-white">{{ $quotations[0]->quotation_name }}</h3>
    <div class='mb20 d-flex justify-content-between'>
      <div class='client-wrapper w45per'>
        <h4 class='quotation-target h4 bold'>{{ $quotations[0]->client_name }}</h4>
        <p class='small mb20'>下記の通りお見積もり申し上げます。</p>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td scope="col" class="">10％対象計</td>
              <td scope="col" class="">消費税（10％）</td>
              <td scope="col" class="">税込合計金額</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td id="subTotal">{{ number_format($quotations[0]->sub_total) }}円</td>
              <td id='tax'>{{ number_format($quotations[0]->total - $quotations[0]->sub_total) }}円</td>
              <td id='total' class="total">{{ number_format($quotations[0]->total) }}円</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class='company-wrapper w30per'>
        <p class='d-flex align-items-center f11 mb10'>発行日：{{ date('Y年m月d日', strtotime($quotations[0]['created_quotation'])) }}</p>
        <p class='company-wrapper__info mb10'>見積書番号：{{ $quotations[0]->quotation_id }}</p>
        <h4 class='h4 bold'>{{ $quotations[0]->company_name }}</h4>
      @if(!is_null($quotations[0]->staff_name))
        <p class='company-wrapper__info d-flex align-items-center mb-1'>担当者：{{$quotations[0]->staff_name}}</p>
      @endif
        <p class='company-wrapper__info'>tel:{{ !empty($quotations[0]->fc_tel) ? $quotations[0]->fc_tel : '' }}</p>
        <p class='company-wrapper__info'>〒{{ $quotations[0]->fc_zipcode }}</p>
        <p class='company-wrapper__info'>{{ $quotations[0]->fc_pref }}{{ $quotations[0]->fc_city }}{{ $quotations[0]->fc_street }}
      @if($quotations[0]->seal)
        <img class='company-wrapper__seal' src="/images/seals/{{ $quotations[0]->seal }}">
      @endif
      <p class='company-wrapper__info d-flex align-items-center'>有効期限：{{ !empty($quotations[0]['effective_date']) ? $quotations[0]['effective_date'] : '1ヶ月' }}</p>
      @if(!is_null($quotations[0]->qualified_business_number))
        <p class='company-wrapper__info mb10'>登録番号：T{{ $quotations[0]['qualified_business_number'] }}</p>
      @endif
      </div>
    </div>
    @if(!empty($quotations[0]['payee']))
      <p>お振込先</p>
      <textarea class="js-payee payee form-control p10 mb20" rows="4" disabled>{{$quotations[0]['payee']}}</textarea>
    @endif

    <div class='culc-wrapper p20'>
    @if($quotations[0]->type == 0)
      @include('share.quotation.nomal-show')
    @elseif($quotations[0]->type == 1)
      @include('share.quotation.material-show')
    @endif
    </div>
</div><!-- card -->

<!-- 見積もり項目テーブル -->

@endsection
