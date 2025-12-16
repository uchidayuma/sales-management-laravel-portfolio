var rowCount = 1;
// 材料見積もりで使いことができるカットメニューリスト
const materialCutMenuList = [71, 55, 56];

$(document).ready(function () {
  // タブのアクティブ切替
  if (window.quotationType == 1) {
    $('#material-tab').tab('show');
  }
  if ((document.URL.match('new') && window.quotations.length === 0) || window.quotationType === 0) {
    addTurfRow();
    addCutTurfRow();
    materialSubTotal();
    addEtcRow('送料', 1, '式', null);
    // enterでsubmitを無効
    $("input").keydown(function (e) {
      if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
        return false;
      } else {
        return true;
      }
    });
  } else {
    if ((document.URL.match('new') && window.isCopy === 1 && window.quotationType == 1) || document.URL.match('edit')) {
      $('article').append('<aside class="loader"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i><p class="loader__message">金額を計算中...</p></aside>')
    }
    window.quotations.forEach(function (val, index) {
      // 反物人工芝
      if (val.product_id !== null && val.unit === '反' && val.vertical === null && val.horizontal === null) {
        addTurfRow(val.product_id, val.num, val.unit_price, val.memo);
        if (val.turf_cuts[0]) {
          var parentId = rowCount - 1
          val.turf_cuts.forEach(function (cut) {
            addFullCutMenuRow(cut.product_id, cut.num, cut.unit_price, cut.memo, parentId)
          })
        }
      }
      // 切り売り人工芝
      if (val.product_id !== null && val.vertical !== null && val.horizontal !== null) {
        addCutTurfRow(val.product_id, val.horizontal, val.vertical, val.num, val.unit_price, val.memo);
        if (val.turf_cuts[0]) {
          var parentId = rowCount - 1
          val.turf_cuts.forEach(function (cut) {
            addCutMenuRow(cut.product_id, cut.num, cut.unit, cut.unit_price, cut.memo, parentId)
          })
          if(val.cut_set_num != null){
            addCutSetNumRow(parentId, val.cut_set_num)
          }else{
            addCutSetNumRow(parentId, 1)
          }
        }
      }
      // 副資材
      if (val.product_id !== null && val.unit !== '反' && val.cut === 0 && val.vertical === null && val.horizontal === null) {
        addCutSubRow(val.product_id, val.num, val.unit, val.unit_price, val.memo)
        $('.js-cut-sub-area').prev('h4').removeClass('d-none');
        $('.js-cut-sub-area').removeClass('d-none');
      }
      if (val.product_id === null) {
        addEtcRow(val.other_product_name, val.num, val.unit, val.unit_price, val.memo);
      }
      // addEditRow(val);
    });
    console.log(window.quotations[0].created_at);
    $(".datepicker").flatpickr({
      enabletime: false,
      dateformat: "y-m-d",
      locale: "ja",
      defalutDate: new Date(window.quotations[0].created_at),
    });
    setTimeout(() => {
      $('#material-discount').val(window.quotations[0].discount);
      materialCutTotal();
    }, 1500);
    setTimeout(() => {
      cutLengthCalc();
      materialRowTotal();
    }, 2500);
    setTimeout(() => {
      materialSubTotal();
      // 編集時かつ、2枚以上なら入力欄を表示
      $('.js-cut-price-row').each(function (i, element) {
        const setCount = parseInt($(element).closest('tr').find('.cut-set-count').val()); 
        if( setCount > 1){
          $(element).removeClass('d-none');
        }
      })
      $('.loader').remove();
    }, 4000);
  }
  // 矢印キーでの金額のずれ防止
  $(document).on("focus", "input[type=number]", function() {
    $(this).on("keydown", function(event) {
      if (event.keyCode === 38 || event.keyCode === 40) {
        event.preventDefault();
      }
    });
  });
});

// 人工芝反物の行追加メソッド
function addTurfRow(productId = null, num = null, unitPrice = null, memo = '') {
  var productList = [];
  Object.keys(window.turfs).forEach(productKey => {
    if (window.turfs[productKey].price != 0) {
      productList.push(
        `<option value=${window.turfs[productKey].id} ${window.turfs[productKey].id === productId ? 'selected' : ''}>${window.turfs[productKey].name}（${window.turfs[productKey].horizontal}m × ${window.turfs[productKey].vertical}m）</option>`
      );
    }
  });
  var template = `
      <tr class="js-material-row dusk-row-${rowCount}">
        <td class='js-product-td'>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <div class="form-group">
            <select class="form-control js-turf-select js-material-product-select material-product-select" name="product-select-${rowCount}" dusk='turf-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count material-product-count form-control" dusk='turf-number${rowCount}' value=${num}>
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit unit form-control dusk-unit-${rowCount}" value="反" readonly>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price material-unit-price form-control dusk-unit-price-${rowCount}" value=${unitPrice}>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" class="js-material-price material-price form-control" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#product-body").append(template);
  $("#product-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 人工芝反物カットメニューの行追加メソッド
function addFullCutMenuRow(productId = null, num = null, unitPrice = null, memo = '', parentId = null) {
  console.log('addFullCutMenuRow');
  var productList = [];
  var products = window.allProducts.filter(function (value) {
    return value.product_type_id === 5;
  });
  productList.push(`<option value=''>カットメニューを選択</option>`)
  products.forEach(product => {
    productList.push(
      `<option value=${product.id} fc-price=${product.price} ${product.id === parseInt(productId) ? "selected" : ""}>${product.name}</option>`
    );
  });
  var template = `
      <tr class="js-material-row js-material-row--child dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' value="${parentId}"/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
            ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" value="${num}" class="js-material-product-count js-fabric-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="反分" readonly>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="${unitPrice}">
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $('.js-row-id').attr('value', parentId).closest(`.dusk-row-${parentId}`).after(template)
  // $("#turf-table").append(template)
  $("#turf-table").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 行の金額算出
$('#materialQuotationForm').on("keyup", ".js-material-product-count, .js-material-unit-price", function () {
  var count = $(this).closest(".js-material-row").find(".js-material-product-count").val();
  count = parseFloat(count);
  var unitPrice = $(this).closest(".js-material-row").find(".js-material-unit-price").val();

  var rowPrice = Math.floor(count * unitPrice);
  $(this).closest(".js-material-row").find(".js-material-price").val(rowPrice);
  $(this).closest(".js-material-row").find(".js-material-price").attr('value', rowPrice);

  // カット人工芝の大きさを変えると金額も変化させる
  calcCutSetPrice($(this))
  materialCutTotal()
  materialSubTotal();
});

// 割引額が変更されたら
$('#materialQuotationForm').on("keyup", "#discount,#material-discount", function () {
  materialSubTotal();
})

$('#materialQuotationForm').on("change", ".js-turf-select, .js-cut-turf-select, .js-sub-select, .js-cut-sub-select, .js-sales-select, .js-cut-select", function () {
  var productId = $(this).val();
  var target = window.allProducts.find(v => v.id == productId);
  // 単価入力を反物か切り売りかで分岐
  if ($(this).hasClass("js-turf-select")) {
    $(this).closest(".js-material-row").find(".js-material-unit-price").val(target.price);
    $(this).closest(".js-material-row").find(".js-material-unit-price").attr("value", target.price);
  } else if ($(this).hasClass("js-cut-turf-select")) {
    $('#total-cut-price').removeClass('d-none');
    $(this).closest(".js-material-row").find(".js-cut-unit-price").val(target.cut_price);
    $(this).closest(".js-material-row").find(".js-cut-unit-price").attr("value", target.cut_price);
  } else if ($(this).hasClass("js-cut-sub-select")) {
    $(this).closest(".js-material-row").find(".js-material-unit-price").val(target.cut_price);
    $(this).closest(".js-material-row").find(".js-material-unit-price").attr("value", target.cut_price);
    // もし通常は切り売りしない商品だったら
    if (!target.cut_price) {
      $(this).closest(".js-material-row").find(".js-material-unit-price").val(target.price);
      $(this).closest(".js-material-row").find(".js-material-unit-price").attr("value", target.price);
    }
  } else if ($(this).hasClass("js-cut-select")) {
    $(this).closest(".js-material-row").find(".js-material-unit-price").val(target.price);
    $(this).closest(".js-material-row").find(".js-material-unit-price").attr("value", target.price);
  }
  // 行の金額算出
  var count = parseFloat($(this).closest(".js-material-row").find(".js-material-product-count").val());
  var unitPrice = $(this).closest(".js-material-row").find(".js-material-unit-price").val();
  var rowPrice = Math.floor(count * unitPrice);
  $(this).closest(".js-material-row").find(".js-material-price").val(rowPrice);
  $(this).closest(".js-material-row").find(".js-material-price").attr('value', rowPrice);

  materialCutTotal()
  totalAreaCount();
  // カット人工芝の大きさを変えると金額も変化させる
  calcCutSetPrice($(this))
  materialSubTotal();
});

// 人工芝切り売りの行追加メソッド
function addCutTurfRow(productId = null, horizontal = null, vertical = null, num = 0, unitPrice = null, memo = '') {
  var productList = [];
  Object.keys(window.turfs).forEach(productKey => {
    productList.push(
      `<option value=${window.turfs[productKey].id} fc-price="${window.turfs[productKey].price}" ${window.turfs[productKey].id === productId ? 'selected' : ''}>${window.turfs[productKey].name}</option>`
    );
  });

  var template = `
      <tr class="js-material-row dusk-row-${rowCount}">
        <td class='w10 pl0 pr0'>
          <i class="fas fa-cut js-add-cut-menu pointer mb10"> カット追加</i>
          <i class="fas fa-plus-square js-add-cut-set pointer text-success bold"> 複数枚注文</i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='js-before-id' value="0" />
        </td>
        <td class='w35 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-turf-select js-material-product-select" name="product-select-${rowCount}" dusk='cut-turf-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" name="horizontal" class="js-cut-turf-horizontal js-horizontal form-control" dusk="horizontal" value=${roundFloatNumber(horizontal, 3)}>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" step="0.1" name="vertical" class="js-cut-turf-vertical js-vertical form-control" dusk="vertical" value=${roundFloatNumber(vertical, 3)}>
        </td>
        <td class='w10'>
          <input type="text" step="0.1" name="unit" value=${num} class="js-area js-material-product-count form-control dusk-unit-${rowCount}" readonly>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price js-cut-unit-price form-control dusk-unit-price-${rowCount}" value=${unitPrice}>
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="price" class="js-area-price js-material-price form-control js-cut-turf-price" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#product-cut-body").append(template);
  $("#product-cut-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

function addCutMenuRow(productId = null, num = null, unit = 'm', unitPrice = null, memo = '', parentId = null) {
  // カットメニューを追加するかの値 !=＝0ならカットメニューを追加しない
  if ((document.URL.match('new') && window.iscopy == 0) || window.quotationtype == 0) { return false; }
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    if (materialCutMenuList.includes(window.cutItems[productKey].id)) {
      productList.push(`
      <option value="${window.cutItems[productKey].id}" ${window.cutItems[productKey].id === parseInt(productId) ? "selected" : ""}>${window.cutItems[productKey].name}</option>
    `);
    }
  });
  var template = `
      <tr class="js-material-row js-material-row--child js-material-cut-row dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value="${parentId}">
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-cut-length form-control dusk-count-${rowCount}" value=${num}>
        </td>
        <td class=''>
          <input type="text" readonly name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${unit}">
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value=${unitPrice}>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" readonly class="js-material-price form-control js-cut-turf-price" value="0">
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#product-cut-body").append(template);
  $("#product-cut-body").sortable({
    handle: ".handle"
  });
  // カットする対象を紐付け
  var rowId = $(this).closest(".js-material-row").find(".js-row-id").val();
  $(this).closest(".js-material-row").next().find(".js-parent-id").val(rowId);

  rowCount++;
};

// 切り売り人工芝を選択した際にカットメニューを追加する
$('#materialQuotationForm').on("change", ".js-cut-turf-select", function () {
  var beforeId = $(this).closest(".js-material-row").find(".js-before-id").val();
  // カットメニューを追加するかの値 !=＝0ならカットメニューを追加しない
  if (beforeId !== '0') { return false; }
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    if (materialCutMenuList.includes(window.cutItems[productKey].id)) {
      productList.push(`<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`);
    }
  });

  var cutSetNum = 1; 

  var template = `
      <tr class="js-material-row js-material-row--child js-material-cut-row dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value=""/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="">
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control js-cut-turf-price" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
      <tr class="js-material-row js-cut-price-row d-none dusk-row-${++rowCount}">
        <td class='w5 pl0 pr0'>
        </td>
        <td class='w30 text-right'>
          枚数
        </td>
        <td class='w12'>
          <input type='hidden' class='js-parent-id' value="" /> 
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='cut-set-count' value="${cutSetNum}" />
          <input type="number" onwheel="return false;" class="js-cut-set-num form-control" dusk="turf-set-num" value="${roundFloatNumber(cutSetNum, 3)}">
        </td>
        <td class='w12'>
        </td>
        <td class='w10 text-right'>
          合計
        </td>
        <td class='w30' colspan='2'>
          <input type="number" onwheel="return false;" name="price" class="js-cut-set-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
        </td>
      </tr>
    `;
  $(this).closest(".js-material-row").after(template);

  //親と紐付けるためにrowIdを取得
  var rowId = $(this).closest(".js-material-row").find(".js-row-id").val();
  $(this).closest(".js-material-row").next().find(".js-parent-id").val(rowId);
  $(this).closest(".js-material-row").next().next().find(".js-parent-id").val(rowId);


  // 行の金額算出
  var count = parseFloat($(this).closest(".js-material-row").find(".js-material-product-count").val());
  var unitPrice = $(this).closest(".js-material-row").find(".js-material-unit-price").val();
  var rowPrice = Math.floor(count * unitPrice);
  $(this).closest(".js-material-row").find(".js-material-price").val(rowPrice);
  $(this).closest(".js-material-row").find(".js-material-price").attr('value', rowPrice);
  $(this).closest(".js-material-row").find(".js-before-id").val($(this).val());
  // カットメニューを追加するかの値＝0なら初期状態なので、カットメニューをappend()する
  materialSubTotal();
  rowCount++;
});
// カットメニューを追加ボタン
$('#materialQuotationForm').on("click", ".js-add-cut-menu", function () {
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    if (materialCutMenuList.includes(window.cutItems[productKey].id)) {
      productList.push(
        `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
      );
    }
  });
  var template = `
      <tr class="js-material-row js-material-row--child js-material-cut-row dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value=""/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="">
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" class="js-material-price js-cut-turf-price form-control" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $(this).closest(".js-material-row").after(template);
  // カットする対象を紐付け
  var rowId = $(this).closest(".js-material-row").find(".js-row-id").val();
  $(this).closest(".js-material-row").next().find(".js-parent-id").val(rowId);
  materialCutTotal()
  materialSubTotal();
  rowCount++;
});

// 人工芝切り売りの枚数入力行追加メソッド
function addCutSetNumRow(rowId = null, cutSetNum = 1) {
  var template = `
      <tr class="js-material-row js-cut-price-row d-none dusk-row-${rowCount}">
        <td class='w5 pl0 pr0'>
        </td>
        <td class='w30 text-right'>
          枚数
        </td>
        <td class='w12'>
          <input type='hidden' class='js-parent-id' value="${rowId}" /> 
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='cut-set-count' value="${cutSetNum}" />
          <input type="number" onwheel="return false;" class="js-cut-set-num form-control" dusk="turf-set-num" value="${roundFloatNumber(cutSetNum, 3)}">
        </td>
        <td class='w12'>
        </td>
        <td class='w10 text-right'>
          合計
        </td>
        <td class='w30' colspan='2'>
          <input type="number" onwheel="return false;" name="price" class="js-cut-set-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
        </td>
      </tr>
    `;
  $("#product-cut-body").append(template);
  $("#product-cut-body").sortable({
    handle: ".handle"
  });
  //js-before-idを紐付ける
  var beforeVal = $(`.js-row-id[value=${rowId}]`).closest('tr').find('.js-cut-turf-select').val()
  $(`.js-row-id[value=${rowId}]`).closest('td').find('.js-before-id').val(beforeVal)
  console.log(beforeVal)
  rowCount++;
}

$('#materialQuotationForm').on('click', '.js-add-cut-set', function() {
  const parentId = $(this).closest('tr').find('.js-row-id').val();
  $(`.js-parent-id[value="${parentId}"]`).closest('tr').removeClass('d-none');
})

$('#materialQuotationForm').on("keyup", ".js-cut-turf-vertical, .js-cut-turf-horizontal, #product-body .js-material-row .js-material-product-count", function () {
  var productId = $(this).closest(".js-material-row").find(".js-material-product-select").val();
  var selectProduct = window.allProducts.find(v => v.id == productId);

  var vertical = $(this).closest(".js-material-row").find(".js-cut-turf-vertical").val();
  var verticalLimit = selectProduct.vertical;
  if (vertical > verticalLimit) {
    alert(`縦幅は${verticalLimit}m以内に指定してください。`);
    $(this).closest(".js-material-row").find(".js-cut-turf-vertical").val("");
    return false;
  }

  var horizontal = $(this).closest(".js-material-row").find(".js-cut-turf-horizontal").val();
  var horizontalLimit = selectProduct.horizontal;
  if (horizontal > horizontalLimit) {
    alert(`横幅は${horizontalLimit}m以内に指定してください。`);
    $(this).closest(".js-material-row").find(".js-cut-turf-horizontal").val("");
    return false;
  }

  console.log(vertical,horizontal);
  console.log(vertical * horizontal);
  console.log(Math.round(vertical * horizontal * 100) / 100);
  var area = Math.round(vertical * horizontal * 100) / 100;
  console.log(area);
  area = Math.round(area * 10) / 10;
  console.log(area);
  $(this).closest(".js-material-row").find(".js-area").val(area);
  $(this).closest(".js-material-row").find(".js-area").attr("value", area);
  var cutUnitPrice = $(this).closest(".js-material-row").find(".js-cut-unit-price").val();
  $(this).closest(".js-material-row").find(".js-area-price").val(Math.round(area * cutUnitPrice));
  $(this).closest(".js-material-row").find(".js-area-price").attr("value", Math.round(area * cutUnitPrice));

  totalAreaCount();
  cutLengthCalc();
  // カット人工芝の大きさを変えると金額も変化させる
  setInterval(() => {
    calcCutSetPrice($(this));
    materialCutTotal();
    materialSubTotal();
  }, 1500);
});

$('#materialQuotationForm').on("change", ".js-cut-price-row", function () {
  // setInterval(() => {
    var parentId = $(this).closest(".js-cut-price-row").find(".js-parent-id").val();
    var num = $(this).closest(".js-cut-price-row").find(".js-cut-set-num").val();

    var cutTurfPrice = materialCutTurfPrice(parentId);
    var cutTotal = materialCutSetTotal(parentId)
    console.log(cutTurfPrice, cutTotal);

    $(this).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal));
    console.log('calc : ' + num * (cutTurfPrice + cutTotal));
    totalAreaCount();
    cutLengthCalc();
    // カット人工芝の大きさを変えると金額も変化させる
    calcCutSetPrice($(this))
    materialCutTotal();
    materialSubTotal();
  // }, 1000);
});

// セット数が変わったら合計金額も変える
$('#materialQuotationForm').on("keyup", ".js-cut-price-row", function () {
  var parentId = $(this).closest(".js-cut-price-row").find(".js-parent-id").val();
  var num = $(this).closest(".js-cut-price-row").find(".js-cut-set-num").val();
  var cutTurfPrice = materialCutTurfPrice(parentId);
  var cutTotal = materialCutSetTotal(parentId)
  $(this).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal));
  materialCutTotal();
  materialSubTotal();
});

// カット長の自動計算
function cutLengthCalc() {
  // 切り売りエリアの計算
  $(".js-material-row--child").each(function (i, childElement) {
    var length = 0.00;
    $("#cut-turf-table").find(".js-material-row--child").each(function (i, element) {
      var parentId = $(element).find(".js-parent-id").val();
      var horizontal = parseFloat($(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-cut-turf-horizontal").val());
      var vertical = parseFloat($(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-cut-turf-vertical").val());
      var productId = $(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-material-product-select").val();
      var selectProduct = window.allProducts.find(v => v.id == parseInt(productId));
      // 反物の横幅＝入力した横幅ならカット長分岐
      // 反物を横幅と購入希望が同じならカットする必要がないため
      // console.log(horizontal, vertical);
      if (selectProduct.vertical == vertical && selectProduct.horizontal == horizontal) {
        length = 0;
      } else if (selectProduct.vertical == vertical) {
        length += vertical;
      } else if (selectProduct.horizontal == horizontal) {
        length += horizontal;
      } else {
        length += horizontal + vertical;
      }
      // 角丸加工や、ゴルフ加工商品だった場合はカット面積挿入しない
      if ($(element).find(".js-unit").val() === "m" || $(element).find(".js-unit").val() === "") {
        length = Math.round(length * 1000) / 1000
        $(element).find(".js-material-product-count").val(Math.round(length * 10) / 10);
        $(element).find(".js-material-product-count").attr('value', Math.round(length * 10) / 10);
      }
      length = 0.00;
    });
  });
  $("#total-length").text(length + "m");
}
// 副資材バラ売り
function addCutSubRow(productId = null, num = null, unit = null, unitPrice = null, memo = '') {
  // 編集の際はdisplay:noneを削除
  if (productId) {
    $('#sub-heading').removeClass('d-none');
    $('#sub-heading-table').removeClass('d-none');
  }
  // 材料見積もりでは出さない商品id
  const excludeList = [64, 79];
  var productList = [];
  Object.keys(window.subItems).forEach(productKey => {
    if (window.allProducts[productKey].is_use_quotation == 1 && !excludeList.includes(window.allProducts[productKey].id)) {
      productList.push(
        `<option value="${window.subItems[productKey].id}" ${window.subItems[productKey].id === productId ? "selected" : ""}>${window.subItems[productKey].name}</option>`
      );
    }
  });
  var template = `
      <tr class="js-material-row dusk-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-grip-lines handle"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-sub-select js-material-product-select" name="product-select-${rowCount}" dusk='sub-cut-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w12'>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count form-control dusk-count-${rowCount}" value=${num}>
        </td>
        <td class='w10'>
          <input readonly type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value=${unit ? unit : ""}>
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value=${unitPrice ? unitPrice : ""}>
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#cut-sub-body").append(template);
  $("#cut-sub-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}
$('#materialQuotationForm').on("change", ".js-cut-sub-select, .js-sub-select", function () {
  var productId = $(this).val();
  var selectProduct = window.allProducts.find(v => v.id == productId);
  if ($(this).hasClass("js-sub-select")) {
    $(this).closest(".js-material-row").find(".js-unit").val(selectProduct.unit);
    $(this).closest(".js-material-row").find(".js-unit").attr("value", selectProduct.unit);
  } else if ($(this).hasClass("js-cut-sub-select")) {
    $(this).closest(".js-material-row").find(".js-unit").val(selectProduct.cut_unit);
    $(this).closest(".js-material-row").find(".js-unit").attr("value", selectProduct.cut_unit);
  }
  // まとめ売りとカットの値段が同じ（どっちもまとめ売り）の場合
  if (selectProduct.is_same_cut_price === 1) {
    $(this).closest(".js-material-row").find(".js-unit").val(selectProduct.unit);
    $(this).closest(".js-material-row").find(".js-unit").attr("value", selectProduct.fc_price);
  }
});

// カット系
function addCutRow() {
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    productList.push(
      `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
    );
  });
  var template = `
      <tr class='js-material-row' dusk="material-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}">
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-material-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class='w15'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="">
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control" value="0">
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="">
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#cuts-body").append(template);
  $("#cuts-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}
$('#materialQuotationForm').on("change", ".js-cut-select", function () {
  var productId = $(this).val();
  var selectProduct = window.allProducts.find(v => v.id == productId);
  $(this).closest(".js-material-row").find(".js-unit").val(selectProduct.unit);
  $(this).closest(".js-material-row").find(".js-unit").attr("value", selectProduct.unit);
  // カット人工芝の大きさを変えると金額も変化させる
  calcCutSetPrice($(this))
});

// その他自由記述
function addEtcRow(productName = "", count = null, unit = "", unitPrice = null, memo = '') {
  var template = `
      <tr class='js-material-row' dusk="material-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-grip-lines handle"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <input type="text" name="other_product${rowCount}" class="js-other-product-name form-control dusk-other-${rowCount}" value="${productName}" dusk='other-name'>
          </div>
        </td>
        <td class='w10'>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-material-product-count form-control dusk-count-${rowCount}" value="${roundFloatNumber(count, 3)}" dusk='other-count'>
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${unit}" dusk='other-unit'>
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="${unitPrice}" dusk='other-price'>
        </td>
        <td class='w15'>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control" value="0" readonly>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="${!memo ? '' : memo}">
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#etc-body").append(template);
  $("#etc-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 行の追加
$('#materialQuotationForm').on("click", "#js-turf-plus", function () {
  addTurfRow();
});
$('#materialQuotationForm').on("click", "#js-cut-turf-plus", function () {
  addCutTurfRow();
});
$('#materialQuotationForm').on("click", "#js-cut-sub-plus", function () {
  $(".js-cut-sub-area").prev().removeClass("d-none");
  $(".js-cut-sub-area").removeClass("d-none");
  addCutSubRow();
});
$('#materialQuotationForm').on("click", "#js-etc-plus", function () {
  $(".js-etc-area").prev().removeClass("d-none");
  $(".js-etc-area").removeClass("d-none");
  addEtcRow("", null, "", null);
});
$('#materialQuotationForm').on("click", "#js-minus-plus", function () {
  $(".js-minus-area").prev().removeClass("d-none");
  $(".js-minus-area").removeClass("d-none");
});
// カット賃の場合は備考欄欠かせない
$('#materialQuotationForm').on("change", ".js-cut-select", function () {
  if($(this).val() == 71){
    $(this).closest('tr').find('.js-pqmemo').addClass('d-none').addClass('js-pqmemo-h').removeClass('js-pqmemo');
  }else{
    $(this).closest('tr').find('.js-pqmemo-h').removeClass('d-none').addClass('js-pqmemo').removeClass('js-pqmemo-h');
  }
});

$('#materialQuotationForm').on("click", "#js-cut-plus", function () {
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    productList.push(
      `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
    );
  });
  var template = `
      <tr class='js-material-row js-material-row--child' dusk="material-row-${rowCount}">
        <td class=''>
          <button type='button' id='js-cut-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>カット追加</button>
        </td>
        <td class=''>
          <i class="fas fa-grip-lines handle"></i>
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-material-product-select" name="product-select-${rowCount}">
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="number${rowCount}" class="js-material-product-count js-material-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="unit-price" class="js-material-unit-price form-control dusk-unit-price-${rowCount}" value="">
        </td>
        <td class=''>
          <input type="number" onwheel="return false;" name="price" class="js-material-price form-control" value="0" readonly>
        </td>
        <td class='pointer'>
          <i class="fas fa-times fa-2x js-material-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $(this).closest(".js-material-row").after(template);
});

// submit時の関数
$('#materialQuotationForm').on("click", "#post-material-quotation", function (event) {
  event.preventDefault();
  materialCutTotal();
  materialSubTotal();
  if ($(".culc-wrapper").find("option:selected").val() == "") {
    alert("セレクトボックスが全て選択されていません");
    return false;
  }
  var error = false;
  var inputError = false;
  var selectError = false;

  //　バリデーション
  $("#materialQuotationForm").find("input:not(:hidden, #material-discount, .js-pqmemo, input[name='q[staff_name]'])").each(function (i, element) {
    if ($(element).val() == "") {
      console.log($(element));
      error = true;
      inputError = true
    }
  });
  if (error) {
    if (inputError) {
      alert('入力されていない項目があります')
      return false;
    }
    if (selectError) {
      alert("商品が選択されていない行があります");
      return false;
    }
    return false;
  }
  $.when(
    $("#materialQuotationForm").find(".js-material-row").each(function (i, element) {
      $(element).find("select").each(function (i, element) {
        if ($(element).find("option:selected").val() == "" || $(element).find("option:selected").text() == "商品を選択してください") {
          error = true;
          selectError = true
        }
      });
      $(element).find(".js-row-id").attr("name", `pqm[${i}][row_id]`);
      $(element).find(".js-material-product-select").attr("name", `pqm[${i}][product_id]`);
      $(element).find(".js-product-name").attr("name", `pqm[${i}][name]`);
      $(element).find(".js-material-product-count").attr("name", `pqm[${i}][num]`);
      $(element).find(".js-material-unit-price").attr("name", `pqm[${i}][unit_price]`);
      $(element).find(".js-pqmemo").attr("name", `pqm[${i}][memo]`);
      $(element).find(".js-unit").attr("name", `pqm[${i}][unit]`);
      $(element).find(".js-is-cut").attr("name", `pqm[${i}][is_cut]`);
      $(element).find(".js-vertical").attr("name", `pqm[${i}][vertical]`);
      $(element).find(".js-horizontal").attr("name", `pqm[${i}][horizontal]`);
      $(element).find(".js-cut-set-num").attr("name", `pqm[${i}][set_num]`);
      // $(element).find(".js-material-price").attr("name", `pqm[${i}][price]`);
      $(element).find(".js-area").attr("name", `pqm[${i}][num]`);
      $(element).find(".js-other-product-name").attr("name", `pqm[${i}][other_product_name]`);
      // カットメニューとカット対象の人工芝を紐づけるために使用
      $(element).find(".js-parent-id").attr("name", `pqm[${i}][parent_id]`);
      $(element).find(".js-pqmemo").attr("memo", `pqm[${i}][parent_id]`);
      if ($(element).find(".js-is-cut").val() == 1) {
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][parent_id]" value="${$(element).find('.js-parent-id').val()}">`)
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][product_id]" value="${$(element).find('.js-material-product-select').val()}">`)
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][num]" value="${$(element).find('.js-cut-length').val()}">`)
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][unit]" value="${$(element).find('.js-unit').val()}">`)
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][unit_price]" value="${$(element).find('.js-material-unit-price').val()}">`)
        $('#materialQuotationForm').append(`<input type='hidden' name="cut[${i}][memo]" value="${$(element).find('.js-pqmemo').val()}">`)
      }
    })//each
  ).done(function(){ 
    console.log('done');     
    if (document.URL.match(/edit/) === null) {
      if (!confirm('この内容で見積書を作成します。よろしいですか？')) {
        /* キャンセルの時の処理 */
        return false;
      } else {
        // 見積もり作成の時のみ続けて作るか聞く
        if (!document.URL.match(/edit/)) {
          if (confirm('続けて同じ案件に見積もりを作りますか？')) {
            $('.js-add').val(1)
            if (confirm('見積もり内容をコピーして作りますか？')) {
              $('.js-add-copy').val(1);
            }
          }
        }
      }
    } // if(document.URL.match(/edit/)
    if (window.user.role === 1) {
      $('body').append('<aside class="loader"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i><p class="loader__message">PDFを書き出しています。10秒ほどお待ちください</p></aside>')
    }else{
      appearLoader('金額を計算しています・・・', 5000);
    }
    $("#materialQuotationForm").submit();
  });
});

$('#materialQuotationForm').on("click", ".js-material-remove-row", function () {
  $(this).parent().parent().fadeOut(700).queue(function () {
    // queueの中なので、$(this)はtrタグを指す
    var cnt = 0;
    var rowId = parseInt($(this).find('.js-row-id').val());
    var parentId = 0;
    // カット人工芝の行を削除したら子要素を全削除
    if( !$(this).hasClass('js-material-row--child') ){
      $(this).closest('tbody').find(`.js-parent-id[value=${rowId}]`).closest('tr').remove();
      $(this).closest('tbody').find(`.js-row-id[value=${rowId}]`).closest('tr').remove();
    };

    if($(this).hasClass('js-material-row--child')){
      parentId = $(this).find('.js-parent-id').val();
      $(".js-material-row--child").each(function (i, element) {
        if ($(element).find(".js-parent-id").val() == parentId) {
          cnt++;
        }
      });
      console.log('cnt : ' + cnt);
      if (cnt === 1){
        $(this).closest('tbody').find(`.js-row-id[value=${parentId}]`).closest('tr').remove();
        $(this).closest('tbody').find(`.js-row-id[value=${parentId}]`).closest('tr').find('.js-before-id').val('0');
        $(this).closest('tbody').find('.js-cut-price-row').find(`.js-parent-id[value=${parentId}]`).closest('tr').remove();
      }
      console.log('parentId : ' + parentId);
    }
    //ToDo: 新規作成のときは上が実行されず、ここだけは実行されている
    this.remove();
    calcCutSetPrice($(this), parentId);
    materialCutTotal();
    const result = materialSubTotal();
    console.log('1047: ' + result);
  });
});

// バリデーションや修正ボタン押した時の復帰用にフォーム保持
$('#materialQuotationForm').on("change", "input", function () {
  var value = $(this).val();
  $(this).attr("value", value);
});
$('#materialQuotationForm').on("change", "select", function () {
  var value = $(this).val();
  $(this).children(`option[value="${value}"]`).attr("selected", true);
});

// 大口割引の計算
function calcDiscountRate(area) {
  switch (true) {
    case area > 1499:
      return 0.15
    case area > 999:
      return 0.1
    case area > 499:
      return 0.05
    default:
      return 0
  }
}

/* 切り売り各セットでの人工芝金額 */
/* 指定のparent-idの人工芝の金額 */
function materialCutTurfPrice(parentId) {
  var val = parseInt($('.js-material-row').find(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-material-price").val());
  return val;
}

/* 切り売り各セットでのカット料金の合計 */
/* 指定のparent-idの人工芝のカットの合計 */
function materialCutSetTotal(parentId) {
  var total = 0;
  var val = 0;
  $('.js-material-cut-row').each(function (index, element) {
    elem = parseInt($(element).find(`.js-parent-id[value=${parentId}]`).closest('tr').find(".js-material-price").val());
    val = isNaN(elem) ? 0 : elem;
    total = total + val;
  });

  return total;
}

function materialCutTotal() {
  // 行の金額算出
  var total = 0
  $('.js-material-cut-row').each(function (index, element) {
    var count = $(element).find('.js-material-product-count').val();
    var unitPrice = $(element).find('.js-material-unit-price').val();
    total += parseInt($(element).find('.js-material-price').val());
  });
  $('#material-total-cut-price').val(total);
}

// 編集の時は再計算
function materialRowTotal() {
  // 行の金額算出
  $('.js-material-row').each(function (index, element) {
    var count = $(element).find('.js-material-product-count').val();
    var unitPrice = $(element).find('.js-material-unit-price').val();
    $(element).find('.js-material-price').val(Math.round(count * unitPrice));

    //指定枚数の総計を入力
    var parentId = $(element).closest(".js-cut-price-row").find(".js-parent-id").val();
    var num = $(element).closest(".js-cut-price-row").find(".js-cut-set-num").val();
    var cutTurfPrice = materialCutTurfPrice(parentId);
    var cutTotal = materialCutSetTotal(parentId)
    $(element).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal));

  });

}
// 小計・合計計算・の設定
function materialSubTotal() {
  var discount = !isNaN(parseInt($('#discount,#material-discount').val())) ? parseInt($('#discount,#material-discount').val()) : 0;
  $(".js-material-row--child").each(function (i, element) {
    var count = $(element).find(".js-material-product-count").val()
    var unitPrice = $(element).find(".js-material-unit-price").val()
    $(element).find(".js-material-price").val(Math.round(count * unitPrice));
  });
  var subtotal = 0;
  // 表示用の小計変数
  $(".js-material-price:not(#material-discount").not("#material-total-cut-price").not('.js-cut-turf-price').each(function (i, element) {
    var rowPrice = parseInt($(element).val())
    rowPrice = !isNaN(rowPrice) ? rowPrice : 0
    subtotal = subtotal + rowPrice;
    subtotal = quotationTax(subtotal);
  });
  //小計用の切り売りの合計金額
  $(".js-cut-price-row").each(function (i, element) {
    var rowPrice = parseInt($(element).find('.js-cut-set-price').val())
    rowPrice = !isNaN(rowPrice) ? rowPrice : 0
    subtotal = quotationTax(subtotal + rowPrice);
  });

  // return setTimeout(() => {
    subtotal = subtotal - discount;
    $('input[name="q[sub_total]"]').val(subtotal);
    // $('#materialQuotationForm').find("#material-sub-total").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(showSubtotal));
    $('#materialQuotationForm').find("#material-sub-total").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(subtotal));
    // $("#subTotal").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(showSubtotal));
    $("#subTotal").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(subtotal));
    // var tax = Math.round(showSubtotal * 0.1);
    var tax = quotationTax(subtotal , 0.1);
    $('#materialQuotationForm').find("#material-tax").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(tax));
    $("#tax").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(tax));
    // var total = Math.round(showSubtotal * 1.1);
    var total = quotationTax(subtotal , 1.1);
    $('#materialQuotationForm').find("#material-total").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(total));
    $("#total").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(total));
    $('input[name="q[total]"]').val(quotationTax(subtotal ,1.1));
    materialCutTotal();
    return true;
  // }, 2500);
}

// 切り上げ切り捨て関数（小数点以下）

function quotationTax(total,tax = 1){
  let quotation_tax_option = window.quotationTaxOption.quotation_tax_option;
  var result = 0;
  switch (quotation_tax_option) {
    // 四捨五入
    case 0:
      result = Math.round(total * tax);
      break;
    // 切り上げ
    case 1:
      result = Math.ceil(total * tax);
      break;
    // 切り捨て
    case 2:
      result = Math.floor(total * tax);
      break;
    default:
      console.log('エラー！！！quotation_tax_option'+quotation_tax_option+'：total'+total +':result'+result);
      result = error;
  }
  return result;
}

function totalAreaCount() {
  var totalArea = 0;
  window.discount = 0
  // 反物の面積計算
  $("#product-body .js-material-row:not(.js-material-row--child)").each(function (index, element) {
    var productId = $(element).find(".js-turf-select").val();
    var product = window.allProducts.find(v => v.id == productId);
    // 選択されていないと0になり、0になると続きの処理が止まるので分岐
    if (productId !== '0') {
      var num = parseInt($(element).find(".js-material-product-count").val());
      var target = window.allProducts.find(v => v.id == productId);
      var turfArea = parseInt(target.vertical) * parseInt(target.horizontal) * num;
      totalArea += turfArea;
    }
    const discountRate = calcDiscountRate(totalArea)
    window.discount += parseInt($(element).find('.js-material-price').val()) * discountRate
  });
  $('#discount').val(window.discount)

  // 切り売りの面積計算
  $("#cut-turf-table .js-material-row:not(.js-material-row--child)").each(function (index, element) {
    // 空欄だとNaNになるので、分岐
    if (Number.isInteger($(element).find(".js-area").val())) {
      totalArea += parseInt($(element).find(".js-area").val());
    }
    // console.log('totalArea : ' + totalArea);
  });


  window.totalArea = Number.isInteger(totalArea) ? parseInt(totalArea) : window.totalArea;
}

function getParam(name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//セレクトボックスが切り替わったら発動
$('#quotationForm').on('change', '.js-account-infomation', function () {
  var payee = $(this).val();
  $('.js-payee').val(payee);
});

function calcCutSetPrice(_this, parentId=null){
  // _thisはjQueryのthisオブジェクト（$(this）)
  // 人工芝行とカット業でparentIdのとり方が変わる
  if( parentId == null){
    if($(_this).closest('tr').hasClass('js-material-cut-row')){
      parentId = $(_this).closest(".js-material-row").find(".js-parent-id").val();
    }else{
      parentId = $(_this).closest(".js-material-row").find(".js-row-id").val();
    }
  }
  var setRow = $(`.js-parent-id[value="${parentId}"]`);
  var num = setRow.closest(".js-cut-price-row").find(".js-cut-set-num").val();
  var cutTurfPrice = materialCutTurfPrice(parentId);
  var cutTotal = materialCutSetTotal(parentId)
  // parentIdに紐づく合計金額行を探してデータinput
  $(`.js-parent-id[value="${parentId}"]`).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal));
}

$('#materialQuotationForm').on('keyup', '.js-pqmemo', function(){
  const content = $(this).val();
  console.log(content);
  if(content.length > 14){
    $(".alert-msg").text("備考欄は15文字まででご入力ください");
    $("#alert-warning").fadeIn(600).delay(1400).fadeOut(4000);
  }
});