@extends('layouts.layout')

@section('css')
  <link href="{{ asset('styles/product/index.min.css') }}" rel="stylesheet">
@endsection

@section('javascript')
<script src="{{ asset('js/product/index.js') }}" defer></script>
@endsection

@section('content')
<!-- タブ部分 -->
<div class="flex-center position-ref full-height" style='position: relative;'>
  <div class="content form-group">
{!! $breadcrumbs->render() !!}
  @if(isFc())
    <div class='stock-status-desctiption mb10 mt10 d-flex align-items-center justify-content-end'>
      <p class='stock-status-desctiption__item mr20'><span class='btn btn-primary btn-stock--mini stock-high'> </span>在庫に余裕あり</p>
      <p class='stock-status-desctiption__item mr20'><span class='btn btn-warning btn-stock--mini stock-middle'> </span>在庫微妙</p>
      <p class='stock-status-desctiption__item'><span class='btn btn-danger btn-stock--mini stock-low'> </span>在庫逼迫</p>
    </div>
  @endif
    <table class="common-table-stripes-column">
      <thead>
        <tr>
          <th class="common-table-stripes-row-thead">製品名</th>
          <th class="common-table-stripes-row-thead">価格</th>
      @if(isAdmin())
          <th class="common-table-stripes-row-thead">在庫数</th>
      @else
          <th class="common-table-stripes-row-thead">在庫状況</th>
      @endif
        </tr>
      </thead>
      <tbody>
  @foreach($products as $product)
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <a href="{{ route('products.show', ['id' => $product->id])}}" class="common-table-stripes-column-item">{{ $product->name }}</a>
          </th>
          <td class="common-table-stripes-column__td">
            <p class="common-table-stripes-column-item f11">{{ number_format($product->price) }} 円/ {{ $product->unit}}</p>
          </td>
      @if(isAdmin())
          <td class="common-table-stripes-column__td common-table-stripes-column__td--stock d-flex justify-content-start align-items-center">
            <input type='number' class='form-control stock-input' name='stock' size='5' value="{{$product->stock}}">
            <button class='stock-update btn btn-danger disabled' type='button' id="{{$product->id}}">在庫量を更新</button>
          </td>
      @else
          <td class="common-table-stripes-column__td common-table-stripes-column__td--stock d-flex justify-content-between align-items-center w50 ml10">
            {!! isStockMargin($product->stock_high, $product->stock_low, $product->stock) !!}
          </td>
      @endif
        </tr> <!-- common-table-stripes-column__tr -->
  @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
