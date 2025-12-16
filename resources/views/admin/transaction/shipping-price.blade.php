@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/transaction/setting.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/bootstrap-multiselect/bootstrap-multiselect.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/popper/popper.min.js') }}"></script>
<script src="/plugins/bootstrap-multiselect/bootstrap-multiselect.min.js" defer></script>
<script src="/js/setting/shipping-price.js" defer></script>
@endsection


@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<h2 class='mb-2'>送料無料キャンペーン設定</h2>
<form class='d-flex flex-start mb-5' method="POST" action="{{route('config.update-free-item')}}">
  @csrf
  <select id="multiple" multiple="multiple" class='multiple' name="free-items[]"  dusk="free-shipping-item-id-select">
    <option value="" {{selected($free_shipping_item_id === '[null]')}}>設定しない</option>
@foreach($turfs AS $t)
    <option value="{{$t['id']}}" {{ in_array($t['id'], json_decode($free_shipping_item_id)) ? 'selected': '' }}>{{$t['name']}}</option>
@endforeach
  </select>
  <button class='btn btn-primary ml-3' type="submit" dusk="free-shipping-item-id-submit">更新</button>
</form>

<div class="alert alert-warning">ボタンを押すまでは保存されません。できるだけこまめに保存をしていただきますようにお願い致します。</div>
<form class="table-container" method="POST" action="{{route('transactions.admin.shipping-price-update')}}">
  @csrf
  <button type="submit" class="btn btn-primary mb10">保存</button>
  <table class="table table-bordered table-striped table-hover bg-white">
    <thead>
      <tr class="">
        <th class="table table-bordered" rowspan="2">送り先</th>
  @foreach($regions AS $r)
        <th class="table table-bordered">{{$r['name']}}</th>
  @endforeach
      </tr>
      <tr>
  @foreach($regions AS $r)
      <th class="table table-bordered th2">
      @foreach($r['prefectures'] AS $p)
        <p>{{$p}}</p>
      @endforeach
        </th>
  @endforeach
      </tr>
    </thead>
    <tbody>
      <tr><td>小サイズ</td><td colspan="12">＜対象商品＞ 副資材（防草シート除く） 人工芝 幅50cm以下 且つ 3㎡以下</td></tr>
      <tr>
        <td></td>
    @foreach($regions AS $r)
        <td><input name="r[{{$r['id']}}][small_shipping_price]" value="{{$r['small_shipping_price']}}" size="7"/></td>
    @endforeach
      </tr>
      <tr><td>大サイズ</td><td colspan="12">＜対象商品＞ 人工芝4.5㎡未満 もしくは 10㎡未満・防草シート</td></tr>
      <tr>
        <td></td>
    @foreach($regions AS $r)
        <td><input name="r[{{$r['id']}}][large_shipping_price]" value="{{$r['large_shipping_price']}}" size="7"/></td>
    @endforeach
      </tr>
      <tr><td>特大サイズ</td><td colspan="12">＜対象商品＞ 人工芝4.5㎡以上 もしくは 10㎡以上 実重45kg 〜 90kg 3辺280超</td></tr>
      <tr>
        <td>1反</td>
    @foreach($regions AS $r)
        <td><input name="r[{{$r['id']}}][extra_large_shipping_price]" value="{{$r['extra_large_shipping_price']}}" size="7"/></td>
    @endforeach
      </tr>
      <tr>
        <td>2反<br>（1反あたり）</td>
    @foreach($regions AS $r)
        <td><input name="r[{{$r['id']}}][extra_large_shipping_price2]" value="{{$r['extra_large_shipping_price2']}}" size="7"/></td>
    @endforeach
      </tr>
      <tr>
        <td>3反以上<br>（1反あたり）</td>
    @foreach($regions AS $r)
        <td><input name="r[{{$r['id']}}][extra_large_shipping_price3]" value="{{$r['extra_large_shipping_price3']}}" size="7"/></td>
    @endforeach
      </tr>
      <tr><td>チャーター便</td><td colspan="12">＜対象商品＞ 人工芝4.5㎡以上 もしくは 10㎡以上 実重45kg 〜 90kg 3辺280超</td></tr>
      <tr>
        <td></td>
        <td>要相談</td>
        <td><!-- 北東北リージョン -->
          青森<input name="p[{{$prefectures[1]['id']}}]" size="8" class='mb10' value="{{$prefectures[1]['charter_shipping_price']}}""/>
          岩手<input name="p[{{$prefectures[2]['id']}}]" size="8" class='mb10' value="{{$prefectures[2]['charter_shipping_price']}}""/>
          秋田<input name="p[{{$prefectures[4]['id']}}]" size="8" class='mb10' value="{{$prefectures[4]['charter_shipping_price']}}""/>
        </td>
        <td>
          宮城<input name="p[{{$prefectures[3]['id']}}]" size="8" class='mb10' value="{{$prefectures[3]['charter_shipping_price']}}""/>
          山形<input name="p[{{$prefectures[5]['id']}}]" size="8" class='mb10' value="{{$prefectures[5]['charter_shipping_price']}}""/>
          福島<input name="p[{{$prefectures[6]['id']}}]" size="8" class='mb10' value="{{$prefectures[6]['charter_shipping_price']}}""/>
        </td>
        <td>
          茨城<input name="p[{{$prefectures[7]['id']}}]" size="8" class='mb10' value="{{$prefectures[7]['charter_shipping_price']}}""/>
          栃木<input name="p[{{$prefectures[8]['id']}}]" size="8" class='mb10' value="{{$prefectures[8]['charter_shipping_price']}}""/>
          群馬<input name="p[{{$prefectures[9]['id']}}]" size="8" class='mb10' value="{{$prefectures[9]['charter_shipping_price']}}""/>
          埼玉<input name="p[{{$prefectures[10]['id']}}]" size="8" class='mb10' value="{{$prefectures[10]['charter_shipping_price']}}""/>
          千葉<input name="p[{{$prefectures[11]['id']}}]" size="8" class='mb10' value="{{$prefectures[11]['charter_shipping_price']}}""/>
          東京<input name="p[{{$prefectures[12]['id']}}]" size="8" class='mb10' value="{{$prefectures[12]['charter_shipping_price']}}""/>
          神奈川<input name="p[{{$prefectures[13]['id']}}]" size="8" class='mb10' value="{{$prefectures[13]['charter_shipping_price']}}""/>
          新潟<input name="p[{{$prefectures[14]['id']}}]" size="8" class='mb10' value="{{$prefectures[14]['charter_shipping_price']}}""/>
          山梨<input name="p[{{$prefectures[18]['id']}}]" size="8" class='mb10' value="{{$prefectures[18]['charter_shipping_price']}}""/>
        </td>
        <td>
          富山<input name="p[{{$prefectures[15]['id']}}]" size="8" class='mb10' value="{{$prefectures[15]['charter_shipping_price']}}""/>
          石川<input name="p[{{$prefectures[16]['id']}}]" size="8" class='mb10' value="{{$prefectures[16]['charter_shipping_price']}}""/>
          福井<input name="p[{{$prefectures[17]['id']}}]" size="8" class='mb10' value="{{$prefectures[17]['charter_shipping_price']}}""/>
          長野<input name="p[{{$prefectures[19]['id']}}]" size="8" class='mb10' value="{{$prefectures[19]['charter_shipping_price']}}""/>
        </td><!-- 北陸リージョン -->
        <td>
          岐阜<input name="p[{{$prefectures[20]['id']}}]" size="8" class='mb10' value="{{$prefectures[20]['charter_shipping_price']}}""/>
          静岡<input name="p[{{$prefectures[21]['id']}}]" size="8" class='mb10' value="{{$prefectures[21]['charter_shipping_price']}}""/>
          愛知<input name="p[{{$prefectures[22]['id']}}]" size="8" class='mb10' value="{{$prefectures[22]['charter_shipping_price']}}""/>
          三重<input name="p[{{$prefectures[23]['id']}}]" size="8" class='mb10' value="{{$prefectures[23]['charter_shipping_price']}}""/>
        </td><!-- 中部リージョン -->
        <td>
          滋賀<input name="p[{{$prefectures[24]['id']}}]" size="8" class='mb10' value="{{$prefectures[24]['charter_shipping_price']}}""/>
          京都<input name="p[{{$prefectures[25]['id']}}]" size="8" class='mb10' value="{{$prefectures[25]['charter_shipping_price']}}""/>
          大阪<input name="p[{{$prefectures[26]['id']}}]" size="8" class='mb10' value="{{$prefectures[26]['charter_shipping_price']}}""/>
          兵庫<input name="p[{{$prefectures[27]['id']}}]" size="8" class='mb10' value="{{$prefectures[27]['charter_shipping_price']}}""/>
          奈良<input name="p[{{$prefectures[28]['id']}}]" size="8" class='mb10' value="{{$prefectures[28]['charter_shipping_price']}}""/>
          和歌山<input name="p[{{$prefectures[29]['id']}}]" size="8" class='mb10' value="{{$prefectures[29]['charter_shipping_price']}}""/>
        </td><!-- 近畿リージョン -->
        {{-- 中国リージョン --}}
        <td>
          鳥取<input name="p[{{$prefectures[30]['id']}}]" size="8" class='mb10' value="{{$prefectures[30]['charter_shipping_price']}}""/>
          島根<input name="p[{{$prefectures[31]['id']}}]" size="8" class='mb10' value="{{$prefectures[31]['charter_shipping_price']}}""/>
          岡山<input name="p[{{$prefectures[32]['id']}}]" size="8" class='mb10' value="{{$prefectures[32]['charter_shipping_price']}}""/>
          広島<input name="p[{{$prefectures[33]['id']}}]" size="8" class='mb10' value="{{$prefectures[33]['charter_shipping_price']}}""/>
          山口<input name="p[{{$prefectures[34]['id']}}]" size="8" class='mb10' value="{{$prefectures[34]['charter_shipping_price']}}""/>
        </td>
        <td>
          徳島<input name="p[{{$prefectures[35]['id']}}]" size="8" class='mb10' value="{{$prefectures[35]['charter_shipping_price']}}""/>
          香川<input name="p[{{$prefectures[36]['id']}}]" size="8" class='mb10' value="{{$prefectures[36]['charter_shipping_price']}}""/>
          愛媛<input name="p[{{$prefectures[37]['id']}}]" size="8" class='mb10' value="{{$prefectures[37]['charter_shipping_price']}}""/>
          高知<input name="p[{{$prefectures[38]['id']}}]" size="8" class='mb10' value="{{$prefectures[38]['charter_shipping_price']}}""/>
        </td><!-- 四国リージョン -->
        <td>
          福岡<input name="p[{{$prefectures[39]['id']}}]" size="8" class='mb10' value="{{$prefectures[39]['charter_shipping_price']}}""/>
          佐賀<input name="p[{{$prefectures[40]['id']}}]" size="8" class='mb10' value="{{$prefectures[40]['charter_shipping_price']}}""/>
          長崎<input name="p[{{$prefectures[41]['id']}}]" size="8" class='mb10' value="{{$prefectures[41]['charter_shipping_price']}}""/>
          大分<input name="p[{{$prefectures[42]['id']}}]" size="8" class='mb10' value="{{$prefectures[42]['charter_shipping_price']}}""/>
        </td><!-- 北九州リージョン -->
        <td>
          熊本<input name="p[{{$prefectures[43]['id']}}]" size="8" class='mb10' value="{{$prefectures[43]['charter_shipping_price']}}""/>
          宮崎<input name="p[{{$prefectures[44]['id']}}]" size="8" class='mb10' value="{{$prefectures[44]['charter_shipping_price']}}""/>
          鹿児島<input name="p[{{$prefectures[45]['id']}}]" size="8" class='mb10' value="{{$prefectures[45]['charter_shipping_price']}}""/>
        </td><!-- 南九州リージョン -->
        <td>要相談</td>
      </tr>
    </tbody>
  </table>
  <button type="submit" class="btn btn-primary" dusk="shipping-price-submit">保存</button>
</form>

@endsection