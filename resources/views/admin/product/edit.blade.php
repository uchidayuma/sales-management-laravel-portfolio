@extends('layouts.layout')

@section('css')
  <link href="{{ asset('styles/product/show.min.css') }}" rel="stylesheet">
  <link href="{{ asset('styles/product/create.min.css') }}" rel="stylesheet">
@endsection
@section('javascript')
<script src="{{ asset('js/product/index.js') }}" defer></script>
@endsection
@section('content')
<!-- タブ部分 -->
{!! $breadcrumbs->render() !!}
<form class='form-group' action="{{ route('products.update', ['id' => $product->id]) }}" method="POST">
  @csrf
  <table class="common-table-stripes-column">
    <tr>
      <th class="common-table-stripes-columns__th w20">No</th>
      <td class="common-table-stripes-column__td form-control--p">{{ $product->id }}</td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">登録日</th>
      <td class="common-table-stripes-column__td form-control--p">{{ date('Y年m月d日', strtotime($product->created_at)) }}</td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">商品名</th>
      <td class="common-table-stripes-column__td"><input type="text" class="form-control common-table-stripes-column__input" name='p[name]' value="{{ $product->name }}"></td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">販売価格</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[price]' value="{{ $product->price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->unit }}</p>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">FC価格</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[fc_price]' value="{{ $product->fc_price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->unit }}</p>
      </td>
    </tr>
<!-- 卸売り価格がある場合表示 -->
    <tr>
      <th class="common-table-stripes-column__th w20">卸売り価格</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[whole_price]' value="{{ $product->whole_price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->unit }}</p>
      </td>
    </tr>
<!-- 切売価格がある場合表示 -->
    <tr>
      <th class="common-table-stripes-column__th w20">{{ $product->product_type_id == 1 ? '切売価格' : 'バラ売り価格' }}</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[cut_price]' value="{{ $product->cut_price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->cut_unit }}</p>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">{{ $product->product_type_id == 1 ? 'FC切売価格' : 'FCバラ売り価格' }}</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[cut_fc_price]' value="{{ $product->cut_fc_price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->cut_unit }}</p>
      </td>
    </tr>
  <!-- 卸売り切売価格がある場合表示 -->
    <tr>
      <th class="common-table-stripes-column__th w20">{{ $product->product_type_id == 1 ? '卸売り切売価格' : '卸売りバラ売り価格' }}</th>
      <td class="common-table-stripes-column__td d-flex mb-0">
        <input type="number" class="form-control common-table-stripes-column__input w20 mr-2" name='p[cut_whole_price]' value="{{ $product->cut_whole_price }}">
        <p class="text-nowrap mt-auto mb-0 h5">円/ {{ $product->cut_unit }}</p>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">規格サイズ</th>
      <td class="common-table-stripes-column__td">
        <textarea class="form-control common-table-stripes-column__input" name='p[specification]' rows="2">{{ $product->specification }}</textarea>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">商品仕様</th>
      <td class="common-table-stripes-column__td">
        <textarea class="form-control common-table-stripes-column__input" name='p[material]' rows="4">{{ $product->material }}</textarea>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">特徴</th>
      <td class="common-table-stripes-column__td">
        <textarea class="form-control common-table-stripes-column__input" name='p[characteristic]' rows="6">{{ $product->characteristic }}</textarea>
      </td>
    </tr>
    <tr>
      <th class="common-table-stripes-column__th w20">商品資料</th>
      <td class="common-table-stripes-column__td"><input type="text" class="form-control common-table-stripes-column__input" name='p[url]' value="{{ !empty($product->url) ? $product->url : '' }}" placeholder="https://drive.google.com/drive/u/0"></td>
    </tr>
  </table>
  <div class='mt-4 d-flex justify-content-between'>
    <a class='btn btn-secondary px-xl-5' href="{{ route('products.show', ['id' => $product->id]) }}">戻る</a>
    <button id='update-submit' class='btn btn-primary px-xl-5' type='submit'>確定する</button>
  </div>
</form>
@endsection