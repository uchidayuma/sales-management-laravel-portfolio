function getParam(name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$(function () {
  $('input:radio[name="t[prepaid]"]').change(function () {
    const value = $('input:radio[name="t[prepaid]"]:checked').val();
    window.halfPayment = value == 1 ? true : false;
  });
});

function isClothTurfOnly() {
  var isClothTurfOnly = true;

  $(".js-cut-turf-row").each(function () {
    isClothTurfOnly = false;
    return false;
  });

  return isClothTurfOnly;
}

function calcTurfCount() {
  var totalCount = 0
  $(".js-turf-row").each(function (i, element) {
    var productId = $(element).find(".js-turf-select").val();
    var num = parseInt($(element).find(".js-product-count").val());
    totalCount += num;
  });
  console.log('反物の数 ： ' + totalCount);
  return totalCount;
}

function calcTurfCount40mm() {
  var totalCount = 0
  $(".js-turf-row").each(function (i, element) {
    const is40mm = $(element).find(".js-turf-select").find('option:selected').attr('is40mm');
    var productText = $(element).find(".js-turf-select").find('option:selected').text();
    if (is40mm == 1) {
      var num = parseInt($(element).find(".js-product-count").val());
      totalCount += num;
    }
  });
  return totalCount;
}

/*===== 面積関係の計算 ===== */

//反物の面積計算
function calcTurfArea() {
  var totalArea = 0;
  $(".js-turf-row").each(function (i, element) {
    var productId = $(element).find(".js-turf-select").find('option:selected').val();
    const product = window.products.find(v => v.id == productId);
    if (product) {
      var area = Math.round(product.vertical * product.horizontal * 10) / 10;
      area = Math.round(area * 10) / 10 * parseInt($(element).find(".js-product-count").val());
      totalArea += area;
    }
  });
  return totalArea;
}

//40mm反物の面積計算
function calcTurf40mmArea() {
  var totalArea = 0;
  $(".js-turf-row").each(function (i, element) {
    var productText = $(element).find(".js-turf-select").find('option:selected').text();
    console.log($(element).find(".js-turf-select").find('option:selected').attr('is40mm'));
    const is40mm = $(element).find(".js-turf-select").find('option:selected').attr('is40mm');
    if (is40mm == 1) {
      var productId = $(element).find(".js-turf-select").find('option:selected').val();
      const product = window.products.find(v => v.id == productId);
      var area = Math.round(product.vertical * product.horizontal * 10) / 10;
      area = Math.round(area * 10) / 10 * parseInt($(element).find(".js-product-count").val());
      totalArea += area;
    }
  });
  return totalArea;
}

// カット人工芝の面積
function calcCutTotalArea() {
  var totalArea = 0;
  $("#cut-turf-table .js-row:not(.js-row--child, .js-cut-price-row)").each(function (index, element) {
    // 空欄だとNaNになるので、分岐
    const rowId = $(element).closest('tr').find('.js-row-id').val();
    const setRow = $(`.js-parent-id[value="${rowId}"]`).closest(".js-cut-price-row");
    var setCount = setRow.find(".js-cut-set-num").val();
    var area = parseFloat($(element).find(".js-area").val()) * setCount
    area = Math.round(area * 10) / 10;
    if (!isNaN(area) && area != '') {
      totalArea += area;
    }
  });
  return parseFloat(totalArea);
}

// カット人工芝の面積(40mmのみ)
function calcCutTotal40mmArea() {
  var totalArea = 0;
  $("#cut-turf-table .js-row:not(.js-row--child)").each(function (index, element) {
    var productText = $(element).find(".js-product-select").find('option:selected').text();
    console.log($(element).find(".js-turf-select").find('option:selected').attr('is40mm'));
    const is40mm = $(element).find(".js-product-select").find('option:selected').attr('is40mm');
    // if (productText.indexOf('40mm') != -1) {
    if(is40mm == 1){ 
      const rowId = $(element).closest('tr').find('.js-row-id').val();
      const setRow = $(`.js-parent-id[value="${rowId}"]`).closest(".js-cut-price-row");
      var setCount = setRow.find(".js-cut-set-num").val();
      var area = parseFloat($(element).find(".js-area").val()) * setCount
      // 空欄だとNaNになるので、分岐
      area = Math.round(area * 10) / 10;
      if (!isNaN(area) && area != '') {
        totalArea += area;
      }
    }
  });
  return parseFloat(totalArea);
}
// 副資材バラ売りのみかどうか
function isCutSubOnly() {
  var isCutSubOnly = true;
  const notSubOnlyArray = ['js-turf-row', 'js-cut-turf-row', 'js-sales-row', 'js-sub-row', 'js-other-row']
  $(".js-row").each(function (i, element) {
    // 副資材以外のものが含まれているかクラス名をループしてチェック
    notSubOnlyArray.forEach( function(str){
      if ( $(element).attr('class').indexOf(`${str}`) != -1) {
        isCutSubOnly = false;
      }
    });
  });
  return isCutSubOnly;
}

// 納品希望日のチャーター便判断
function isCharterShipping() {
  var state = false
  $(".js-turf-row").each(function (i, element) {
    var productId = $(element).find(".js-turf-select").find('option:selected').val();
    const product = window.products.find(v => v.id == productId);
    if (product) {
      var area = Math.round(product.vertical * product.horizontal * 10) / 10;
      area = Math.round(area * 10) / 10 * parseInt($(element).find(".js-product-count").val());
      if ((!window.isFactoryPickUp && area >= 500 && window.halfPayment) || (window.isFactoryPickUp && area >= 800 && window.halfPayment)) {
        console.log('チャーター')
        state = true;
      }
    }
  });
  return state;
}

// 送料自動計算のチャーター便判断
function isCharterShippingPrice() {
  var state = false
  var area = 0;
  $(".js-turf-row").each(function (i, element) {
    var productId = $(element).find(".js-turf-select").find('option:selected').val();
    const product = window.products.find(v => v.id == productId);
    if (product) {
      var turfArea = Math.round(product.vertical * product.horizontal * 10) / 10;
      area += parseInt(Math.round(turfArea * 10) / 10 * $(element).find(".js-product-count").val());
    }
  });
  $(".js-cut-turf-row").each(function (i, element) {
    if($(element).find(".js-area").val() != ''){
      area += parseInt($(element).find(".js-area").val());
    }
  });

  console.log('area : ' + area);
  if (area >= 500) {
    console.log('チャーター')
    state = true;
  }

  return state;
}

function zeroValidation() {
  var boolean = true;
  //　バリデーション
  $('main').find(".js-product-count:not(.js-cut-length), .js-vertical, .js-horizontal").each(function (i, element) {
    console.log($(element).val());
    if ($(element).val() == 0) {
      console.log('数値が0！！');
      boolean = false;
    }
  });
  return boolean;
}
// 商品がGolfかどうかを判断
function isGolf(productName = null){
  if(productName == null) return false;
  if (productName.indexOf('GOLF') !== -1){
    return true;
  } 
  return false;
}
// 切り売りの場合カットメニューの処理
function addGolfCutMenu(cutItems = null, productName = null, editFlg = null, parseInt = null, productId = null){
  // GOLFの場合以外追加できない商品のIDをconfigsテーブルから取得
  const cutTurfInvisibleIdsArr = window.cutTurfInvisibleIds.split(',').map(Number);

  var productList = [];

  // 発注書編集時に追加するカットメニューの処理
  if(editFlg == true){
    Object.keys(cutItems).forEach(productKey => {
      // 編集時GOLFの商品のカットメニュー追加
      if (isGolf(productName)) {
        // 編集時ゴルフ関連の商品行を追加してカットを追加した場合の処理
        if(productId == null){
          if (cutTurfInvisibleIdsArr.includes(cutItems[productKey].id) && cutItems[productKey].name != 'カット賃') {
            productList.push(
              `<option value=${cutItems[productKey].id}>${cutItems[productKey].name}</option>`
            );
          }
        // 編集時自動的に入力される部分
        } else {
          // 編集時元から入っているデータがGOLF関係時のみ追加できるカットでなかった場合
          if(!cutTurfInvisibleIdsArr.includes(Number(productId))){
            if (!cutTurfInvisibleIdsArr.includes(cutItems[productKey].id) ) {
              productList.push(
                `<option value="${cutItems[productKey].id}" ${cutItems[productKey].id === parseInt ? 'selected' : ''}>${cutItems[productKey].name}</option>`
              );
            }
            // 編集時元から入っているデータがGOLF関係時のみ追加できるカットだった場合
          } else {
            if (cutTurfInvisibleIdsArr.includes(cutItems[productKey].id) && cutItems[productKey].name != 'カット賃') {
              productList.push(
                `<option value="${cutItems[productKey].id}" ${cutItems[productKey].id === parseInt ? 'selected' : ''}>${cutItems[productKey].name}</option>`
              );
            }
          }
        }
      // 編集時GOLF以外の商品だった場合
      } else {
        if (!cutTurfInvisibleIdsArr.includes(cutItems[productKey].id)) {
          productList.push(
            `<option value="${cutItems[productKey].id}" ${cutItems[productKey].id === parseInt ? 'selected' : ''}>${cutItems[productKey].name}</option>`
          );
        }
      }
    });
  // 発注書作成時に追加するカットメニューの処理
  } else if(editFlg == false){
    Object.keys(cutItems).forEach(productKey => {
      // カットする商品名に【GOLF】の文字列が含まれいる場合
      if (isGolf(productName)) {
        if (cutTurfInvisibleIdsArr.includes(cutItems[productKey].id) && cutItems[productKey].name != 'カット賃') {
          productList.push(
            `<option value=${cutItems[productKey].id} ${cutItems[productKey].id === parseInt ? 'selected' : ''}>${cutItems[productKey].name}</option>`
          );
        }
      }else{
          productList.push(
            `<option value=${cutItems[productKey].id} ${cutItems[productKey].id === parseInt ? 'selected' : ''}>${cutItems[productKey].name}</option>`
          );
      }
    });
  } 

  return productList;
} 

function calculateShipping() {
  return new Promise((resolve, reject) => {
    const shippingPrefecture = getPrefectureFromAddress($('textarea[name="t[address]"]').val());
    if(!shippingPrefecture) return reject('shippingPrefecture is null');
    var shippingCost = 0;
    console.log('isCharter : ' + isCharterShipping());
    if(window.isFactoryPickUp){
      return resolve(shippingCost);
    } else if(isCharterShippingPrice()){
// チャーター便オンリーの料金になるように修正
      const shippingPrice = window.shippingPriceTable.find(v => v.name.includes(shippingPrefecture));
      $('#price-wrapper').addClass('d-none').removeClass('d-flex');
      $('#shipping-price').addClass('d-none').val(shippingPrice.charter_shipping_price);
      $('#charter-message').removeClass('d-none');
      return resolve(shippingPrice.charter_shipping_price);
    } else {
      $('#price-wrapper').removeClass('d-none').addClass('d-flex');
      $('#shipping-price').removeClass('d-none');
      $('#charter-message').addClass('d-none');
      console.log('shippingPrefecture : ' + shippingPrefecture);
      const smallSize = smallSizeCount();
      const largeSize = largeSizeCount();
      const extraLargeSize = extraLargeSizeCount();
      console.log('smallSize : ' + smallSize);
      console.log('largeSize : ' + largeSize);
      console.log('extraLargeSize : ' + extraLargeSize);
      // 送料計算
      const smallSizePrice = calcSmallSizePrice(shippingPrefecture, smallSize);
      console.log('smallSizePrice : ' + smallSizePrice);
      const largeSizePrice = calcLargeSizePrice(shippingPrefecture, largeSize);
      console.log('largeSizePrice : ' + largeSizePrice);
      const extraLargeSizePrice = calcExtraLargeSizePrice(shippingPrefecture, extraLargeSize);
      console.log('extraLargeSizePrice : ' + extraLargeSizePrice);

      // サイズ別に箱数を計算→送料を計算
      return resolve(smallSizePrice + largeSizePrice + extraLargeSizePrice);
    }
  });

}

function smallSizeCount(){
  const shippingIncludedIds = [25, 26, 40];
  // count product_type_id = 2. shipping_weight sums in #sub-body
  var sumWeight = 0;
  // 同梱不可の箱数カウント
  var sumBoxCount = 0;
  // まとめ売りの計算
  $("#sub-body .js-row").each(function (i, element) {
    if(!shippingIncludedIds.includes(parseInt($(element).find(".js-product-select").val()))){
    // 同梱OK製品の計算
      if ($(element).find(".js-shipping-size").val() == "1" && $(element).find(".js-shipping-include").val() == "1") {
        sumWeight += parseFloat($(element).find(".js-shipping-weight").val() * $(element).find(".js-product-count").val());
        console.log('sumWeight : ' + sumWeight);
      }else if($(element).find(".js-shipping-size").val() == "1" && $(element).find(".js-shipping-include").val() == "0") {
      // 同梱不可製品の計算 同梱不可なので、商品ごとに何箱になるか計算
        const weight = parseFloat($(element).find(".js-shipping-weight").val() * $(element).find(".js-product-count").val());
        sumBoxCount += Math.ceil(weight / 20);
      }
    }
  })
  $("#cut-sub-body .js-row").each(function (i, element) {
    // 副資材バラ売り商品の重量および、箱の数の計算
    if(!window.freeShippingItemIds.includes(parseInt($(element).find(".js-product-select").val())) && !shippingIncludedIds.includes(parseInt($(element).find(".js-product-select").val()))){
      // 同梱OK製品の計算
      if ($(element).find(".js-shipping-size").val() == "1" ) {
        const weight = parseFloat($(element).find(".js-shipping-cut-weight").val() * $(element).find(".js-product-count").val());
        sumWeight += Math.round(weight * 1000) / 1000;
        console.log('副資材バラ売り : ' + $(element).find('option:selected').text() + ' : ' +  $(element).find(".js-shipping-cut-weight").val());
        console.log('sumWeight : ' + sumWeight);
      }
    }
  })
  $("#sales-body .js-row").each(function (i, element) {
  // freeShippingItemIdsに含まれている商品は送料無料かをチェック
    if(!window.freeShippingItemIds.includes(parseInt($(element).find(".js-product-select").val())) && !shippingIncludedIds.includes(parseInt($(element).find(".js-product-select").val()))){
      // 同梱OK製品の計算
      if($(element).find(".js-shipping-size").val() == "1" && $(element).find(".js-shipping-include").val() == "1") {
        console.log('同梱OK販促商品の重量 : ' + $(element).find(".js-shipping-weight").val());
        sumWeight += parseFloat($(element).find(".js-shipping-weight").val() * $(element).find(".js-product-count").val());
        console.log('sumWeight : ' + sumWeight);
      }else if($(element).find(".js-shipping-size").val() == "1" && $(element).find(".js-shipping-include").val() == "0") {
      // 同梱不可製品の計算 同梱不可なので、商品ごとに何箱になるか計算
        const weight = parseFloat($(element).find(".js-shipping-weight").val() * $(element).find(".js-product-count").val());
        sumBoxCount += Math.ceil(weight / 20);
      }
    }
  })
  // return small size count
  console.log('smallSizeCount : ' + Math.ceil(sumWeight / 20));

  return Math.ceil(sumWeight / 20) + sumBoxCount;
}

function largeSizeCount(){
  //Find out the length of cut artificial turf within 4.5m.
  var sumBoxCount = 0;
  $("#cut-turf-table .js-cut-turf-row").each(function (i, element) {
    // pick up vertical < 4.5m or vertical * horizontal < 10m2
    const parentId = $(element).find('.js-row-id').val();
    const cutSetNum = $('.js-cut-price-row').find(`.js-parent-id[value="${parentId}"]`).parent().children('.js-cut-set-num').val();
    console.log('cut-set-count : ' + cutSetNum);
    // 横幅2m かつ 長さ 4.5m以上なら問答無用で特大サイズ
    if ( ($(element).find(".js-horizontal").val() <= 2 && $(element).find(".js-vertical").val() < 4.5) 
      || ($(element).find(".js-horizontal").val() < 2 && $(element).find(".js-vertical").val() * $(element).find(".js-horizontal").val() < 10)
      // || ($(element).find(".js-horizontal").val() == 2 && $(element).find(".js-vertical").val() < 4.5)
     ) {
      sumBoxCount += parseInt(cutSetNum);
    }
  })
  // ターポリンシート1.8m幅と防草シート類は大型サイズとしてカウント
  // 防草シート類は同梱可→商品ごとに重量を算出して箱数を計算
  const largeSubItemsId = [11,16,17,18];
  var sumWeights = {};
  largeSubItemsId.forEach(id => {
    sumWeights[id] = 0;
  });
  console.log(sumWeights);
  $("#sub-body .js-row").each(function (i, element) {
    console.log('大型サイズの商品ID : ' + $(element).find(".js-product-select").val());
    if(!window.freeShippingItemIds.includes(parseInt($(element).find(".js-product-select").val()))){
      if(largeSubItemsId.includes(parseInt($(element).find(".js-product-select").val()))){
        // 同梱可かどうか
        if($(element).find(".js-shipping-include").val() == "1"){
          console.log(parseInt($(element).find(".js-product-count").val()));
          console.log($(element).find(".js-shipping-weight").val());
          console.log(parseFloat($(element).find(".js-shipping-weight").val() / 2 * $(element).find(".js-product-count").val()));
          // 防草シートは同商品なら2つまで大サイズに入るので、2で割る
          sumWeights[parseInt($(element).find(".js-product-select").val())] += parseFloat($(element).find(".js-shipping-weight").val() / 2 * $(element).find(".js-product-count").val());
        }else{
          sumBoxCount += parseInt($(element).find(".js-product-count").val());
        }
      }
    }
  })
  // 切り売りターポリンシート1.8m幅と防草シート類は大型サイズとして1梱包重量で割って計算
  $("#cut-sub-body .js-row").each(function (i, element) {
    console.log('大型サイズの商品ID : ' + $(element).find(".js-product-select").val());
    if(!window.freeShippingItemIds.includes(parseInt($(element).find(".js-product-select").val()))){
      if(largeSubItemsId.includes(parseInt($(element).find(".js-product-select").val()))){
        // 同梱可かどうか
        console.log(parseInt($(element).find(".js-product-count").val()));
        console.log($(element).find(".js-shipping-cut-weight").val());
        console.log(parseFloat($(element).find(".js-shipping-cut-weight").val() * $(element).find(".js-product-count").val()));
        sumWeights[parseInt($(element).find(".js-product-select").val())] += parseFloat($(element).find(".js-shipping-cut-weight").val() * $(element).find(".js-product-count").val());
      }
    }
  })
  console.log(sumWeights);
  // 防草シートとターポリンシートの箱数を別々に計算
  var limitWeight = 40;
  for (var key in sumWeights) {
    if (sumWeights.hasOwnProperty(key)) { // これはプロトタイプチェーンからのプロパティをフィルタリングするためのものです
        console.log("Key:", key, "Value:", sumWeights[key]);
        // switch (key) {
        //   case 11:
        //     limitWeight = 20;
        //     break;
        //   default:
        //     limitWeight = 40;
        //     break;
        // }
        sumBoxCount += Math.ceil(sumWeights[key] / 20);
    }
  }

  console.log('largeSizeCount : ' + sumBoxCount);

  return sumBoxCount;
}

function extraLargeSizeCount(){
  // Artificial turf 2 x 4.5m or more, or 10m2 or more or countertop
  var sumBoxCount = 0;
  $("#turf-table .js-row").each(function (i, element) {
    if(!window.freeShippingItemIds.includes(parseInt($(element).find(".js-product-select").val()))){
      sumBoxCount = sumBoxCount + parseInt($(element).find(".js-product-count").val());
    }
  })
  $("#cut-turf-table .js-row").each(function (i, element) {
      // pick up vertical > 4.5m or vertical * horizontal > 10m2 横幅が2mがmust条件
    if ( ($(element).find(".js-horizontal").val() == 2 && $(element).find(".js-vertical").val() >= 4.5) || $(element).find(".js-vertical").val() * $(element).find(".js-horizontal").val() >= 10) {
      const parentId = $(element).find('.js-row-id').val();
      const cutSetNum = $('.js-cut-price-row').find(`.js-parent-id[value="${parentId}"]`).parent().children('.js-cut-set-num').val();
    console.log('cut-set-count : ' + cutSetNum);
      sumBoxCount += parseInt(cutSetNum);
    }
  })

  return sumBoxCount
}

function calcSmallSizePrice(shippingPrefecture = '', smallSizeCount = 1){
  // find shipping cost in window.shippingPriceTable to shippingPrefecture
  const shippingPrice = window.shippingPriceTable.find(v => v.name.includes(shippingPrefecture));
  console.log('shippingPrice : ' + shippingPrice);

  return shippingPrice.small_shipping_price * smallSizeCount;
}

function calcLargeSizePrice(shippingPrefecture = '', largeSizeCount = 1){
  // find shipping cost in window.shippingPriceTable to shippingPrefecture
  const shippingPrice = window.shippingPriceTable.find(v => v.name.includes(shippingPrefecture));

  return shippingPrice.large_shipping_price * largeSizeCount;
}

function calcExtraLargeSizePrice(shippingPrefecture = '', extraLargeSizeCount = 1){
  // find shipping cost in window.shippingPriceTable to shippingPrefecture
  const shippingPrice = window.shippingPriceTable.find(v => v.name.includes(shippingPrefecture));
  // 同じ種類の人工芝が複数ある場合は、extra_large_shipping_price2 3位上ならextra_large_shipping_price3を適用する
  let shippingUnitPrice = 0;
  switch(extraLargeSizeCount){
    case 1:
      shippingUnitPrice = shippingPrice.extra_large_shipping_price;
      break;
    case 2:
      shippingUnitPrice = shippingPrice.extra_large_shipping_price2;
      break;
    default: // 3以上
      shippingUnitPrice = shippingPrice.extra_large_shipping_price3;
      break;
  }

  return shippingUnitPrice * extraLargeSizeCount;
}