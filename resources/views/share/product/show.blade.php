@extends('layouts.layout')

@section('css')
  <link href="{{ asset('styles/product/show.min.css') }}" rel="stylesheet">
@endsection

@section('javascript')
<script src="{{ asset('js/product/index.js') }}" defer></script>
@endsection

@section('content')
<!-- タブ部分 -->
{!! $breadcrumbs->render() !!}
  <table class="common-table-stripes-column mb30">
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label"> No</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ $product->id }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">登録日</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ date('Y年m月d日', strtotime($product->created_at)) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">商品名</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ $product->name }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">販売価格</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->price) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">FC価格</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->fc_price) }}</p>
      </td>
  <!-- 卸売り価格がある場合表示 -->
@if(!empty($product->whole_price))
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">卸売り価格</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->whole_price) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@endif
<!-- 切売価格がある場合表示 -->
@if(!empty($product->cut_price))
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">{{ $product->product_type_id === 1 ? '切売価格' : 'バラ売り価格' }}</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->cut_price) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@endif
@if(!empty($product->cut_fc_price))
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">{{ $product->product_type_id === 1 ? 'FC切売価格' : 'FCバラ売り価格' }}</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->cut_fc_price) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@endif
  <!-- 卸売り切売価格がある場合表示 -->
@if(!empty($product->cut_whole_price))
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">{{ $product->product_type_id === 1 ? '卸売り切売価格' : '卸売りバラ売り価格' }}</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ number_format($product->cut_whole_price) }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@endif
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">規格サイズ</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ $product->specification }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">商品仕様</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ $product->material }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">特徴</p>
      </th>
      <td class="common-table-stripes__td">
        <p class="common-table-stripes-item">{{ $product->characteristic }}</p>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@if(!empty($product->url))
    <tr class="common-table-stripes__tr">
      <th class="common-table-stripes-column__th">
        <p class="common-table-stripes-item__label">商品資料</p>
      </th>
      <td class="common-table-stripes__td">
        <a href="{{ $product->url }}" target='blank' class="p-4">商品資料ダウンロードページ<i class="color-link pointer fas fa-external-link-alt common-table-stripes-item"></i></a>
      </td>
    </tr> <!-- common-table-stripes__tr -->
@endif
  </table>
  <div class='d-flex justify-content-end align-items-center'>
    <a class='btn btn-secondary mr10' href="{{ route('products.index') }}">戻る</a>
    <a class='btn btn-primary w15' href="{{ route('products.edit', ['id' => $product->id]) }}" {{adminOnlyHidden()}}>編集する</a>
  </div>
@endsection