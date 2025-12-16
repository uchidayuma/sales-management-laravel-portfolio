<form id="materialQuotationForm" class='quotation-form br5' action="{{ route('quotations.material.store', ['id' => $contact->id] ) }}" method="POST">
  @csrf
  <input class='js-add' type='hidden' value='0' name='add' />
  <input class='js-add-copy' type='hidden' value='0' name='add-copy' />
  <input  type='hidden' value='1' name='q[type]' />
  <h3 class="text-center font-weight-bold text-uppercase py-4 bg-white"><input type='text' name='q[name]' class='form-control' duks="title" value="{{ old('q.name') ? old('q.name') : '御見積書' }}" placeholder='見積もり書名を入力'></h3>
  <div class='mb20 d-flex justify-content-between'>
    <div class='client-wrapper w45per'>
      <h4 class='quotation-target h4 bold'><input type='text' class='form-control' value="{{ isCompany($contact) ? $contact->company_name : $contact->surname.$contact->name }} {{ isCompany($contact) ? '御中' : '様' }}" placeholder="見積もり相手の名前" name='q[client_name]' dusk='client_name'></h4>
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
            <td id="material-sub-total"></td>
            <td id='material-tax'></td>
            <td id='material-total' class="total"></td>
          </tr>
        </tbody>
      </table>
  @if(!empty($user->account_infomation1))
      <select class="js-account-infomation custom-select w30 my-3"  dusk='account_info_select'>
        <option value="{!! $user->account_infomation1 !!}">口座１</option>
    @if(!empty($user->account_infomation2))
        <option value="{!! $user->account_infomation2 !!}">口座２</option>
    @endif
    @if(!empty($user->account_infomation3))
        <option value="{!! $user->account_infomation3 !!}">口座３</option>
    @endif
      </select>
  @endif
    </div>

    <div class='company-wrapper w30per'>
      <p class='company-wrapper__info d-flex align-items-center mb10'>見積日：<input data-provide="datepicker" class="form-control datepicker js-date w70per" type="datetime" name="q[created_at]" value="{{ old('q.created_at') ? old('q.created_at') : date('Y-m-d') }}"/></p>
      <p class='company-wrapper__info d-flex align-items-center mb10'>有効期限：<input class="form-control  w40" type="text" name="q[effective_date]" value="{{ old('q.effective_date') ? old('effective_date') : '1ヶ月' }}"/></p>
      <h4 class='h4 bold'>{{ !empty($user->name) ? $user->name : $user->company_name }}</h4>
      <p class='company-wrapper__info d-flex align-items-center mb-1'>担当者：<input type="text" name="q[staff_name]" value="{{ !is_null($user->staff) ? $user->staff : '' }}" class="form-control w70"/></p>
      <p class='company-wrapper__info'>tel:{{ !empty($user->tel) ? $user->tel : '' }}</p>
      <p class='company-wrapper__info'>〒{{ $user->zipcode }}</p>
      <p class='company-wrapper__info mb10'>{{ $user->pref }}{{ $user->city }}{{ $user->street }}
    @if(!empty($user->seal))
      <img class='company-wrapper__seal' src="/images/seals/{{ $user->seal }}">
    @endif
    </div>
  </div>
  @if(!empty($_GET['copy']))
    <textarea class="js-payee payee form-control p10 mb20" name="q[payee]" rows="5" placeholder="振込先口座番号などはこちらにご記入ください。&#13;&#10;サンプル銀行&#13;&#10;本店&#13;&#10;普通 0000000&#13;&#10;サンプル株式会社">{!! old('q.payee') ? old('q.payee') : ($base_quotation[0]['payee'] ?? '') !!}</textarea>
  @else
    <textarea class="js-payee payee form-control p10 mb20" name="q[payee]" rows="5" placeholder="振込先口座番号などはこちらにご記入ください。&#13;&#10;サンプル銀行&#13;&#10;本店&#13;&#10;普通 0000000&#13;&#10;サンプル株式会社">{!! old('q.payee') ? old('q.payee') : ($user->account_infomation1 ?? '') !!}</textarea>
  @endif

  <div class='culc-wrapper p20 mb20'>
    <input type="hidden" name="q[contact_id]" value="{{ $contact->id }}">
    <input type="hidden" name="q[sub_total]" value="">
    <input type="hidden" name="q[total]" value="">

    <h4 class='h4'>人工芝反物</h4>
    <table class="table table-responsive-md text-center" id="turf-table">
      <thead>
        <tr>
          <th class="text-center">商品名</th>
          <th class="text-center">数量</th>
          <th class="text-center">単位</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
          <th class="text-center">備考欄</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody id='product-body'>
      </tbody>
    </table>
    <button type='button' id='js-turf-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>人工芝反物行を追加</button>

    <h4 class='h4'>人工芝切り売り</h4>
    <table class="table table-responsive-md text-center" id="cut-turf-table">
      <thead>
        <tr>
          <th class="text-center"></th>
          <th class="text-center">詳細</th>
          <th class="text-center">幅（m)</th>
          <th class="text-center">長さ（m)</th>
          <th class="text-center">面積（㎡）</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
          <th class="text-center">備考欄</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody id='product-cut-body'>
      </tbody>
    </table>
    <div id='total-cut-price' class='d-none'>
      <h4 class='f10 mb0 mr5'>切り売り人工芝カット賃合計</h4>
      <input class="form-control js-material-price w15 mr5" id='material-total-cut-price' value="{{old('q.discount')}}" placeholder="1000" readonly><p class='f10'>円</p>
    </div>
    <button type='button' id='js-cut-turf-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>人工芝切り売り行を追加</button>
    

    <h4 class='h4 d-none'>副資材バラ売り</h4>
    <table class="table table-responsive-md text-center js-cut-sub-area d-none">
      <thead>
        <tr>
          <th class="text-center"></th>
          <th class="text-center">詳細</th>
          <th class="text-center">数量</th>
          <th class="text-center">単位</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
          <th class="text-center">備考欄</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody id='cut-sub-body'>
      </tbody>
    </table>
    <br>
    <button type='button' id='js-cut-sub-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>副資材バラ売り行を追加</button>

    <h4 class='h4 js-etc-area'>その他メニューにない商品</h4>
    <table class="table table-responsive-md text-center js-etc-area">
      <thead>
        <tr>
          <th class="text-center"></th>
          <th class="text-center">注文名</th>
          <th class="text-center">数量</th>
          <th class="text-center">単位</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
          <th class="text-center">備考欄</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody id='etc-body'>
      </tbody>
    </table>
    <br>
    <button type='button' id='js-etc-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>その他を追加</button>
    <h4 class='h4 js-minus-area'>割引</h4>
    <div class='d-flex align-items-center mb30'>
      <input class="form-control js-material-price w15 mr5" id='material-discount' name="q[discount]" value="{{old('q.discount')}}" placeholder="1000"><p class='f12'>円</p>
    </div>
  @if(!empty($_GET['copy']))
      <textarea class="js-memo memo p10 mb20" name="q[memo]" rows="5" placeholder="見積書全体への備考欄はこちらに">{{ old('q.memo') ? old('q.mamo') : ($base_quotation[0]['quotation_memo'] ?? '') }}</textarea>
  @else
      <textarea class="js-memo memo p10 mb20" name="q[memo]" rows="5" placeholder="見積書全体への備考欄はこちらに">{{ old('q.memo') ? old('q.mamo') : ($user->quotation_memo ?? '') }}</textarea>
  @endif
    <input type="submit" class="btn btn-info align-right" id="post-material-quotation" value="{{ strpos(url()->current(),'edit') === false ? '見積もりを作成' : 'コピーして見積もりを作成' }}">
  </div>
</form>