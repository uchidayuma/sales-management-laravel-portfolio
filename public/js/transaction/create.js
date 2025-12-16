// ブラウザバックや修正、途中落ちから復帰した場合
var rowCount = 1;
var referrer = document.referrer;
referrer = referrer.split("/");
referrer = referrer.pop();

$(document).ready(function () {
  // adminならいつでもsubmit押せる
  if (window.user.role != 1) {
    $('#post-transaction').prop('disabled', true).addClass('disabled btn-secondary');
  }
  totalAreaCount();
  $('[data-toggle="tooltip"]').tooltip()
  var title = [];
  $(".js-sales-select option").each(function () {
    title.push($(this).attr("title"));
  });

  if (localStorage.getItem("transaction") && referrer === "comfirm") {
    var dom = localStorage.getItem("transaction");
    $(".culc-wrapper").empty();
    $(".culc-wrapper").html(dom);
    // 確認画面から戻ってきた場合は割引額をreadonlyに入力
    $('#discount').val(getParam('discount'))
    const receiveType = getParam('address_type');
    // 工場受け取りなら、readonlyと納品希望日を調整
    if(receiveType == 3){
      $('textarea[name="t[address]"]').prop('readonly', true);
      window.isFactoryPickUp = true;
    }
    // 再計算
    subTotal();
    checkTransPortState();
    localStorage.removeItem('transaction');
  } else {
    // 発注書編集なら
    $(`#prepaid${window.prepaid}`).click();
    if (window.editFlg) {
      $('article').append('<aside class="loader"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i><p class="loader__message">金額を計算中...</p></aside>')
      // 工場引取かどうかを判定
      if($('input[name="t[address_type]"]:checked').val() === '3'){
        window.isFactoryPickUp = true;
      }
      // =========== 編集の場合は商品を展開 ==============
      window.transactions.forEach(function (val, index) {
        // 反物人工芝
        if (val.product_id !== null && val.unit === '反' && val.vertical === null && val.horizontal === null) {
          addTurfRow(val.product_id, val.num, val.pt_unit_price ? val.pt_unit_price : val.fc_price);
          if (val.turf_cuts !== null) {
            var parentId = rowCount - 1
            val.turf_cuts.forEach(function (cut) {
              addFullCutMenuRow(cut.product_id, cut.num, parentId)
            })
          }
        }
        // 切り売り人工芝
        if (val.product_id !== null && val.vertical !== null && val.horizontal !== null) {
          console.log(val);
          addCutTurfRow(val.product_id, val.horizontal, val.vertical, val.num, val.fc_cut_price, val.product_name);
          // parentIdはrowCount-1で表現できる
          const parentId = rowCount - 1
          if (val.turf_cuts !== null) {
            val.turf_cuts.forEach(function (cut) {
              // parentIdはrowCount-1で表現できる
              addCutMenuRow(cut.product_id, cut.num, parentId, val.product_name, cut.unit_price)
            })
          }
          addCutSetNumRow(parentId, val.cut_set_num != null ? val.cut_set_num : 1)
        }
        // 副資材
        if (val.product_id !== null && val.unit !== '反' && val.cut === 0 && val.product_type_id === 2) {
          addSubRow(val.product_id, val.num, val.unit, val.unit_price)
          $('.js-sub-area').prev('h4').removeClass('d-none');
          $('.js-sub-area').removeClass('d-none');
        }
        // 副資材バラ売り
        if (val.product_id !== null && val.unit !== '反' && val.cut === 1 && val.product_type_id === 2) {
          addCutSubRow(val.product_id, val.num, val.unit, val.unit_price)
          $('.js-cut-sub-area').prev('h4').removeClass('d-none');
          $('.js-cut-sub-area').removeClass('d-none');
        }
        // 販促商品
        if (val.product_id !== null && val.unit !== '反' && val.product_type_id === 3 ) {
          addSalesRow(val.product_id, val.num, val.unit, val.unit_price)
          $('.js-sales-area').prev('h4').removeClass('d-none');
          $('.js-sales-area').removeClass('d-none');
        }
        // その他自由記述商品
        if (val.product_id === null) {
          $('.js-etc-area').prev('h4').removeClass('d-none');
          $('.js-etc-area').removeClass('d-none');
          addEtcRow(val.other_product_name, val.num, val.unit, val.other_product_price);
        }
        // addEditRow(val);
        rowCount++;
      });
      // =========== 編集の場合は商品を展開ここまで ==============
      setTimeout(() => {
        if($('input[name="t[address_type]"]:checked').val() === '5'){
          $('textarea[name="t[address]"]').prop('readonly', false);
        }
        totalAreaCount();
        $('.loader').remove();
        // 本部が編集する時に必要なので消さない！
        if (window.user.role === 1) {
          if (!$('.js-minus-area')[0]) {
            $(".culc-wrapper").append(
              `<section class='d-flex align-items-center'><h4 class='h4 js-minus-area mb0 mr-1'>特別割引</h4><div class='d-flex align-items-center mr-5'><input class="form-control js-price mr10" id='special-discount' name="t[special_discount]" value="${window.spDiscount}" placeholder="1000"><p class='f12'>円</p></div>
              <h4 class='h4 js-minus-area mb0 mr-1'>大口割引</h4><div class='d-flex align-items-center'><input class="form-control js-price mr5" id='discount' name="t[discount]" value="${window.dbDiscount}" placeholder="1000"><p class='f12'>円</p></div></section>`
            );
          }
        }
         // 編集時かつ、2枚以上なら入力欄を表示
        $('.js-cut-price-row').each(function (i, element) {
          const setCount = parseInt($(element).closest('tr').find('.cut-set-count').val()); 
          if( setCount > 1){
            $(element).removeClass('d-none');
          }
        })
        if( window.transactions[0].delivery_at2){
          const secondDate = dayjs($('input[name="t[delivery_at]"]').val()).add(1, 'day').format('YYYY-MM-DD');
          $(".datepicker2").flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            locale: "ja",
            allowInput: window.appEnv === 'local' || window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
            defaultDate: window.transactions[0].delivery_at2,
            minDate: window.user.role === 2 ?  secondDate : null
          });
          $('.datepicker2').removeClass('d-none');
          $('.datepicker2').prev().removeClass('d-none');
        }
        if( window.transactions[0].delivery_at3){
          $(".datepicker3").flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            locale: "ja",
            allowInput: window.appEnv === 'local' || window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
            defaultDate: window.transactions[0].delivery_at3,
            minDate: window.user.role === 2 ? dayjs(window.transactions[0].delivery_at2).add(1, 'day').format('YYYY-MM-DD') : null
          });
          $('.datepicker3').removeClass('d-none');
          $('.datepicker3').prev().removeClass('d-none');
        }
        subTotal();
      }, 4000);
    // 編集時のifここまで
    } else {
      addTurfRow();
      addCutTurfRow();
      addSubRow();
      subTotal();
      setDatePicker(4, true);
    }
  }
  // enterでsubmitを無効
  $("input").keydown(function (e) {
    if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
      return false;
    } else {
      return true;
    }
  });
});

// 人工芝反物の行追加メソッド
function addTurfRow(productId = null, num = null, unitPrice = null) {
  console.log('addTurfRow');
  var productList = [];
  Object.keys(window.turfs).forEach(productKey => {
    if (window.turfs[productKey].price != 0) {
      productList.push(
        `<option value=${window.turfs[productKey].id} ${window.turfs[productKey].id === productId ? 'selected' : ''} is40mm=${window.turfs[productKey].is40mm}>${window.turfs[productKey].name}（${window.turfs[productKey].horizontal}m × ${window.turfs[productKey].vertical}m）</option>`
      );
    }
  });
  // ==== TODO 本部かどうかでreadonly分岐する ===== 
  var template = `
      <tr class="js-row js-turf-row js-turf-row-${rowCount}">
        <td class='w10'>
          <i class="fas fa-plus-square js-add-turf-cut-menu pointer">カット<br>追加</i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-turf-select js-product-select" name="product-select-${rowCount}" dusk='turf-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w10'>
          <input type="number" name="number${rowCount}" class="js-product-count form-control" value="${num}" dusk='turf-number${rowCount}'>
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="反" readonly>
        </td>
        <td class='w12'>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="${unitPrice}" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w12'>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='w5 pointer align-middle js-remove-td js-remove-row'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  if(!$('#product-body').length){
    $('#js-turf-plus').prev().append('<tbody id="product-body"></tbody>')
  }
  $("#product-body").append(template);
  $("#product-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 反物カットメニューの追加
$(document).on("click", ".js-add-turf-cut-menu", function () {
  var productList = [];
  var products = window.products.filter(function (value) {
    return value.product_type_id === 5;
  });
  productList.push(`<option value=''>カットメニューを選択</option>`)
  products.forEach(product => {
    productList.push(
      `<option value=${product.id} fc-price=${product.fc_price}>${product.name}</option>`
    );
  });
  var template = `
      <tr class="js-row js-row--child">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value=""/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
            ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" name="number${rowCount}" class="js-product-count js-fabric-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="反分" readonly>
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td
      </tr>
    `;
  $(this).closest(".js-row").after(template);
  // カットする対象を紐付け
  var rowId = $(this).closest(".js-row").find(".js-row-id").val();
  $(this).closest(".js-row").next().find(".js-parent-id").val(rowId);
  // 行の金額算出
  var count = $(this).closest(".js-row").find(".js-product-count").val();
  var unitPrice = $(this).closest(".js-row").find(".js-unit-price").val();
  $(this).closest(".js-row").find(".js-price").val(Math.round(count * unitPrice * 10) / 10);
  subTotal();
  rowCount++;
});
// 人工芝反物カットメニューの行追加メソッド
function addFullCutMenuRow(productId = null, num = null, parentId = null) {
  console.log('addFullCutMenuRow');
  var productList = [];
  var products = window.products.filter(function (value) {
    return value.product_type_id === 5;
  });
  productList.push(`<option value=''>カットメニューを選択</option>`)
  const selectProduct = window.products.find(v => v.id == productId );
  products.forEach(product => {
    productList.push(
      `<option value=${product.id} fc-price=${product.price} ${product.id === parseInt(productId) ? "selected" : ""}>${product.name}</option>`
    );
  });
  var template = `
      <tr class="js-row js-row--child dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' value="${parentId}"/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
            ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" name="number${rowCount}" value="${num}" class="js-product-count js-fabric-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="反分" readonly>
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="${selectProduct.price}"  ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $('.js-row-id').attr('value', parentId).closest(`.js-turf-row-${parentId}`).after(template)
  // $("#turf-table").append(template)
  $("#turf-table").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 行の金額算出
$(document).on("keyup", ".js-product-count, .js-unit-price", function () {
  var count = parseFloat($(this).closest(".js-row").find(".js-product-count").val());
  var unitPrice = $(this).closest(".js-row").find(".js-unit-price").val();

  $(this).closest(".js-row").find(".js-price").val(Math.round(count * unitPrice));
  $(this).closest(".js-row").find(".js-price").attr("value", Math.round(count * unitPrice));
  subTotal();
});

// 割引額が変更されたら
$(document).on("keyup", "#discount, #special-discount", function () {
  console.log('割引額変更');
  subTotal();
})

$(document).on("change", ".js-turf-select, .js-cut-turf-select, .js-sub-select, .js-cut-sub-select, .js-sales-select, .js-cut-select, .js-other-product-name, #receive-check, #prepaid0, #prepaid1, #prepaid2", function () {
  $(this).find('option').removeAttr('selected');
  var productId = $(this).val();
  var target = window.products.find(v => v.id == productId);
  // 単価入力を反物か切り売りかで分岐
  if ($(this).hasClass("js-turf-select")) {
    $(this).closest(".js-row").find(".js-unit-price").val(target.fc_price);
    $(this).closest(".js-row").find(".js-unit-price").attr("value", target.fc_price);
  } else if ($(this).hasClass("js-cut-turf-select")) {
    $(this).closest(".js-row").find(".js-cut-unit-price").val(target.cut_fc_price);
    $(this).closest(".js-row").find(".js-cut-unit-price").attr("value", target.cut_fc_price);
    //裏貼り加工を選択した際のアラート
    if ((productId == 65 || productId == 66) && window.alertState === true) {
      window.alertState = false;
      $(".alert-msg").text("本部に納期確認を行ってから発注してください");
      $("#alert-danger").fadeIn(600).delay(1400).fadeOut(3000);
      console.log(window.alertState);
      setTimeout(() => {
        window.alertState = true;
      }, 10000);
    }
  } else if ($(this).hasClass("js-sub-select")) {
    $(this).closest(".js-row").find(".js-unit-price").val(target.fc_price);
    $(this).closest(".js-row").find(".js-unit-price").attr("value", target.fc_price);
  } else if ($(this).hasClass("js-cut-sub-select")) {
    $(this).closest(".js-row").find(".js-unit-price").val(target.cut_fc_price);
    $(this).closest(".js-row").find(".js-unit-price").attr("value", target.cut_fc_price);
  } else if ($(this).hasClass("js-sales-select")) {
    $(this).closest(".js-row").find(".js-unit-price").val(target.fc_price);
    $(this).closest(".js-row").find(".js-unit-price").attr("value", target.fc_price);
  } else if ($(this).hasClass("js-cut-select")) {
    $(this).closest(".js-row").find(".js-unit-price").val(target.fc_price);
    $(this).closest(".js-row").find(".js-unit-price").attr("value", target.fc_price);
  }
  // まとめ売りとカットの値段が同じ（どっちもまとめ売り）の場合
  if (typeof target !== "undefined" && $(this).attr('id') !== 'receive-check') {
    if (target.is_same_cut_price === 1) {
      $(this).closest(".js-row").find(".js-unit-price").val(target.fc_price);
      $(this).closest(".js-row").find(".js-unit-price").attr("value", target.fc_price);
    }
  }

  // 行の金額算出
  var count = parseFloat($(this).closest(".js-row").find(".js-product-count").val());
  var unitPrice = parseInt($(this).closest(".js-row").find(".js-unit-price").val());
  var rowPrice = Math.round(count * unitPrice * 10) / 10;
  console.log(rowPrice);
  $(this).closest(".js-row").find(".js-price").val(rowPrice);
  $(this).closest(".js-row").find(".js-price").attr('value', rowPrice);

  calcCutSetPrice($(this))
  totalAreaCount();
  subTotal();
});

// 人工芝切り売りの行追加メソッド
function addCutTurfRow(productId = null, horizontal = null, vertical = null, num = 0, unitPrice = null, productName = null) {
  var productList = [];
  Object.keys(window.turfs).forEach(productKey => {
    // SB30は切り売り販売しない サンプル芝COOL40mmも出さない SB20CP1も出さない
    if(window.turfs[productKey].id != 7 && window.turfs[productKey].id != 2 && window.turfs[productKey].id != 83){
      productList.push(
        `<option value=${window.turfs[productKey].id} fc-price="${window.turfs[productKey].price}" ${window.turfs[productKey].id === parseInt(productId) ? 'selected' : ''} is40mm=${window.turfs[productKey].is40mm}>${window.turfs[productKey].name}</option>`
      );
    }
  });
  const selectProduct = window.products.find(v => v.id == productId );
  var template = `
      <tr class="js-row js-cut-turf-row dusk-row-${rowCount}">
        <td class='w10 pl0 pr0'>
          <i class="fas fa-cut js-add-cut-menu pointer mb15" dusk="add-cut-turf"> カット追加</i>
          <i class="fas fa-plus-square js-add-cut-set pointer text-success bold"> 複数枚注文</i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='js-before-id' value="0" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-turf-select js-product-select" name="product-select-${rowCount}" dusk='cut-turf-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w10'>
          <input type="number" name="horizontal" class="js-cut-turf-horizontal js-horizontal form-control" dusk="horizontal" id="horizontal-${rowCount}" value="${roundFloatNumber(horizontal, 3)}">
        </td>
        <td class='w10'>
          <input type="number" step="0.1" name="vertical" class="js-cut-turf-vertical js-vertical form-control" dusk="vertical" id="vertical-${rowCount}" value="${roundFloatNumber(vertical, 3)}">
        </td>
        <td class='w10'>
          <input type="text" step="0.1" name="unit" class="js-area js-product-count js-cut-turf-price form-control dusk-unit-${rowCount}" value="${num}" readonly>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" class="js-unit-price js-cut-unit-price form-control dusk-unit-price-${rowCount}" value="${ !selectProduct ? null : selectProduct.cut_fc_price}" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-area-price js-price js-cut-turf-price form-control" value="${num!=0 ? num*unitPrice : 0}" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#product-cut-body").append(template);
  $("#product-cut-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 切り売り人工芝を選択した際にカットメニューを追加する
$(document).on("change", ".js-cut-turf-select", function () {
  var beforeId = $(this).closest(".js-row").find(".js-before-id").val();
  // カットメニューを追加するかの値 !=＝0ならカットメニューを追加しない
  if (beforeId !== '0') { return false; }
  // カットメニュー制限をconfigテーブルから持ってくる
  const cutTurfInvisibleIdsArr = window.cutTurfInvisibleIds.split(',').map(Number);
  var productList = [];
  // カットする商品名取得
  var productName = $(this).closest(".js-row").find('.js-cut-turf-select option:selected').text();
  Object.keys(window.cutItems).forEach(productKey => {
    // 【GOLF】の文字列が含まれる場合はカットメニューを追加できるように
      if (!cutTurfInvisibleIdsArr.includes(window.cutItems[productKey].id) ) {
        productList.push(
          `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
        );
        if (!isGolf(productName)) {
          $(this).closest(".js-row").find('.js-add-cut-menu').hide();
        } else {
          $(this).closest(".js-row").find('.js-add-cut-menu').show();
        }
      }
  });
  var cutSetNum = 1;
  var template = `
      <tr class='js-row js-row--child turf-cut--auto'>
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value=""/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" name="number${rowCount}" class="js-product-count js-cut-length form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price js-cut-turf-price form-control" value="0" readonly>
        </td>     
      </tr>
      <tr class="js-row js-cut-price-row d-none dusk-row-${++rowCount}">
        <td class='w5 pl0 pr0'>
        </td>
        <td class='w30 text-right'>
          枚数
        </td>
        <td class='w12'>
          <input type='hidden' class='js-parent-id' value="" /> 
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='cut-set-count' value="${cutSetNum}" />
          <input type="number" class="js-cut-set-num form-control" dusk="turf-set-num" value="${roundFloatNumber(cutSetNum, 3)}">
        </td>
        <td class='w12'>
        </td>
        <td class='w10 text-right'>
          合計
        </td>
        <td class='w30' colspan='2'>
          <input type="number" name="price" class="js-cut-set-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
        </td>
      </tr>
    `;
  $(this).closest(".js-row").after(template);
  //親と紐付けるためにrowIdを取得
  var rowId = $(this).closest(".js-row").find(".js-row-id").val();
  $(this).closest(".js-row").next().find(".js-parent-id").val(rowId);
  $(this).closest(".js-row").next().next().find(".js-parent-id").val(rowId);

  // 行の金額算出
  var count = parseFloat($(this).closest(".js-row").find(".js-product-count").val());
  var unitPrice = $(this).closest(".js-row").find(".js-unit-price").val();
  var rowPrice = Math.floor(count * unitPrice);
  $(this).closest(".js-row").find(".js-price").val(rowPrice);
  // カットメニューを追加するかの値＝0なら初期状態なので、カットメニューをappend()する
  $(this).closest(".js-row").find(".js-before-id").val($(this).val());
  subTotal();
  rowCount++;
});
// カットメニューを追加ボタン
$(document).on("click", ".js-add-cut-menu", function () {
  var productName = $(this).closest(".js-row").find('.js-cut-turf-select option:selected').text();
  productList = addGolfCutMenu(window.cutItems,productName,window.editFlg);
  var template = `
      <tr class='js-row js-row--child'>
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' name value=""/>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}" dusk='cut-menu-select-2'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" name="number${rowCount}" class="js-product-count js-cut-length form-control dusk-count-${rowCount}" dusk="cut-menu-num-2">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price js-cut-turf-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td
      </tr>
    `;

  $(this).closest(".js-row").next().after(template)
  // カットする対象を紐付け
  var rowId = $(this).closest(".js-row").find(".js-row-id").val();
  $(this).closest(".js-row").next().next().find(".js-parent-id").val(rowId);
  subTotal();
  rowCount++;
});

function addCutMenuRow(productId = null, num = null, parentId = null, productName = null, unitPrice = null) {
  console.log(productId);
  console.log(num);
  console.log(parentId);
  // カットメニューを追加するかの値 !=＝0ならカットメニューを追加しない
  // if ((document.URL.match('new') && window.iscopy == 0) || window.quotationtype == 0) { return false; }
  var productList = [];
  productList = addGolfCutMenu(window.cutItems,productName,window.editFlg,parseInt(productId), productId);
  const selectProduct = window.products.find(v => v.id == productId);
  console.log(selectProduct);
  var template = `
      <tr class="js-row js-row--child js-cut-row dusk-row-${rowCount}">
        <td class=''>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-parent-id' value="${parentId}">
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}" dusk='cut-menu-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td></td>
        <td class=''>
          <input type="number" name="number${rowCount}" class="js-product-count js-cut-length form-control dusk-count-${rowCount}" value=${num}>
        </td>
        <td class=''>
          <input type="text" readonly name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${selectProduct.unit}">
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value=${!unitPrice ? selectProduct.price : unitPrice} ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class=''>
          <input type="number" name="price" readonly class="js-price form-control" value="0">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#product-cut-body").append(template);
  $("#product-cut-body").sortable({
    handle: ".handle"
  });
  // カットする対象を紐付け
  // var rowId = $(this).closest(".js-row").find(".js-row-id").val();
  // $(this).closest(".js-row").next().find(".js-parent-id").val(rowId);

  rowCount++;
};

// 人工芝切り売りの枚数入力行追加メソッド
function addCutSetNumRow(rowId = null, cutSetNum = 1) {
  var template = `
      <tr class="js-row js-cut-price-row d-none dusk-row-${rowCount}">
        <td class='w5 pl0 pr0'>
        </td>
        <td class='w30 text-right'>
          枚数
        </td>
        <td class='w12'>
          <input type='hidden' class='js-parent-id' value="${rowId}" /> 
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='cut-set-count' value="${cutSetNum}" />
          <input type="number" class="js-cut-set-num form-control" dusk="turf-set-num" value="${roundFloatNumber(cutSetNum, 3)}">
        </td>
        <td class='w12'>
        </td>
        <td class='w10 text-right'>
          合計
        </td>
        <td class='w30' colspan='2'>
          <input type="number" name="price" class="js-cut-set-price form-control" value="0" readonly>
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
  rowCount++;
}

$(document).on('click', '.js-add-cut-set', function() {
  const parentId = $(this).closest('tr').find('.js-row-id').val();
  $(`.js-parent-id[value="${parentId}"]`).closest('tr').removeClass('d-none');
})

$(document).on("keyup", ".js-cut-turf-vertical, .js-cut-turf-horizontal, #product-body .js-row .js-product-count", function () {
  var productId = $(this).closest(".js-row").find(".js-product-select").val();
  var selectProduct = window.products.find(v => v.id == productId);
  // 反物のカットメニューならば計算しない
  if (selectProduct.product_type_id == 5) { return false }

  var vertical = $(this).closest(".js-row").find(".js-cut-turf-vertical").val();
  var verticalLimit = selectProduct.vertical;
  if (vertical > verticalLimit) {
    alert(`縦幅は${verticalLimit}m以内に指定してください。`);
    $(this).closest(".js-row").find(".js-cut-turf-vertical").val("");
    return false;
  }

  var horizontal = $(this).closest(".js-row").find(".js-cut-turf-horizontal").val();
  var horizontalLimit = selectProduct.horizontal;
  if (horizontal > horizontalLimit) {
    alert(`横幅は${horizontalLimit}m以内に指定してください。`);
    $(this).closest(".js-row").find(".js-cut-turf-horizontal").val("");
    return false;
  }

  var area = Math.round(vertical * horizontal * 100) / 100;
  console.log(area);
  area = Math.round(area * 10) / 10;
  console.log(area);
  $(this).closest(".js-row").find(".js-area").val(area);
  $(this).closest(".js-row").find(".js-area").attr("value", area);
  var cutUnitPrice = parseInt($(this).closest(".js-row").find(".js-cut-unit-price").val());
  // 切り売り面積は少数第2位を四捨五入
  var lastPrice = Math.round(area * cutUnitPrice * 10000) / 10000;
  $(this).closest(".js-row").find(".js-area-price").val(lastPrice);
  $(this).closest(".js-row").find(".js-area-price").attr("value", lastPrice);

  totalAreaCount();
  cutLengthCalc();
  // カット人工芝の大きさを変えると金額も変化させる
  calcCutSetPrice($(this))
  subTotal();
});

// セット数が変わったら合計金額も変える
$(document).on("keyup", ".js-cut-price-row, .js-cut-length", function () {
  var parentId = $(this).closest(".js-cut-price-row").find(".js-parent-id").val();
  var num = $(this).closest(".js-cut-price-row").find(".js-cut-set-num").val();
  var cutTurfPrice = calcCutTurfPrice(parentId);
  var cutTotal = cutSetTotal(parentId)
  $(this).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal));
  // カット人工芝の大きさを変えると金額も変化させる
  totalAreaCount();
  calcCutSetPrice($(this))
  subTotal();
});

// カット長の自動計算
function cutLengthCalc() {
  // 切り売りエリアの計算
  $(".js-row--child").each(function (i, childElement) {
    var length = 0.00;
    $("#cut-turf-table").find(".js-row--child").each(function (i, element) {
      var parentId = $(element).find(".js-parent-id").val();
      var horizontal = parseFloat($(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-cut-turf-horizontal").val());
      var vertical = parseFloat($(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-cut-turf-vertical").val());
      var productId = $(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-product-select").val();
      var selectProduct = window.products.find(v => v.id == parseInt(productId));
      // 反物の横幅＝入力した横幅ならカット長分岐
      // 反物を横幅と購入希望が同じならカットする必要がないため
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
        $(element).find(".js-product-count").val(Math.round(length * 10) / 10);
        $(element).find(".js-product-count").attr('value', Math.round(length * 10) / 10);
      }
      length = 0.00;
    });
  });
  $("#total-length").text(length + "m");
}

// 副資材まとめ売り
function addSubRow(productId = null, num = null) {
  var productList = [];
  Object.keys(window.subItems).forEach(productKey => {
    if(window.subItems[productKey].fc_price){
      productList.push(
        `<option value="${window.subItems[productKey].id}" ${window.subItems[productKey].id === parseInt(productId) ? "selected" : ""}>${window.subItems[productKey].name}</option>`
      );
    }
  });
  const selectProduct = window.products.find(v => v.id == productId);
  var template = `
      <tr class="js-row js-sub-row dusk-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-grip-lines handle"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='js-shipping-size' value="${selectProduct ? selectProduct.shipping_size : 0}" />
          <input type='hidden' class='js-shipping-weight' value="${selectProduct ? selectProduct.shipping_weight : 0}" />
          <input type='hidden' class='js-shipping-cut-weight' value="${selectProduct ? selectProduct.shipping_cut_weight : 0}" />
          <input type='hidden' class='js-shipping-include' value="${selectProduct ? selectProduct.shipping_include : 0}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-sub-select js-product-select" name="product-select-${rowCount}" dusk='sub-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w12'>
          <input type="number" name="number${rowCount}" class="js-product-count form-control" value="${num}" dusk="sub-number">
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${ !selectProduct ? '' : selectProduct.unit}" readonly>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" class="js-unit-price js-unit-price form-control dusk-unit-price-${rowCount}" value="${ !selectProduct ? '' : selectProduct.fc_price}" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-price form-control" value="0" step="1" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#sub-body").append(template);
  $("#sub-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

// 副資材バラ売り
function addCutSubRow(productId = null, num = null) {
  var productList = [];
  Object.keys(window.subItems).forEach(productKey => {
    // is_exclude_sub_cut = 1 は発注書の副資材のカット売りを除外する（まとめ売りのみ）
    if ((window.products[productKey].cut_price || window.products[productKey].is_same_cut_price === 1) && window.products[productKey].is_exclude_sub_cut === 0) {
      productList.push(
        `<option value="${window.subItems[productKey].id}" ${window.subItems[productKey].id === parseInt(productId) ? "selected" : ""}>${window.subItems[productKey].name}</option>`
      );
    }
  });
  const selectProduct = window.products.find(v => v.id == productId);
  var template = `
      <tr class="js-row js-cut-sub-row dusk-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-grip-lines handle"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='js-is-cut' value='1'/>
          <input type='hidden' class='js-shipping-size' value="${selectProduct ? selectProduct.shipping_size : 0}" />
          <input type='hidden' class='js-shipping-weight' value="${selectProduct ? selectProduct.shipping_weight : 0}" />
          <input type='hidden' class='js-shipping-cut-weight' value="${selectProduct ? selectProduct.shipping_cut_weight : 0}" />
          <input type='hidden' class='js-shipping-include' value="${selectProduct ? selectProduct.shipping_include : 0}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-sub-select js-product-select" name="product-select-${rowCount}" dusk='sub-cut-select'>
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w12'>
          <input type="number" name="number${rowCount}" value="${num}" class="js-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${ !selectProduct ? '' : selectProduct.cut_unit}" readonly>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="${ !selectProduct ? null : selectProduct.cut_fc_price}" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-price form-control" value="0" step="1" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#cut-sub-body").append(template);
  $("#cut-sub-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}

$(document).on("change", ".js-cut-sub-select, .js-sub-select", function () {
  var productId = $(this).val();
  var selectProduct = window.products.find(v => v.id == productId);
  if ($(this).hasClass("js-sub-select")) {
    $(this).closest(".js-row").find(".js-unit").val(selectProduct.unit);
    $(this).closest(".js-row").find(".js-unit").attr("value", selectProduct.unit);
    $(this).closest(".js-row").find(".js-shipping-size").val(selectProduct.shipping_size);
    $(this).closest(".js-row").find(".js-shipping-weight").val(selectProduct.shipping_weight);
    $(this).closest(".js-row").find(".js-shipping-include").val(selectProduct.shipping_include);
  } else if ($(this).hasClass("js-cut-sub-select")) {
    $(this).closest(".js-row").find(".js-unit").val(selectProduct.cut_unit);
    $(this).closest(".js-row").find(".js-unit").attr("value", selectProduct.cut_unit);
    $(this).closest(".js-row").find(".js-shipping-size").val(selectProduct.shipping_size);
    $(this).closest(".js-row").find(".js-shipping-cut-weight").val(selectProduct.shipping_cut_weight);
    $(this).closest(".js-row").find(".js-shipping-include").val(selectProduct.shipping_include);
  }
  // まとめ売りとカットの値段が同じ（どっちもまとめ売り）の場合
  if (selectProduct.is_same_cut_price === 1) {
    console.log(selectProduct);
    $(this).closest(".js-row").find(".js-unit").val(selectProduct.unit);
    $(this).closest(".js-row").find(".js-unit").attr("value", selectProduct.unit);
  }
  smallSizeCount();
});

// 販促物売り
function addSalesRow(productId=null, num=null) {
  var productList = [];
  Object.keys(window.salesItems).forEach(productKey => {
    productList.push(
      `<a value=${window.salesItems[productKey].id} class="dropdown-item js-dropdown-item pointer" image=${window.salesItems[productKey].image} dusk=${"product" + window.salesItems[productKey].id}>${window.salesItems[productKey].name}</a>`
    );
  });
  const selectProduct = window.products.find(v => v.id == productId);
  var template = `
      <tr class="js-row js-sales-row dusk-row-${rowCount}">
        <td class='w5 align-middle'>
          <i class="fas fa-grip-lines handle"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
          <input type='hidden' class='js-shipping-size' value="${selectProduct ? selectProduct.shipping_size : 0}" />
          <input type='hidden' class='js-shipping-weight' value="${selectProduct ? selectProduct.shipping_weight : 0}" />
          <input type='hidden' class='js-shipping-include' value="${selectProduct ? selectProduct.shipping_include : 0}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="dropdown">
            <button type="button" class="btn btn-lg btn-light dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" dusk='sales-select'>
              ${ productId !== null ? selectProduct.name : '商品を選択してください' }
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">${productList}</div>
          </div>
          <input type='hidden' class='js-product-select' id="product-select-${rowCount}" />
        </td>
        <td class='w10'>
          <input type="number" name="number${rowCount}" value="${num}" dusk="sales-number" class="js-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class='w10'>
          <input type="text" name="unit" value="${ !selectProduct ? '' : selectProduct.unit}" class="js-unit form-control dusk-unit-${rowCount}" readonly>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" value="${ !selectProduct ? null : selectProduct.fc_price}" class="js-unit-price form-control dusk-unit-price-${rowCount}"  ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#sales-body").append(template);
  $("#sales-body").sortable({
    handle: ".handle"
  });
  // デフォルトで商品ID選択する必要がある場合は↓で代入
  if(productId){
    $(`#product-select-${rowCount}`).val(productId);
  }
  rowCount++;
}

$(document).on("mouseenter", ".js-dropdown-item", function () {
  $("#sales-image").show().attr("src", $(this).attr("image"));
});
$(document).on("mouseleave", ".js-dropdown-item", function () {
  $("#sales-image").hide();
});

// 販促物選択時の計算など
$(document).on("click", ".dropdown-item", function () {
  $(this).closest(".js-row").find(".dropdown-toggle").text($(this).text());
  $("#sales-image").hide();
  $(this).closest(".js-row").find(".js-product-select").val($(this).attr("value")).trigger("change");
  var productId = $(this).attr("value");
  var selectProduct = window.products.find(v => v.id == productId);
  $(this).closest(".js-row").find(".js-unit").val(selectProduct.unit);
  $(this).closest(".js-row").find(".js-unit").attr('value', selectProduct.unit);
  $(this).closest(".js-row").find(".js-unit-price").val(selectProduct.fc_price);
  $(this).closest(".js-row").find(".js-unit-price").attr('value', selectProduct.fc_price);
  $(this).closest(".js-row").find(".js-shipping-size").val(selectProduct.shipping_size);
  $(this).closest(".js-row").find(".js-shipping-size").attr('value', selectProduct.shipping_size);
  $(this).closest(".js-row").find(".js-shipping-weight").val(selectProduct.shipping_weight);
  $(this).closest(".js-row").find(".js-shipping-weight").attr('value', selectProduct.shipping_weight);
  $(this).closest(".js-row").find(".js-shipping-include").val(selectProduct.shipping_include);
  $(this).closest(".js-row").find(".js-shipping-include").attr('value', selectProduct.shipping_include);
  subTotal()
  totalAreaCount();
});

// カット系
function addCutRow() {
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    if (window.cutItems[productKey].name != 'カット賃') {
      productList.push(
        `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
      );
    }
  });
  var template = `
      <tr class='js-row js-cut-row'>
        <td class='w5'>
          <i class="fas fa-cut"></i>
          <input type='hidden' class='js-row-id' value="${rowCount}" />
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}">
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class='w10'>
          <input type="number" name="number${rowCount}" class="js-product-count js-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class='w15'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class='w10'>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="" ${window.user.role === 1 ? '' : 'readonly'}>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $("#cuts-body").append(template);
  $("#cuts-body").sortable({
    handle: ".handle"
  });
  rowCount++;
}
$(document).on("change", ".js-cut-select", function () {
  var productId = $(this).val();
  var selectProduct = window.products.find(v => v.id == productId);
  $(this).closest(".js-row").find(".js-unit").val(selectProduct.unit);
  $(this).closest(".js-row").find(".js-unit").attr("value", selectProduct.unit);
  // カット人工芝の大きさを変えると金額も変化させる
  calcCutSetPrice($(this))
  subTotal();
});

// その他自由記述
function addEtcRow(productName = "", count = null, unit = "", unitPrice = null) {
  var template = `
      <tr class='js-row js-other-row'>
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
          <input type="number" name="number${rowCount}" value="${roundFloatNumber(count, 3)}" class="js-product-count js-product-count form-control dusk-count-${rowCount}" dusk='other-count'>
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="${unit}" dusk='other-unit'>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="${unitPrice}" dusk='other-price'>
        </td>
        <td class='w15'>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
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
$(document).on("click", "#js-turf-plus", function () {
  addTurfRow();
});
$(document).on("click", "#js-cut-turf-plus", function () {
  addCutTurfRow();
});
$(document).on("click", "#js-sub-plus", function () {
  $(".js-sub-area").prev().removeClass("d-none");
  $(".js-sub-area").removeClass("d-none");
  addSubRow();
});
$(document).on("click", "#js-cut-sub-plus", function () {
  $(".js-cut-sub-area").prev().removeClass("d-none");
  $(".js-cut-sub-area").removeClass("d-none");
  addCutSubRow();
});
$(document).on("click", "#js-sales-plus", function () {
  $(".js-sales-area").prev().removeClass("d-none");
  $(".js-sales-area").removeClass("d-none");
  addSalesRow();
});
$(document).on("click", "#js-etc-plus", function () {
  $(".js-etc-area").prev().removeClass("d-none");
  $(".js-etc-area").removeClass("d-none");
  addEtcRow();
});
$(document).on("click", "#js-minus-plus", function () {
  $(".js-minus-area").prev().removeClass("d-none");
  $(".js-minus-area").removeClass("d-none");
});

$(document).on("click", "#js-cut-plus", function () {
  var productList = [];
  Object.keys(window.cutItems).forEach(productKey => {
    if (window.cutItems[productKey].name != 'カット賃') {
      productList.push(
        `<option value=${window.cutItems[productKey].id}>${window.cutItems[productKey].name}</option>`
      );
    }
  });
  var template = `
      <tr class='js-row js-row--child'>
        <td class=''>
          <button type='button' id='js-cut-plus' class='btn btn-info mb30'><i class='fas fa-plus-circle mr10'></i>カット追加</button>
        </td>
        <td class=''>
          <i class="fas fa-grip-lines handle"></i>
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-cut-select js-product-select" name="product-select-${rowCount}">
              <option value="0">商品を選択してください</option>
              ${productList}
            </select>
          </div>
        </td>
        <td class=''>
          <input type="number" name="number${rowCount}" class="js-product-count js-product-count form-control dusk-count-${rowCount}">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" value="" readonly>
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price form-control" value="0" readonly>
        </td>
        <td class='pointer'>
          <i class="fas fa-times fa-2x js-remove-row pointer"></i>
        </td>
      </tr>
    `;
  $(this).closest(".js-row").after(template);
});

// 必ず送料計算ボタンをクリックさせる
$(document).on('change', '.js-product-select, .js-product-count, .js-vertical, .js-horizontal', function() {
      // ここに変更があった時の処理を記述
      console.log('Input value changed! New value:', $(this).val());
    if (window.user.role === 2) {
      $('#post-transaction').prop('disabled', true).addClass('disabled btn-secondary');;
    } 
});

// submit時の関数
$(document).on("click", "#post-transaction", function (event) {
  event.preventDefault();
  // input shipping_cost
  // calculateShipping().then(function (shippingCost) {
  //   $('input[name="t[shipping_cost]"]').val(shippingCost);
  // }).catch(function (error) {
  //   alert('送料の計算ができませんでした。');
  //   return false;
  // });
  // エラーメッセージがあればバリデーションとreturn falseを実行する
  var alertMessage = '';

  //　バリデーション
  // if ($('input[name="t[shipping_cost]"]').val() == "") {
  //   alert("送料計算ボタンを押してから確定してください。");
  //   return false;
  // }
  if ($('textarea[name="t[address]"]').val() == "") {
    alert("受け取り場所を入力してください（選択ボタンで選択）");
    return false;
  }
  if ($('textarea[name="t[consignee]"]').val() == "") {
    alert("荷受人様の名前を入力してください（選択ボタンで選択）");
    return false;
  }
  if ($('input[name="t[tel]"]').val() == "") {
    alert("荷受け人様連絡先TELを入力してください");
    return false;
  }
  if ($(".culc-wrapper").find("option:selected").val() == "") {
    alertMessage = '未入力の項目があります。'
  }
  $('main').find("input:not(:hidden, #discount, #special-discount, #direct_shipping, .js-date)").each(function (i, element) {
    if ($(element).val() == "") {
      alertMessage = '未入力の項目があります。'
    }
  });
  if (!$('input[name="t[delivery_at3]"]').hasClass('d-none') && $('input[name="t[delivery_at3]"]').val() == '') {
    alertMessage = '第3納品希望日を入力してください。'
  }
  if (!$('input[name="t[delivery_at2]"]').hasClass('d-none') && $('input[name="t[delivery_at2]"]').val() == '') {
    alertMessage = '第2納品希望日を入力してください。'
  }
  if ($('input[name="t[delivery_at]"]').val() == '') {
    alertMessage = '納品希望日を入力してください。'
  }
  // 数量0のバリデーション
  if (!zeroValidation()) {
    alertMessage = '数量に0は入力できません。'
  }

  // セレクトボックスのバリデーション
  $(".js-row").each(function (i, element) {
    $(element).find("select").each(function (i, element) {
      if ($(element).find("option:selected").val() == "" || $(element).find("option:selected").text() == "商品を選択してください") {
        alertMessage = '未選択の商品行があります。\n「商品を選択してください」のドロップダウンもご確認ください';
      }
    });

    $(element).find(".js-row-id").attr("name", `pt[${i}][row_id]`);
    $(element).find(".js-product-select").attr("name", `pt[${i}][product_id]`);
    $(element).find(".js-product-name").attr("name", `pt[${i}][name]`);
    $(element).find(".js-product-count").attr("name", `pt[${i}][num]`);
    $(element).find(".js-unit-price").attr("name", `pt[${i}][unit_price]`);
    $(element).find(".js-unit").attr("name", `pt[${i}][unit]`);
    $(element).find(".js-is-cut").attr("name", `pt[${i}][is_cut]`);
    $(element).find(".js-cut-set-num").attr("name", `pt[${i}][set_num]`);
    $(element).find(".js-vertical").attr("name", `pt[${i}][vertical]`);
    $(element).find(".js-horizontal").attr("name", `pt[${i}][horizontal]`);
    $(element).find(".js-price").attr("name", `pt[${i}][price]`);
    $(element).find(".js-area").attr("name", `pt[${i}][area]`);
    $(element).find(".js-other-product-name").attr("name", `pt[${i}][other_product_name]`);
    $(element).find("#transport-state").attr("name", "transport_state");
    if ($(element).find(".js-other-product-name").length > 0) {
      //その他商品にはvalue指定する必要がある
      var otherProductName = $(element).find(".js-other-product-name").val();
      $(element).find(".js-other-product-name").attr('value', otherProductName);
    }
    // カットメニューとカット対象の人工芝を紐づけるために使用
    $(element).find(".js-parent-id").attr("name", `pt[${i}][parent_id]`);
    if ($(element).find(".js-is-cut").val() == 1 && typeof $(element).find(".js-parent-id").val() !== "undefined") {
      $('#transactionForm').append(`<input type='hidden' name="cut[${i}][parent_id]" value="${$(element).find('.js-parent-id').val()}">`)
      $('#transactionForm').append(`<input type='hidden' name="cut[${i}][product_id]" value="${$(element).find('.js-product-select').val()}">`)
      $('#transactionForm').append(`<input type='hidden' name="cut[${i}][num]" value="${$(element).find('.js-cut-length').val()}">`)
      $('#transactionForm').append(`<input type='hidden' name="cut[${i}][unit_price]" value="${$(element).find('.js-unit-price').val()}">`)
    }
  });
  //送るチェックボタン保持用
  // $('input[name="t[address_type]"]:checked').val()


  if (alertMessage != '') {
    alert(alertMessage);
    return false;
  }
  // 以下編集時の対応
  // もし工場引取なら、第2第3納品希望日を消す
  if( window.isFactoryPickUp ){
    $('.datepicker2').val('').addClass('d-none');
    // $('.datepicker2');
    $('.datepicker3').val('').addClass('d-none');
    // $('.datepicker3').addClass('d-none');
  }
  // 第2納品希望日と第3納品希望日が必要かどうか最後に判断
  if( isClothTurfOnly() && !window.isFactoryPickUp ){
    const turfCount = calcTurfCount();
    const turfCount40mm = calcTurfCount40mm();
    // 第2納品希望日
    if( turfCount < 6 && turfCount40mm < 4){
      $('.datepicker2').val('');
    }
    // 第2納品希望日
    if( turfCount < 11 && turfCount40mm < 7){
      $('.datepicker3').val('');
    }
  }
  var content = $(".culc-wrapper").html();
  localStorage.setItem("transaction", content);
  $("#transactionForm").submit();
});

$(document).on("click", ".js-remove-row", function () {
  $(this).parent().parent().fadeOut(700).queue(function () {
    var target = $(this).find('.js-row-id').val();

    //芝行を削除したらカットメニューも削除
    $(".js-row--child").each(function (i, element) {
      if ($(element).find(".js-parent-id").val() == target) {
        $(element).remove();
      }
    });
    //紐づく行もすべて削除
    var cnt = 0;
    var target = $(this).find('.js-row-id').val();
    $(".js-row--child, .js-cut-price-row").each(function (i, element) {
      if ($(element).find(".js-parent-id").val() == target) {
        $(element).remove();
      }
    });
    if($(this).hasClass('js-row--child')){
      var parentId = $(this).find('.js-parent-id').val();
      $(".js-row--child").each(function (i, element) {
        if ($(element).find(".js-parent-id").val() == parentId) {
          cnt++;
        }
      });
      if (cnt == 1){
        $(this).closest('tbody').find(`.js-row-id[value=${parentId}]`).closest('tr').find('.js-before-id').val('0');
        $(this).closest('tbody').find(`.js-row-id[value=${parentId}]`).closest('tr').remove();
        $(this).closest('tbody').find('.js-cut-price-row').find(`.js-parent-id[value=${parentId}]`).closest('tr').remove();
      }
    }

    this.remove();

    subTotal();
    setTimeout(() => {
      $(this).remove();
      totalAreaCount();
    }, 1000);
  });
});

// バリデーションや修正ボタン押した時の復帰用にフォーム保持
$(document).on("change", "input", function () {
  var value = $(this).val();
  $(this).attr("value", value);
});
$(document).on("change", "select", function () {
  var value = $(this).val();
  $(this)
    .children(`option[value="${value}"]`)
    .attr("selected", true);
});

// 大口割引の計算
function calcDiscountRate(area) {
  // console.log(area);
  switch (true) {
    case area > 1999:
      return 0.20
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

// 小計・合計計算・の設定
function subTotal() {
  console.log('subTotal');
  var discount = isNaN(parseInt($('#discount').val())) ? 0 : parseInt($('#discount').val());
  var specialDiscount = isNaN(parseInt($('#special-discount').val())) ? 0 : parseInt($('#special-discount').val());
  console.log('specialDiscount : ' + specialDiscount);
  $(".js-row--child").each(function (i, element) {
    var count = parseFloat($(element).find(".js-product-count").val());
    var unitPrice = $(element).find(".js-unit-price").val()
    $(element).find(".js-price").val(Math.round(count * unitPrice));
  });
  var subtotal = 0;
  // 表示用の小計変数
  var showSubtotal = 0;
  // $(".js-price:not(#discount)").not('.js-cut-turf-price').each(function (i, element) {
  $(".js-price").not("#discount").not("#special-discount").each(function (i, element) {
    // 編集時に金額代入
    var count = parseFloat($(this).closest(".js-row").find(".js-product-count").val());
    var unitPrice = $(this).closest(".js-row").find(".js-unit-price").val();
    $(this).closest(".js-row").find(".js-price").val(Math.round(count * unitPrice));
    // カット人工芝のテーブルは別の場所で計算
    if($(this).closest('#product-cut-body').length < 1){
      var rowPrice = parseInt($(element).val())
      rowPrice = !isNaN(rowPrice) ? rowPrice : 0
      subtotal = subtotal + rowPrice;
    }
  });
  
  //カット人工芝の小計用の合計金額
  $(".js-cut-price-row").each(function (i, element) {
    // こっちは各行計算用
    const parentId = $(element).find('.js-parent-id').val();
    calcCutSetPrice(null, parentId)
    // こっちは小計用
    var rowPrice = parseInt($(element).find('.js-cut-set-price').val())
    rowPrice = !isNaN(rowPrice) ? rowPrice : 0
    subtotal = parseInt(Math.round(subtotal + rowPrice));
  });

  console.log(!isNaN(specialDiscount));
  console.log(specialDiscount !== null);
  console.log(window.user.role === 1);
  if (!isNaN(subtotal)) {
    let discountTotal = 0;
    if ((!isNaN(discount) || discount !== null) && window.user.role === 1) {
        discountTotal += discount;
    }
    if ((!isNaN(specialDiscount) || specialDiscount !== null) && window.user.role === 1) {
        discountTotal += specialDiscount;
    }
    showSubtotal += Math.floor(subtotal - discountTotal);
  }

  const shippingCost = isNaN(parseInt($('#shipping-cost').val())) ? 0 : parseInt($('#shipping-cost').val());
  showSubtotal += shippingCost;
  console.log('showSubtotal : ' + showSubtotal);
  $("#subTotal").empty();
  $("#subTotal").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(showSubtotal));
  console.log('subtotal : ' + subtotal);
  var tax = Math.floor(showSubtotal * window.taxRate);
  $("#tax").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(tax));
  var total = Math.floor(showSubtotal * (1 + window.taxRate));
  $("#total").text(new Intl.NumberFormat("ja-JP", { style: "currency", currency: "JPY" }).format(total));
  $('input[name="t[sub_total]"]').val(subtotal);
  $('input[name="t[total]"]').val(total);
}

//Googleスプレッドシートから取得したデータを配列に変換
function parseHolidayData(data) {
  var arr = [];
  for (var i = 0; i < data.length; i++) {
    var dd = data[i];
    arr[i] = dd.holiday;
  }
  return arr;
}
//APIから祝日を取得
// return value array[]
function getPublicHoliday() {
  // 通信回線がない時は空配列返す
  // =====<<<<< オフライン開発用 TODOデプロイする時は必ず削除 >>>> ======
  // return [];
  var date = new Date();
  date.setDate(date.getDate());
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var result = [];

  var res = $.ajax({
    type: 'GET',
    url: 'https://holidays-jp.github.io/api/v1/' + year + '/date.json',
    dataType: 'json',
    async: false,
    responseType: 'json'
  }).responseText;
  var data = JSON.parse(res)
  var key = Object.keys(data)

  for (var i = 0; i < key.length; i++) {
    result[i] = formatDate(key[i])
  }

  if (month === 12 || month === 11) {
    var nextYear = date.getFullYear() + 1;
    var res = $.ajax({
      type: 'GET',
      url: 'https://holidays-jp.github.io/api/v1/' + nextYear + '/date.json',
      dataType: 'json',
      async: false,
      responseType: 'json'
    }).responseText;
    var data = JSON.parse(res)
    var key = Object.keys(data)

    for (var i = 0; i < key.length; i++) {
      result.push(formatDate(key[i]))
    }
  }
  return result;
};

// x営業日後の日付
function getDeliveryDate(x) {
  var officeHolidays = parseHolidayData(window.officeHoliday);

  var days = [];
  for (var i = 1; i < x + 1; i++) {
    days[i - 1] = getDate(i)
  }

  var deliveryDay = "";
  var publicHoliday = publicHolidaySum(x);
  var dayOfWeek = dayOfWeekEndSum(x);
  var holiday = setHolidaySum(officeHolidays, x);
  var sum = publicHoliday + dayOfWeek + holiday;
  var cnt = 0;
  for (var i = 1; i < 100; i++) {
    if (sum != 0) {
      if (checkHoliday(officeHolidays, getDate(x + i)) == false && getDayOfWeek(x + i) != 0 && getDayOfWeek(x + i) != 6 && checkPublicHoliday(getDate(x + i)) == false) {
        cnt++;
      }
      if (sum == cnt) {
        deliveryDay = getDate(x + i);
        break;
      }
    } else if (sum == 0) {
      deliveryDay = getDate(x);
      break;
    }
  }
  console.log('deleveryDay : ' + deliveryDay);
  return deliveryDay;
}

//x日間の会社休日をカウント
function setHolidaySum(holiday, x) {
  var cnt = 0;
  var days = [];

  for (var i = 1; i < x + 1; i++) {
    days[i - 1] = getDate(i);
  }
  for (var i = 0; i < days.length; i++) {
    for (var j = 0; j < holiday.length; j++) {
      // 会社休日カレンダーが土日なら追加しない
      var isIncludeOfficeHoliday = dayjs(days[i]).format('YYYY-MM-DD') === dayjs(holiday[j]).format('YYYY-MM-DD');
      var isWeekEnd = dateTodayOfWeek(holiday[j]) === 0 || dateTodayOfWeek(holiday[j]) === 6 ;
      if ( isIncludeOfficeHoliday && isWeekEnd === false ) {
        cnt++;
      }
    }
  }
  console.log('期間中の会社休日：' + cnt);
  return cnt;
}
// 特定の日付から会社休日かどうかを判定
function checkHoliday(holiday, day) {
  for (var j = 0; j < holiday.length; j++) {
    if (dayjs(day).format('YYYY-MM-DD') === dayjs(holiday[j]).format('YYYY-MM-DD')) {
      return true
    }
  }
  return false;
}

// x日間に祝日があるかを判定
function publicHolidaySum(x) {
  var days = [];
  for (var i = 1; i < x + 1; i++) {
    days[i - 1] = getDate(i)
  }
  var publicHoliday = getPublicHoliday();

  var cnt = 0;
  for (var i = 0; i < days.length; i++) {
    for (var j = 0; j < publicHoliday.length; j++) {
      if (days[i] == publicHoliday[j] && checkDayOfWeek(days[i]) == false) { //祝日と土曜がかぶっていたらカウントしない
        cnt++;
      }
    }
  }
  console.log('祝日の数 : ' + cnt);
  return cnt;
}
//特定の日付が祝日かどうか
function checkPublicHoliday(day) {
  var publicHoliday = getPublicHoliday();

  for (var i = 0; i < publicHoliday.length; i++) {
    if (day == publicHoliday[i]) {
      return true;
    }
  }
  return false;
}

//x日間に土日が何日あるかを取得
function dayOfWeekEndSum(x) {
  var cnt = 0;
  for (var i = 1; i < x + 1; i++) {
    if (getDayOfWeek(i) == 0 || getDayOfWeek(i) == 6) {
      cnt++;
    }
  }
  console.log('土日の数： ' + cnt);
  return cnt;
}

//特定の日付が土日かどうか
function checkDayOfWeek(date) {
  var date = new Date(date);
  var res = date.getDay();
  //console.log(res)
  if (res === 0 || res === 6) {
    return true;
  }
  return false;
}

// ○日後の曜日の取得(0 or 6なら土日) → 返り値は数値（曜日）
function getDayOfWeek(day=1) {
  var date = new Date();
  date.setDate(date.getDate() + day);
  var dayOfWeek = date.getDay();
  return dayOfWeek;
}

function getDate(day) {
  var date = new Date();
  date.setDate(date.getDate() + day);
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var day = date.getDate();
  return String(year) + "-" + String(month) + "-" + String(day);
}

//特定の日付を指定の形式にフォーマット
function formatDate(date) {
  var date = new Date(date);
  date.setDate(date.getDate());
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var day = date.getDate();
  return String(year) + "-" + String(month) + "-" + String(day);
}

// 日付が変更されたらdatepickerの制約を更新
$(document).on( 'change','.datepicker, .datepicker2, datepicker3', function() {
  checkTransPortState();
})
// Duskテスト用（通常は直接値を入力できない）
$(document).on( 'keyup', '.datepicker, .datepicker2, datepicker3', function() {
  if($(this).val().length === 10){
    checkTransPortState();
  }
})
// 案件紐付けが変更されたら納品希望日を更新
$(document).on( 'change','#contact-select', function() {
  $("#contact-id").val($("#contact-select").val());
  checkTransPortState();
})

function setDatePicker(state, isCharter = false) {
  var minDate;
  var arrivalDate = $(".js-date").val();
  // 編集のときはお届け日アラート出さない
  minDate = getDeliveryDate(state);
  $('#test3').text(arrivalDate);
  $('#test').text(new Date(arrivalDate));
  $('#test2').text(new Date(minDate));
  if (new Date(arrivalDate) < new Date(minDate) && window.user.role === 2 && referrer != "comfirm") {
    alert("到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。");
    $(".datepicker").flatpickr({
      enableTime: false,
      dateFormat: "Y-m-d",
      locale: "ja",
      allowInput: window.appEnv === 'local' ||window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
      defaultDate: null,
      minDate: window.user.role === 2 ? minDate : null
    });
  }else{
    $(".datepicker").flatpickr({
      enableTime: false,
      dateFormat: "Y-m-d",
      locale: "ja",
      allowInput: window.appEnv === 'local' ||window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
      defaultDate: arrivalDate,
      minDate: window.user.role === 2 ? minDate : null
    });
    // 値入れ直さないとTEXTに反映されない
    $('input[name="t[delivery_at]"]').val(arrivalDate);
  }
  $('.datepicker2').addClass('d-none');
  $('.datepicker2').prev().addClass('d-none');
  $('.datepicker3').addClass('d-none');
  $('.datepicker3').prev().addClass('d-none');
  $('#transport-state').val(state);
  // 登録案件かつ、反物オンリーかつ、工場引取でない場合は第2納品希望日と第3納品希望日の判定を行う
  if( window.registration && isClothTurfOnly() && !window.isFactoryPickUp){
    const turfCount = calcTurfCount();
    const turfCount40mm = calcTurfCount40mm();
    // 第2納品希望日
    const secondDate = dayjs($('input[name="t[delivery_at]"]').val()).add(1, 'day').format('YYYY-MM-DD');
    if( turfCount > 5 || turfCount40mm > 3){
      console.log('fire!  : 第2納品希望日');
        console.log($('input[name="t[delivery_at]"]').val());
        console.log($('input[name="t[delivery_at]"]').val() === '');
      $('.datepicker2').removeClass('d-none');
      $('.datepicker2').prev().removeClass('d-none');
      var options = {};
      // 第1納品希望日入力していなければ入力させない
      if(!$('input[name="t[delivery_at]"]').val()){
        options = { 
          dateFormat: "Y-m-d",
          locale: "ja",
          disable: [{ from: "2020-04-01",to: "2150-05-01"}]
        }
      }else{
        options = {
          enableTime: false,
          dateFormat: "Y-m-d",
          locale: "ja",
          allowInput: window.appEnv === 'local' || window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
          defaultDate: null,
          minDate: window.user.role === 2 ?  secondDate : null
        }
      }
      // もし第1納品希望日が変更された場合、第2納品希望日と比較して初期化するか判断
      var firstDate = dayjs($('input[name="t[delivery_at]"]').val());
      var secondInput = dayjs($('input[name="t[delivery_at2]"]').val());
      if( firstDate.isSame(secondInput) || firstDate.isAfter(secondInput) || secondInput === 'Invalid Date'){
        $(".datepicker2").flatpickr(options);
      }else if($('input[name="t[delivery_at]"]').val() === '' || ($('input[name="t[delivery_at]"]').val() && $('input[name="t[delivery_at2]"]').val() === '')){
      // 初回起動時
      // 初回起動時 第1納品希望日が入力されていて、第2だけnullなら初期化やり直し
        $(".datepicker2").flatpickr(options);
      }
    }
    // 第3納品希望日
    if( turfCount > 10 || turfCount40mm > 6){
      console.log('fire!  : 第3納品希望日');
      var secondInput = dayjs($('input[name="t[delivery_at2]"]').val());
      $('.datepicker3').removeClass('d-none');
      $('.datepicker3').prev().removeClass('d-none');
      var options = {};
      // 第2納品希望日入力していなければ入力させない
      if($('input[name="t[delivery_at2]"]').val() == ''){
        options = { 
          dateFormat: "Y-m-d",
          locale: "ja",
          disable: [{ from: "2020-04-01",to: "2150-05-01"}]
        }
      }else{
        options = {
          enableTime: false,
          dateFormat: "Y-m-d",
          locale: "ja",
          allowInput: window.appEnv === 'local' || window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
          defaultDate: null,
          minDate: window.user.role === 2 ? dayjs(secondInput).add(1, 'day').format('YYYY-MM-DD') : null
        }
      }
      // もし第2納品希望日が変更された場合、第3納品希望日と比較して初期化するか判断
      var firstDate = dayjs($('input[name="t[delivery_at]"]').val());
      var thirdInput = dayjs($('input[name="t[delivery_at3]"]').val());
      if( $('input[name="t[delivery_at]"]').val() === '' || secondInput.isSame(thirdInput) || secondInput.isAfter(thirdInput) || thirdInput === '' || firstDate.isSame(thirdInput)|| firstDate.isAfter(thirdInput)){
        $(".datepicker3").flatpickr(options);
      }else if($('input[name="t[delivery_at]"]').val() === '' || ($('input[name="t[delivery_at]"]').val() && $('input[name="t[delivery_at2]"]').val() && $('input[name="t[delivery_at3]"]').val() === '' )){
      // 初回起動時 第1と第2納品希望日が入力されていて、第3だけnullなら初期化やり直し
        $(".datepicker3").flatpickr(options);
      }
    }//if第3納品希望日
    // チャーター便なら隠す&入力消す
    if(isCharter){
      $('.datepicker2').addClass('d-none').val('');
      $('.datepicker2').prev().addClass('d-none');
      $('.datepicker3').addClass('d-none').val('');
      $('.datepicker3').prev().addClass('d-none');
    }
    $("#transport-state").val(state);
  } // if
}

function totalAreaCount() {
  var totalArea = 0;
  window.discount = 0
  // 反物の面積計算
  $("#product-body .js-row:not(.js-row--child)").each(function (index, element) {
    var productId = $(element).find(".js-turf-select").val();
    var product = window.products.find(v => v.id == productId);
    // 選択されていないと0になり、0になると続きの処理が止まるので分岐
    if (productId !== '0') {
      var num = parseInt($(element).find(".js-product-count").val());
      var target = window.products.find(v => v.id == productId);
      var turfArea = parseInt(target.vertical) * parseInt(target.horizontal) * num;
      totalArea += turfArea;
    }
    const discountRate = calcDiscountRate(turfArea)
    window.discount += parseInt($(element).find('.js-price').val()) * discountRate
  });
  $('#discount').val(window.discount)

  // 切り売りの面積計算
  $("#cut-turf-table .js-row:not(.js-row--child)").each(function (index, element) {
    // 空欄だとNaNになるので、分岐
    if ($.isNumeric($(element).find(".js-area").val())) {
      totalArea += parseInt($(element).find(".js-area").val());
    }
  });

  checkTransPortState();
  window.totalArea = Number.isInteger(totalArea) ? parseInt(totalArea) : window.totalArea;
}

function checkTransPortState() {
  // その他商品にチャーターと入っていれば大口注文の判定
  console.log('fire! checkTransPortState()');
  $(".js-other-product-name").each(function (index, element) {
    if (/チャーター/.test($(element).val() && window.halfPayment)) {
      $(".alert-msg").text("チャーター便の場合は本部と打ち合わせの上、配送希望日をご入力ください。");
      $("#alert-danger").fadeIn(600).delay(1400).fadeOut(4000);
      stateChange = true;
      setDatePicker(20, true);
      return false;
    }
  });
  // 後で紐付けた場合もTRUEにする
  console.log($("#contact-select"));
  var isRegistration = window.registration;
  if(!window.editFlg){
    isRegistration = window.registration || $("#contact-select").val().length != 0 ? true : false; //登録案件かどうか
  }
  var isSubProductOnly = true; //副資材まとめ売りのみかどうか
  var isSampleOnly = true; //サンプルアイテムのみかどうか
  var isSubAndSampleOnly = true // 副資材とサンプルのみかどうか
  // 副資材まとめ売りのみのかどうか
  const notSubOnlyArray = ['js-turf-row', 'js-cut-turf-row', 'js-sales-row', 'js-cut-sub-row', 'js-other-row']
  $(".js-row").each(function (i, element) {
    // 副資材以外のものが含まれているかクラス名をループしてチェック
    notSubOnlyArray.forEach( function(str){
      if ( $(element).attr('class').indexOf(`${str}`) != -1) {
        isSubProductOnly = false;
      }
    });
  });
  if (isSubProductOnly === true) {
    console.log('副資材のみ4営業日');
    setDatePicker(4);
    return false
  }
  //＝＝＝＝＝＝＝ 副資材まとめ売りのみのかどうかここまでで判断＝＝＝＝＝＝＝＝＝

  // ===== サンプルアイテムのみかどうかを判断＝＝＝＝＝＝＝＝
  const notSampleOnlyArray = ['js-turf-row', 'js-cut-turf-row', 'js-sub-row', 'js-cut-sub-row', 'js-other-row']
  $(".js-row").each(function (i, element) {
    // 販促行以外が合った時点でfalse
    notSampleOnlyArray.forEach( function(str){
      if ( $(element).attr('class').indexOf(`${str}`) != -1) {
        isSampleOnly = false
      }
    });
    if ($(element).attr('class') === 'js-row js-sales-row') {
      console.log('販促アイテム行');
      // 販促アイテムで選択されているものがサンプル以外ならfalse
      if (!window.samples.includes(parseInt($(element).find('.js-product-select').val()))) {
        isSampleOnly = false
      }
    }
  });
  // 販促行のみかつ、サンプルだけが選択されていれば4営業日
  if (isSampleOnly === true) {
    console.log('販促業のみかつ、サンプルのみの発注4営業日');
    setDatePicker(4);
    return false
  }
  // ===== サンプルアイテムのみかどうかを判断ここまで＝＝＝＝＝＝＝＝
  // ===== 副資材とサンプルのみでも4営業日 ====
  const notSubAndSampleOnlyArray = ['js-turf-row', 'js-cut-turf-row', 'js-cut-sub-row', 'js-other-row']
  $(".js-row").each(function (i, element) {
    // 販促行と副資材まとめ売り行以外が合った時点でfalse
    notSubAndSampleOnlyArray.forEach( function(str){
      if ( $(element).attr('class').indexOf(`${str}`) != -1) {
        isSubAndSampleOnly = false
      }
    });
    if ($(element).attr('class') === 'js-row js-sales-row') {
      console.log('販促アイテム行');
      // 販促アイテムで選択されているものがサンプル以外ならfalse
      if (!window.samples.includes(parseInt($(element).find('.js-product-select').val()))) {
        isSubAndSampleOnly = false
      }
    }
  });
  // 販促行のみかつ、サンプルだけが選択されていれば4営業日
  if (isSubAndSampleOnly === true) {
    console.log('副資材とサンプルのみの発注4営業日');
    setDatePicker(4);
    return false
  }
  // ===== 副資材とサンプルのみでも4営業日ここまで ====

  // ===== 案件に紐づく発注か、紐付かない発注かどうかを判断＝＝＝＝＝＝＝＝
  //ここからは参考表に分岐に従って処理が進むので、分岐が大きくなるが、readmeにある出荷目安表を参考に見て欲しい
  if (isRegistration) {
    //登録案件の場合の発注内容や引き取り希望などを見て納品日を算出
    // 副資材バラ売りのみかどうか
    if(isCutSubOnly()){
      console.log('副資材バラ売りのみ');
      window.transportState = 7;
      setDatePicker(window.transportState)
    }else if (isClothTurfOnly()) {
      // 反物オンリーの場合
      console.log('登録案件かつ、反物オンリー');
      var turfCount = calcTurfCount()
      var turfCount40mm = calcTurfCount40mm()
      console.log(turfCount);
      console.log(turfCount40mm);
      // 40mmのみならばこっちの分岐
      console.log(turfCount40mm);
      console.log(turfCount - turfCount40mm);
      if (turfCount === turfCount40mm) {
        console.log('40mmのみ');
        switch (true) {
          case turfCount40mm > 6:
            window.transportState = window.isFactoryPickUp ? 5 : 7;
            console.log('登録案件40mmのみかつ、7反以上');
            break;
          case turfCount40mm <= 3:
            window.transportState = window.isFactoryPickUp ? 2 : 4;
            console.log('登録案件40mmのみかつ、3反以下');
            break;
          case turfCount40mm <= 6:
            window.transportState = window.isFactoryPickUp ? 3 : 5;
            console.log('登録案件40mmのみかつ、4〜6反');
            break;
          default:
            window.transportState = window.isFactoryPickUp ? 3 : 5;
            console.log('登録案件40mmのみかつ、4〜6反');
            break;
        }
        console.log('setDatePicker');
        setDatePicker(window.transportState)
      } else { //if (turfCount === turfCount40mm) {
        // 40mmのみでなければこっちの分岐
        console.log('40mmと30mm混合');
        switch (true) {
          case turfCount <= 5:
            window.transportState = window.isFactoryPickUp ? 2 : 4;
            console.log('登録案件かつ、反物オンリーで5反以下');
            break;
          case turfCount > 10:
            window.transportState = window.isFactoryPickUp ? 5 : 7;
            console.log('登録案件かつ、反物オンリーで11反以上');
            break;
          case 6 <= turfCount <= 10:
            window.transportState = window.isFactoryPickUp ? 3 : 5;
            console.log('登録案件かつ、反物オンリーで5〜10反');
            break;
        }
        console.log('setDatePicker');
        setDatePicker(window.transportState)
      } //if (turfCount === turfCount40mm) {
      return false //反物オンリーならここで納期が確定するので、return false
    } else {//if (isClothTurfOnly()) {反物オンリーかどうか
      console.log('登録案件反物とカット混合');
      //カット人工芝 or 副資材バラ売りが含まれる場合
      var turfArea = calcTurfArea()
      var turf40mmArea = calcTurf40mmArea()
      var cutArea = calcCutTotalArea();
      var cut40mmArea = calcCutTotal40mmArea();
      console.log(turfArea, turf40mmArea, cutArea, cut40mmArea);
      if ((turfArea === turf40mmArea) && (cutArea === cut40mmArea)) {
      // 40mmのみかどうか
        console.log('40mmのみ')
        // 40mmのみの場合、60m2未満
        if ((turfArea + cutArea) < 60) {
          window.transportState = window.isFactoryPickUp ? 5 : 7;
          console.log('登録案件かつ、反物カットミックスの40mmのみの60m2未満');
        } else {
          window.transportState = window.isFactoryPickUp ? 7 : 10;
          console.log('登録案件かつ、反物カットミックスの40mmのみの60m2以上');
        }
      } else {
        console.log('40mm以外のある')
        // 混合の場合、100m2未満
        if ((turfArea + cutArea) < 100) {
          window.transportState = window.isFactoryPickUp ? 5 : 7;
          console.log('登録案件かつ、反物カットミックスの40mm・30mm混合の100m2未満');
        } else {
          window.transportState = window.isFactoryPickUp ? 7 : 10;
          console.log('登録案件かつ、反物カットミックスの40mm・30mm混合の100m2以上');
        }
      }
      console.log('setDatePicker');
      setDatePicker(window.transportState)
    }//if反物オンリーかどうか終了
    return false //ここで納期が確定するので、return false
  } else {//if (isRegistration) 登録案件かどうか
    console.log('案件に紐付けない発注');
    // 大口注文の判定
    var clothTurfCnt = 0;
    if (isCharterShipping()) {
      stateChange = true;
      $(".alert-msg").text("チャーター便の場合は本部と打ち合わせの上、配送希望日をご入力ください。");
      $("#alert-danger").fadeIn(600).delay(1400).fadeOut(4000);
      setDatePicker(20, true);
      return false;
    }
    // if ((!window.isFactoryPickUp && clothTurfCnt == 1 && totalArea >= 500) || (window.isFactoryPickUp && clothTurfCnt == 1 && totalArea >= 500 && selectProductId == 2) || (window.isFactoryPickUp && clothTurfCnt == 1 && totalArea >= 800 && selectProductId == 1)) {
    // }
    //資材発注の場合の発注内容や引き取り希望などを見て納品日を算出
    if (isClothTurfOnly() && (calcTurfCount() > 24)) {
      // 反物オンリーの場合
      console.log('資材発注かつ、反物オンリーで25反以上の場合');
      $(".alert-msg").text("チャーター便の場合は本部と打ち合わせの上、配送希望日をご入力ください。");
      $("#alert-danger").fadeIn(600).delay(1400).fadeOut(4000);
      setDatePicker(10, true);
      return false;
    } else {
      console.log('資材発注反物とカット混合');
      //カット人工芝 or 副資材バラ売りが含まれる場合
      var turfArea = calcTurfArea()
      var cutArea = calcCutTotalArea();
      console.log(turfArea, cutArea);
      if ((turfArea + cutArea) < 200) {
        window.transportState = window.isFactoryPickUp ? 7 : 10;
        console.log('資材発注 200m2未満');
      } else {
        window.transportState = window.isFactoryPickUp ? 12 : 15;
        console.log('資材発注 200m2以上');
      }
      console.log('setDatePicker');
      setDatePicker(window.transportState);
      return false;
    }
  }//if (isRegistration) { 登録案件かどうか
  // ===== 案件に紐づく発注か、紐付かない発注かどうかを判断ここまで＝＝＝＝＝
}


// 発注しないボタンをクリック
$(document).on("click", ".skip-btn", function () {
  if (!confirm('この案件では資材の発注を行いません。よろしいですか？')) {
    /* キャンセルの時の処理 */
    return false;
  } else {
    $('#transactionForm').attr('action', '/transactions/create/order/skip').submit();
  }
})
/* 切り売り各セットでの人工芝金額 */
/* 指定のparent-idの人工芝の金額 */
function calcCutTurfPrice(parentId) {
  var val = parseInt($('.js-row').find(`.js-row-id[value=${parentId}]`).closest('tr').find(".js-price").val());
  return val;
}

/* 切り売り各セットでのカット料金の合計 */
/* 指定のparent-idの人工芝のカットの合計 */
function cutSetTotal(parentId) {
  var total = 0;
  var val = 0;
  $('.js-row--child').each(function (index, element) {
    const priceRow = $(element).find(`.js-parent-id[value=${parentId}]`).closest('tr').find(".js-price")
    val = parseInt($(element).find(`.js-parent-id[value=${parentId}]`).closest('tr').find(".js-price").val());
    val = isNaN(val) ? 0 : val;
    total = total + val;
  });

  return total;
}

function calcCutSetPrice(_this, parentId=null){
  // _thisはjQueryのthisオブジェクト（$(this）)
  // 人工芝行とカット業でparentIdのとり方が変わる
  if( parentId===null ){
    if($(_this).closest('tr').hasClass('js-cut-turf-row')){
      parentId = $(_this).closest("tr").find(".js-row-id").val();
    }else{
      parentId = $(_this).closest("tr").find(".js-parent-id").val();
    }
  }
  var setRow = $(`.js-parent-id[value="${parentId}"]`);
  var num = setRow.closest(".js-cut-price-row").find(".js-cut-set-num").val();
  var cutTurfPrice = calcCutTurfPrice(parentId);
  var cutTotal = cutSetTotal(parentId)
  console.log(cutTurfPrice, cutTotal);
  // parentIdに紐づく合計金額行を探してデータinput
  $(`.js-parent-id[value="${parentId}"]`).closest(".js-cut-price-row").find(".js-cut-set-price").val(num * (cutTurfPrice + cutTotal)).attr("value", num * (cutTurfPrice + cutTotal));
} 

// 見積書展開機能
$(document).on("click", "#convert-quotation", function () {
  appearLoader(message = '見積書を取得中です。', timeout = 5000);
  const contactId = $('#contact-id').val();
  $.get(`/api/quotations/${contactId}`,
    function (res) {
      $('.js-quotations-body').empty();
      $('#product-body').empty();
      $('#product-cut-body').empty();
      $('#sub-body').empty();
      $('#cut-sub-body').empty();
      $('#sales-body').empty();
      $('#etc-body').empty();
      // ボンド＆ターポリンシート展開用の接着剤5kgとターポリンシート25cm幅を準備
      const adhesive = window.products.find(v => v.id == 13 );
      const tarpaulinSheet = window.products.find(v => v.id == 9 );
      // エスキューブ工法なら接合シート1巻に変換
      const joiningSheet = window.products.find(v => v.id == 75 );
      if (res.length > 0) {
        rowCount = 1;
          // ここで商品判断して分岐展開
        res.forEach(function (val, index) {
          // 反物人工芝
          if (val.product_id !== null && val.unit === '反' && val.vertical === null && val.horizontal === null) {
            addTurfRow(val.product_id, val.num, val.pt_unit_price ? val.pt_unit_price : val.unit_price);
            if (dig(val, 'turf_cuts', 0)) {
              val.turf_cuts.forEach(function (cut) {
                addFullCutMenuRow(cut.product_id, cut.num, cut.unit_price, rowCount - 1)
              })
            }
          }
          // 切り売り人工芝
          if (val.product_id !== null && val.unit == '㎡' && val.vertical !== null && val.horizontal !== null) {
            const turfCount = Math.floor(val.num / 20);
            const cutArea = val.num % 20;
            // 20m2以上なら反物と分ける
            if( turfCount > 0 ){
              const turf = window.products.find(v => v.id == val.product_id );
              addTurfRow(val.product_id, turfCount, turf.price)
              addCutTurfRow(val.product_id, val.horizontal, val.vertical, cutArea, val.pt_unit_price ? val.pt_unit_price : val.unit_price);
            }else{
              addCutTurfRow(val.product_id, val.horizontal, val.vertical, val.num, val.pt_unit_price ? val.pt_unit_price : val.unit_price);
            }
            // parentIdはrowCountから1を引いたもの
            if (dig(val, 'turf_cuts', 0)) {
              val.turf_cuts.forEach(function (cut) {
                addCutMenuRow(51, cut.num, rowCount - 1)
              })
            } else {
              addCutMenuRow(51, 0, rowCount - 1)
            }
            addCutSetNumRow(rowCount - 2, val.cut_set_num != null ? val.cut_set_num : 1)
          }
          // 副資材
          if (val.product_id !== null && val.unit !== '反' && val.cut === 0 && val.vertical === null && val.horizontal === null) {
            $('.js-cut-area').prev('h4').removeClass('d-none');
            $('.js-cut-area').removeClass('d-none');
            // ボンド＆ターポリンシートなら分けて行追加
            if(val.product_id === 64){
              addSubRow(adhesive.id, val.num, adhesive.unit, adhesive.pt_unit_price ? adhesive.pt_unit_price : adhesive.unit_price)
              addSubRow(tarpaulinSheet.id, val.num, tarpaulinSheet.unit, adhesive.pt_unit_price ? adhesive.pt_unit_price : tarpaulinSheet.unit_price)
              // エスキューブ工法なら、接合シートに変換 レート＝50mで1巻
            }else if(val.product_id === 79){
              const convertNum = (val.num / 50) + 1;
              addSubRow(joiningSheet.id, val.num, joiningSheet.unit, joiningSheet.pt_unit_price ? joiningSheet.pt_unit_price : joiningSheet.unit_price)
            }else{
              addSubRow(val.product_id, val.num, val.unit, val.pt_unit_price ? val.pt_unit_price : val.unit_price)
            }
          }
          if (val.product_id !== null && val.unit !== '反' && val.unit !== '㎡' && val.unit !== 'm2') {
            $('.js-cut-sub-area').prev('h4').removeClass('d-none');
            $('.js-cut-sub-area').removeClass('d-none');
            if(val.product_id === 64){
              addCutSubRow(adhesive.id, val.num, adhesive.unit, adhesive.unit_price)
              addCutSubRow(tarpaulinSheet.id, val.num, tarpaulinSheet.unit, tarpaulinSheet.unit_price)
              // エスキューブ工法なら、接合シートに変換 レート＝50mで1巻
            }else if(val.product_id === 79){
              const convertNum = (val.num / 50) + 1;
              addSubRow(joiningSheet.id, parseInt(convertNum), joiningSheet.unit, joiningSheet.unit_price)
            }else{
              addCutSubRow(val.product_id, val.num, val.unit, val.unit_price)
            }
          }
          // 手入力
          const excludeList = ['設置作業費', '残材処分費', '下地作業費', '下地改良', '交通費', '出張費', '諸経費', '代金引換手数料', '施工費', '交通費・残材処理費', '下地処理費', '残土処分', '整地工事', '作業費', '砕石']
          if ( !val.product_id && val.other_product == 1 && !excludeList.includes(val.name)) {
            $(".js-etc-area").prev().removeClass("d-none");
            $(".js-etc-area").removeClass("d-none");
            addEtcRow(val.name, val.num, val.unit, val.unit_price);
          }
        }); // EndOfLoop
        subTotal();
      } else {
        alert('見積書が空のようです・・・')
      }
    })
});
// 受け取り場所などの自動入力
$(document).on('change', 'input[name="t[address_type]"]', function(){
  // 一旦直送パラメーターを戻す
  $("#direct_shipping").val('0');
  $('textarea[name="t[address]"]').prop('readonly', false);
  window.isFactoryPickUp = false;
  const user = window.user;
  const contact = window.contact;
  $('textarea[name="t[address]"]').prop('readonly', true);
  var data = {"address": "", "consignee": "", "tel": ""};
  switch ($(this).val()) {
    case "1":
      if(editFlg){
        data.address = "〒" + window.transactions[0].fc_zipcode + " " + window.transactions[0].fc_pref + window.transactions[0].fc_city + window.transactions[0].fc_street;
        data.consignee = window.transactions[0].company_name + " " + window.transactions[0].fc_staff + "様";
        data.tel = window.transactions[0].fc_tel;
      }else{
        data.address = "〒" + user.zipcode + " " + user.pref + user.city + user.street;
        data.consignee = user.company_name + " " + user.staff + "様";
        data.tel = user.tel;
      }
      break;
    case "2":
      if(editFlg){
        data.address = "〒" + window.transactions[0].fc_s_zipcode + " " + window.transactions[0].fc_s_pref + window.transactions[0].fc_s_city + window.transactions[0].fc_s_street;
        data.consignee = window.transactions[0].company_name + " " + window.transactions[0].fc_staff + "様";
        data.tel = window.transactions[0].fc_storage_tel;
      }else if( !user.s_zipcode && !user.s_pref ){
        data.address = "";
        data.consignee = "";
        data.tel = "";
      } else {
        data.address = "〒" + user.s_zipcode + " " + user.s_pref + user.s_city + user.s_street;
        data.consignee = user.company_name + " " + user.staff + "様";
        data.tel = user.storage_tel;
      }
      break;
    case "3":
      data.address = window.factoryAddress;
      data.consignee = user.company_name + " " + user.staff + "様";
      data.tel = !user.s_tel ? user.tel : user.s_tel;
      window.isFactoryPickUp = true;
      break;
    case "4":
      $("#direct_shipping").val('1');
      // 登録案件なら
      if(window.registration){
        data.address = "〒" + contact.zipcode + " " + contact.pref + contact.city + contact.street;
        console.log(parseInt(contact.contact_type_id));
        if(parseInt(contact.contact_type_id) > 4){
          console.log(contact.surname);
          const lastName = !contact.surname ? "御中" : contact.surname + contact.name + " 様";
          data.consignee = contact.company_name + " " + lastName;
          console.log(data);
        }else{
          data.consignee = contact.surname + contact.name + " 様";
        }
        data.tel = contact.tel;
      }else{
        $('textarea[name="t[address]"]').prop('readonly', false);
      }
      break;
    case "5":
      if( user.optional_zipcode && user.optional_pref){
        data.address = "〒" + user.optional_zipcode + " " + user.optional_pref + user.optional_city + user.optional_street;
      }
      if( user.optional_staff){
        data.consignee = user.company_name + " " + user.optional_staff + "様";
      }else{
        data.consignee = user.company_name + "御中";
      }
      if( user.optional_tel){
        data.tel = user.optional_tel;
      }
      $('textarea[name="t[address]"]').prop('readonly', false);
      break;
  
    default:
      break;
  }
  $('textarea[name="t[address]"]').val(data.address);
  $('textarea[name="t[consignee]"]').val(data.consignee);
  $('input[name="t[tel]"]').val(data.tel);
  checkTransPortState();
})

// 送料計算ボタンを押した時とsubmit時の処理
$(document).on("click", "#js-calc", function (e) {
  appearLoader('送料を計算しています。', 100000)
  // Call your async function
  calculateShipping().then(function(result) {
    console.log('Shipping calculated');
    console.log(result);
    if(!isNaN(result)){
      $("input[name='t[shipping_cost]']").val(result);
      subTotal();
      $('.loader').remove();
      $('#post-transaction').prop('disabled', false).removeClass('disabled btn-secondary');;
    }else{
      $('.loader').remove();
      alert('全ての商品を選択してから送料を計算してください');
    }
  }).catch(function(err) {
    console.log(err);
    $('.loader').remove();
    if(err == 'shippingPrefecture is null'){
      alert('発送先の住所を選択してください');
    }
  });
});