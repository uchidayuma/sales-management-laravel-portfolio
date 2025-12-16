    <input type="hidden" name="q[sub_total]" value="">
    <input type="hidden" name="q[total]" value="">

    <h4 class='h4'>人工芝反物</h4>
    <table class="table table-responsive-md text-center" id="turf-table">
      <thead>
        <tr>
          <th class="text-center">詳細</th>
          <th class="text-center">数量</th>
          <th class="text-center">単位</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
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
    

    <h4 id='sub-heading' class='h4 d-none'>副資材バラ売り</h4>
    <table id='sub-heading-table' class="table table-responsive-md text-center js-cut-sub-area d-none">
      <thead>
        <tr>
          <th class="text-center"></th>
          <th class="text-center">詳細</th>
          <th class="text-center">数量</th>
          <th class="text-center">単位</th>
          <th class="text-center">単価(円)</th>
          <th class="text-center">金額</th>
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
      <input class="form-control w15 mr5" id='material-discount' name="q[discount]" value="{{old('q.discount')}}" placeholder="1000"><p class='f12'>円</p>
    </div>