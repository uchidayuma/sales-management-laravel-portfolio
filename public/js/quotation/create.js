var rowCount = 1;
var remodal = $('[data-remodal-id=upload-modal]').remodal();
$(document).ready(function () {
  // remodalのインスタンス化
  $('#upload-modal-open').on('click', function () {
    remodal.open();
  })
  // パスの取得
  if ((document.URL.match('new') && window.quotations.length === 0) || window.quotationType === 1) {
    addRow();
    addRow();
    addRow();
    addRow('2');
    addRow('2');
    addRow('2');
    addFreeRow('下地作業費', '㎡')
    addFreeRow('設置作業費', '㎡')
    addFreeRow('残材処分費', '式', 1)
    addFreeRow('交通費', '式', 1)
    addFreeRow('諸経費', '式', 1)
  } else {
    // 編集の場合
    window.quotations.forEach(function (val, index) {
      addEditRow(val);
    });
    calcRowPrice();
    // 編集の時は再計算
    if (window.discount != 0) {
      $('#quotationTableBody').append(
        `
        <tr class='js-discount-row' dusk="tr-${rowCount}">
          <td class=''>
            <i class="fas fa-grip-lines handle"></i>
          </td>
          <td class='w30 js-product-td'>
            <div class="form-group">
              <input type='text' class='form-control' value='お値引き' readonly>
            </div> 
          </td>
          <td class='w12'>
            <input type="number" name="number" class="js-product-count product-count form-control dusk-count-${rowCount}" readonly>
          </td>
          <td class='w10'>
            <input type="text" name="unit" class="js-unit unit form-control dusk-unit-${rowCount}" readonly>
          </td>
          <td class='w15'>
            <input type="number" name="unit-price" class="js-unit-price unit-price form-control dusk-unit-price-${rowCount}" readonly>
          </td>
          <td class='w15'>
            <input id='js-discount' type="number" name="q[discount]" class="js-discount form-control" value="${window.discount}" placeholder='値引額' dusk='discount'>
          </td>
          <td class='w20'>
          <td class='w10 pointer'>
            <i class="fas fa-times fa-2x js-remove-row pointer"></i>
          </td>
        </tr>
        `
      )
    }
    console.log(window.quotations[0].discount !== null);
    if (window.quotations[0].discount) {
      addDiscountRow(window.quotations[0].discount);
    }
    setTimeout(() => {
      calcRowPrice();
      subTotal();
    }, 2000);
  }

  // $(".datepicker").flatpickr({
  //   enabletime: false,
  //   dateformat: "y-m-d",
  //   locale: "ja",
  //   defalutDate: new Date(window.quotations[0].created_at),
  // });
  if (window.pq.length > 0) {
    afterValidateRender();
  }
});

function addRow(type = 0, selectedId = null) {
  var productList = []
  if(type == '0'){
    window.products.filter(p => p.product_type_id === 1).forEach((product) => {
      productList.push(`<option value=${product.id}>${product.name}</option>`);
    });
  }else if(type == '2'){
    window.products.filter(p => p.product_type_id === 2 && p.is_use_quotation === 1).forEach((product) => {
      productList.push(`<option value=${product.id}>${product.name}</option>`);
    });
  }
  var template = `
      <tr class='js-row' dusk="tr-${rowCount}">
        <td class=' align-middle'>
          <i class="fas fa-grip-lines handle"></i>
        </td>
        <td class='js-product-td'>
          <div class="form-group">
            <select class="form-control js-product-select product-select" name="product-select-${rowCount}">
              <option selected value="0" disabled>商品を選択してください</option>
              ${productList}
            </select>
          </div> 
        </td>
        <td class=''>
          <input type="number" name="number" class="js-product-count product-count form-control dusk-count-${rowCount}" step="0.1">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit unit form-control dusk-unit-${rowCount}" value="${type == '0' ? '㎡' : '㎡/円'}">
        </td>
        <td class=''>
          <input type="number" name="unit-price" class="js-unit-price unit-price form-control dusk-unit-price-${rowCount}">
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price price form-control" value="0" readonly>
        </td>
        <td class=''>
          <select class="form-control js-type type dusk-type-${rowCount}">
            <option value='0' ${type == '0' ? 'selected' : ''}>人工芝</option>
            <option value='2' ${type == '2' ? 'selected' : ''}>副資材</option>
            <option value='1' ${type == '1' ? 'selected' : ''}>自由記述</option>
        </td>
        <td>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="">
        </td>
        <td class='pointer align-middle'>
          <i class="fas fa-times fa-3x js-remove-row color-light-black pointer" dusk="remove-${rowCount}"></i>
        </td>
      </tr>
    `
  $('#quotationTableBody').append(template);
  $("#quotationTableBody").sortable({
    handle: '.handle',
  });
  rowCount++;
};

function addFreeRow(productName = null, unit = '', count = null, unitPrice = null,) {
  var productList = []
  window.products.filter(p => p.product_type_id === 1).forEach((product) => {
    productList.push(`<option value=${product.id}>${product.name}</option>`);
  });
  var template = `
      <tr class='js-row' dusk="tr-${rowCount}">
        <td class='align-middle'>
          <i class="fas fa-grip-lines handle"></i>
        </td>
        <td class='js-product-td'>
          <input type="text" class="js-product-name product-select form-control dusk-product-name-${rowCount}" value="${productName}" placeholder="商品名を自由に記述してください">
        </td>
        <td class=''>
          <input type="number" name="number" class="js-product-count product-count form-control dusk-count-${rowCount}" value="${count}" step="0.1">
        </td>
        <td class=''>
          <input type="text" name="unit" class="js-unit unit form-control dusk-unit-${rowCount}" value="${unit}">
        </td>
        <td class=''>
          <input type="number" name="unit-price" value="${unitPrice}" class="js-unit-price unit-price form-control dusk-unit-price-${rowCount}">
        </td>
        <td class=''>
          <input type="number" name="price" class="js-price price form-control" value="0" readonly>
        </td>
        <td class=''>
          <select class="form-control js-type dusk-type-${rowCount}">
            <option value='0'>人工芝</option>
            <option value='2'>副資材</option>
            <option value='1' selected>自由記述</option>
        </td>
        <td class=''>
          <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value="">
        </td>
        <td class='w10 pointer align-middle'>
          <i class="fas fa-times fa-3x js-remove-row color-light-black pointer"></i>
        </td>
      </tr>
    `
  $('#quotationTableBody').append(template);
  $("#quotationTableBody").sortable({
    handle: '.handle',
  });
  // 商品名がない行なら属性自体を消す
  if (productName === null) {
    $(`.dusk-product-name-${rowCount}`).removeAttr('value');
  }
  rowCount++;
};

$('#quotationTableBody').on('change', '.js-product-select', function () {
  var target = window.products.find((v) => v.id == $(this).val());
  var rowUnitPrice = 0;
  if( target.is_same_cut_price === 1 ){
    rowUnitPrice = target.price;
  }else if(target.cut_price != null ) {
    rowUnitPrice = target.cut_price
  }else{
    rowUnitPrice = target.price
  }
  $(this).closest('.js-row').find('.js-unit-price').val(rowUnitPrice);
  // 行の金額算出
  var count = parseFloat($(this).closest('.js-row').find('.js-product-count').val());
  count = count !== '' ? Number(parseFloat(count)) : 0

  var unitPrice = $(this).closest('.js-row').find('.js-unit-price').val();
  unitPrice = Number(unitPrice);

  if (target.product_type_id == 2) {
    var rowUnit = target.is_same_cut_price === 1 ? target.unit : target.cut_unit
    $(this).closest('.js-row').find('.js-unit').val(rowUnit);
  }

  $(this).closest('.js-row').find('.js-price').val(Math.round(count * unitPrice));
  subTotal();
});

$('#quotationTableBody').on('click', '.js-remove-row', function () {
  $(this).parent().parent().fadeOut(700).queue(function () {
    this.remove();
    subTotal();
  })

  if (document.URL.match('edit')) {

  }
})

$('#quotationTableBody').on('change', '.js-type', function () {
  $(this).closest('.js-row').children('.js-product-td').empty();
  // 商品から選択ならば
  switch (parseInt($(this).val())) {
    case 1:
      $(this).closest('.js-row').children('.js-product-td').append(`<input type="text" class="js-product-name form-control dusk-product-name-${rowCount}" placeholder="商品名を自由に記述してください">`);
      $(this).closest('.js-row').find('.js-unit').val('');
      break;

    case 0:
      var productList = []
      window.products.filter(p => p.product_type_id === 1).forEach((product) => {
        productList.push(`<option value=${product.id}>${product.name}</option>`);
      });
      $(this).closest('.js-row').children('.js-product-td').append(`
        <div class="form-group">
          <select class="form-control js-product-select">
            <option selected disabled>商品を選択してください</option>
            ${productList}
          </select>
        </div> 
      `);
      $(this).closest('.js-row').find('.js-unit').val('');
      break;

    case 2:
      var productList = []
      window.products.filter(p => p.product_type_id === 2 && p.is_use_quotation === 1).forEach((product) => {
        // if (product.is_same_cut_price === 0) {
        productList.push(`<option value=${product.id}>${product.name}</option>`);
        // }
      });
      $(this).closest('.js-row').children('.js-product-td').append(`
        <div class="form-group">
          <select class="form-control js-product-select">
            <option selected disabled>商品を選択してください</option>
            ${productList}
          </select>
        </div> 
      `);
      $(this).closest('.js-row').find('.js-unit').val('㎡/円');
      break;

    default:
      $(this).closest('.js-row').children('.js-product-td').append(`<input type="text" class="js-product-name form-control dusk-product-name-${rowCount}" placeholder="商品名を自由に記述してください">`);
      $(this).closest('.js-row').find('.js-unit').val('');
      break;
  }
  if ($(this).val() == 1) {
  } else {
    // 自由記述ならば
  }
})

$('#quotationTableBody').on('keyup', '.js-product-count,.js-unit-price', function culcRow() {
  // 行の金額算出
  var count = $(this).closest('.js-row').find('.js-product-count').val();
  count = count !== '' ? Number(parseFloat(count)) : 0

  var unitPrice = $(this).closest('.js-row').find('.js-unit-price').val();
  unitPrice = Number(unitPrice);

  $(this).closest('.js-row').find('.js-price').val(Math.round(count * unitPrice));
  subTotal();
})

// 編集の時は再計算
/*
function rowTotal() {
  // 行の金額算出
  $('.js-row').each(function (index, element) {
    console.log($(element));
    var count = parseFloat($(element).find('.js-product-count').val());
    console.log(count);
    var unitPrice = $(element).find('.js-unit-price').val();
    console.log(unitPrice);
    $(element).find('.js-price').val(Math.round(count * unitPrice));
  });

}
*/

function subTotal() {
  var subtotal = 0;
  $('.js-price').each(function (i, element) {
    var rowPrice = parseInt($(element).val())
    rowPrice = !isNaN(rowPrice) ? rowPrice : 0
    subtotal = (subtotal + rowPrice);
    subtotal = quotationTax(subtotal);
  })
  if ($.isNumeric($('#js-discount').val())) {
    subtotal -= parseInt($('#js-discount').val());
  }
  $('#subTotal').empty();
  $('#subTotal').text(new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY' }).format(subtotal));
  $('input[name="q[sub_total]"]').val(subtotal);
  var tax = quotationTax(subtotal,0.1);
  $('#tax').text(new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY' }).format(tax));
  var total = quotationTax(subtotal,1.1);
  $('#total').text(new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY' }).format(total));
  $('input[name="q[total]"]').val(quotationTax(subtotal,1.1));
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

$('#quotationForm').on('click', '.js-plus', function () {
  addRow();
})

$('#quotationForm').on('keyup', '#js-discount', function () {
  subTotal()
})

$('#quotationForm').on('click', '#post-quotation', function (event) {
  event.preventDefault();
  // 更新なら確認しない
  if (document.URL.match(/edit/) === null) {
    if (!confirm('この内容で見積書を作成します。よろしいですか？')) {
      /* キャンセルの時の処理 */
      return false;
    } else {
      // 見積もり作成の時のみ続けて作るか聞く
      if (!document.URL.match(/edit/)) {
        if (confirm('続けて同じ案件に見積もりを作りますか？')) {
          $('.js-add').val(1);
          if (confirm('見積もり内容をコピーして作りますか？')) {
            $('.js-add-copy').val(1);
          }
        }
      }
    }
  } // if(document.URL.match(/edit/)
  if (window.user.role === 1) {
    $('body').append('<aside class="loader"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i><p class="loader__message">PDFを書き出しています。10秒ほどお待ちください</p></aside>')
  }
  $('.js-row').each(function (i, element) {
    $(element).find('.js-pqid').attr('name', `pq[${i}][id]`);
    $(element).find('.js-product-select').attr('name', `pq[${i}][product_id]`);
    $(element).find('.js-product-name').attr('name', `pq[${i}][name]`);
    $(element).find('.js-product-count').attr('name', `pq[${i}][num]`);
    $(element).find('.js-unit-price').attr('name', `pq[${i}][unit_price]`);
    $(element).find('.js-unit').attr('name', `pq[${i}][unit]`);
    $(element).find('.js-type').attr('name', `pq[${i}][other_product]`);
    $(element).find('.js-pqmemo').attr('name', `pq[${i}][memo]`);
    $(element).find('.js-price').attr('name', `pq[${i}][price]`);
  })
  $('#quotationForm').submit();
});

// 値引き
$('#quotationForm').on('click', '.js-discount-plus', function () {
  if ($('body').find('.js-discount').length > 0) {
    alert('値引き行は1行しか入力できません！')
    return false
  } else {
    var template = `
      <tr class='js-discount-row' dusk="tr-${rowCount}">
        <td class=''>
          <i class="fas fa-grip-lines handle"></i>
        </td>
        <td class='w30 js-product-td'>
          <div class="form-group">
            <input type='text' class='form-control' value='お値引き' readonly>
          </div> 
        </td>
        <td class='w10'>
          <input type="number" name="number" class="js-product-count form-control dusk-count-${rowCount}" readonly>
        </td>
        <td class='w10'>
          <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" readonly>
        </td>
        <td class='w15'>
          <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" readonly>
        </td>
        <td class='w15'>
          <input id='js-discount' type="number" name="q[discount]" class="js-discount form-control" value="" placeholder='値引額' dusk='discount'>
        </td>
        <td class='w20'>
        <td class='w10 pointer'>
          <i class="fas fa-times fa-3x color-light-black js-remove-row pointer"></i>
        </td>
      </tr>
    `
    $('#quotationTableBody').append(template);
    $("#quotationTableBody").sortable({
      handle: '.handle',
    });
    rowCount++;
  };
})

function addEditRow(val) {
  var productList = []
  window.products.forEach((product) => {
    productList.push(`<option value=${product.id}>${product.name}</option>`);
  });
  if (val.product_id) {
    var product = window.products.find((v) => v.id == val.product_id);
    var options = `<option value='0' selected>商品から選択</option><option value='1'>自由記述</option>`;
    var productSelect = `<select class="form-control js-product-select product-select" name="product-select-${rowCount}">
                          <option selected value="${product.id}">${product.name}</option>
                            ${productList}
                         </select >`;
  } else {
    var product = { 'id': 0, 'name': val.other_product_name ? val.other_product_name : val.name }
    var options = `<option value='1' selected>自由記述</option><option value='0'>商品から選択</option>`;
    var productSelect = `<input type="text" class="js-product-name product-select form-control dusk-product-name-${rowCount}" value=${val.other_product_name ? val.other_product_name : val.name} placeholder="商品名を自由に記述してください">`
  }
  var template = `
    <tr class='js-row' dusk="tr-${rowCount}">
      <td class=''>
        <i class="fas fa-grip-lines handle"></i>
      </td>
      <td class='js-product-td'>
        <div class="form-group">
        ${productSelect}
        </div > 
      </td >
      <td class=''>
        <input type="number" name="number" class="js-product-count product-count form-control dusk-count-${rowCount}" value=${val.num}>
      </td>
      <td class=''>
        <input type="text" name="unit" class="js-unit unit form-control dusk-unit-${rowCount}" value=${val.unit}>
      </td>
      <td class=''>
        <input type="number" name="unit-price" class="js-unit-price unit-price form-control dusk-unit-price-${rowCount}" value=${val.unit_price ? val.unit_price : product.fc_price}>
      </td>
      <td class=''>
        <input type="number" name="price" class="js-price price form-control" value="0" readonly>
      </td>
      <td class=''>
        <select class="form-control js-type type dusk-type-${rowCount}">
          ${options}
        </select>
      </td>
      <td class=''>
        <input type="text" name="memo" class="js-pqmemo memo form-control" maxlength="26" value=${!val.memo ? '' : val.memo}>
      </td>
      <td class='pointer'>
        <i class="fas fa-times fa-2x js-remove-row pointer"></i>
      </td>
    </tr>
  `
  $('#quotationTableBody').append(template);
  $("#quotationTableBody").sortable({
    handle: '.handle',
  });
  rowCount++;
};

function addDiscountRow(val) {
  $('#quotationTableBody').append(
    `
        <tr class='js-discount-row' dusk="tr-${rowCount}">
          <td class=''>
            <i class="fas fa-grip-lines handle"></i>
          </td>
          <td class='w30 js-product-td'>
            <div class="form-group">
              <input type='text' class='form-control' value='お値引き' readonly>
            </div> 
          </td>
          <td class='w12'>
            <input type="number" name="number" class="js-product-count form-control dusk-count-${rowCount}" readonly>
          </td>
          <td class='w10'>
            <input type="text" name="unit" class="js-unit form-control dusk-unit-${rowCount}" readonly>
          </td>
          <td class='w15'>
            <input type="number" name="unit-price" class="js-unit-price form-control dusk-unit-price-${rowCount}" readonly>
          </td>
          <td class='w15'>
            <input id='js-discount' type="number" name="q[discount]" class="js-discount form-control" value="${val}" placeholder='値引額' dusk='discount'>
          </td>
          <td class='w20'>
          <td class='w10 pointer'>
            <i class="fas fa-times fa-2x js-remove-row pointer"></i>
          </td>
        </tr>
        `
  )
}
// バリデーション時戻った際にJSで動的に過去入力を復元
function afterValidateRender() {
  $('.js-row').each(function (i, element) {
    // 履歴が自由記述だった場合
    if (window.pq[i].other_product == 1) {
      $(this).find('.js-product-td').empty();
      $(this).find('.js-product-td').append('<input type="text" class="js-product-name form-control" placeholder="商品名を自由に記述してください"> ');
      $(element).find('.js-product-name').val(window.pq[i].name);
    } else {
      $(element).find('.js-product-select').val(window.pq[i].product_id);
      // $(element).find(`.js-product-select option:[value="${window.pq[i].product_id}"]`).prop('selected', true);
    }

    $(element).find('.js-unit-price').val(window.pq[i].unit_price);
    $(element).find('.js-product-count').val(window.pq[i].num);
    $(element).find('.js-unit').val(window.pq[i].unit);
    $(element).find('.js-type').val(window.pq[i].other_product);
    $(element).find('.js-pqmemo').val(window.pq[i].memo);
    // $(element).find(`.js-type option:[value="${window.pq[i].other_product}"]`).prop('selected', true);
    $(element).find('.js-price').val(window.pq[i].price);
  })
  subTotal();
}

//セレクトボックスが切り替わったら発動
$('#quotationForm').on('change', '.js-account-infomation', function () {
  //選択したvalue値を変数に格納
  var payee = $(this).val();
  //選択したvalue値をp要素に出力
  $('.js-payee').val(payee);
});

// 見積書アップロードのD&D
Dropzone.autoDiscover = false;
window.onload = function () {
  $("#dropzone").dropzone({
    url: '/quotations/ajax/pdf/parse',
    // addRemoveLinks: true,
    acceptedMimeTypes: '.pdf',
    parallelUploads: 1,
    maxFiles: 10,
    maxFilesize: 2048,
    dictDefaultMessage: '見積書以外のファイルはここをクリックか、ドラッグ＆ドロップしてください',
    dictCancelUpload: 'アップロードを中止',
    dictRemoveFile: 'ファイルを削除',
    dictCancelUploadConfirmation: '削除が完了しました',
    dictInvalidFileType: 'PDFファイルのみアップロード可能です',
    init: function () {
      this.on("success", function (file, response) {
        appearLoader('PDFファイルを解析しています・・・', 8000)
        $('.js-row').remove();
        $('.js-discount-row').remove();
        setTimeout(() => {
          remodal.close();
        }, 2000);
        renderParseResult(response)
        // レンダリングを待ってから計算
        setTimeout(() => {
          calcRowPrice();
          // ウェイトカバー外す
        }, 5000);
      })
    }
  });
};

$('#quotationForm').on('keyup', '.js-pqmemo', function(){
  const content = $(this).val();
  console.log(content);
  if(content.length > 14){
    $(".alert-msg").text("備考欄は15文字まででご入力ください");
    $("#alert-warning").fadeIn(600).delay(1400).fadeOut(4000);
  }
});