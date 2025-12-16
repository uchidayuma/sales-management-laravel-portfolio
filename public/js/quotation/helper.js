function renderParseResult(res) {
    var discount = 0;
    console.log(res);
    res.forEach((rowArray, i, currentArray) => {
        // もし配列の中にproductsテーブルの商品名が入っていれば商品行として展開
        if (isIncludeProduct(rowArray)) {
            // 商品名がnull かつ、 単位・単価が埋まっている行があれば、商品名だけ代入
            if (existPriceOnlyRow()) {
                for (let i = 0; i < $('.js-row').length; i++) {
                    const element = $('.js-row')[i];
                    const name = $(element).find('.js-product-name').val();
                    const count = $(element).find('.js-product-count').val();
                    const unit = $(element).find('.js-unit').val();
                    const unitPrice = $(element).find('.js-unit-price').val();
                    if (!name && (count || unit || unitPrice)) {
                        $(element).find('.js-product-name').val(rowArray[0]);
                        break;
                    }
                }
            } else {
                addFreeRow(rowArray[0], rowArray[2], removeUnitAndComma(rowArray[1]), removeUnitAndComma(rowArray[3]));
            }
        } else if (isIncludeDoublePrice(rowArray)) {
            // 単位・単価がnull かつ、 商品名が埋まっている行があれば、け代入
            if (existProductNameOnlyRow()) {
                for (let i = 0; i < $('.js-row').length; i++) {
                    const element = $('.js-row')[i];
                    const name = $(element).find('.js-product-name').val();
                    const count = $(element).find('.js-product-count').val();
                    const unit = $(element).find('.js-unit').val();
                    const unitPrice = $(element).find('.js-unit-price').val();
                    if (name && !count && !unit && !unitPrice) {
                        console.log('あった！');
                        console.log(rowArray[1], rowArray[2], rowArray[3],);
                        $(element).find('.js-unit').val(rowArray[1]);
                        $(element).find('.js-product-count').val(rowArray[2]);
                        $(element).find('.js-unit-price').val(rowArray[3]);
                        break;
                    }
                }
            } else {
                // もし金額系の数値が2つ以上あればレンダリング
                addFreeRow(rowArray[0], rowArray[2], removeUnitAndComma(rowArray[1]), removeUnitAndComma(rowArray[3]));
            }
        } else if (isPriceArray(rowArray)) {
            // 上からチェックして空いているinputタグがあれば挿入
            addFreeRow(null, rowArray[0], removeUnitAndComma(rowArray[1]), removeUnitAndComma(rowArray[2]));
        } else if (isDiscount(rowArray)) {
            discount += replacePriceToInteger(rowArray[2] ? rowArray[2] : rowArray[1]);
        }
    });
    addDiscountRow(discount);
}

function isIncludeProduct(targetArray) {
    var isInclude = false
    window.allProducts.forEach(product => {
        if(!Array.isArray(targetArray)){
            Object.values(targetArray).map(function (val, key, array ) {
                if (upperCaseToLowerCase(val).includes(product.name) || val.includes('サンプル芝')) {
                    isInclude = true;
                }
            })
        }else{
            targetArray.forEach((target, i, rowArray) => {
                if (upperCaseToLowerCase(target).includes(product.name) || target.includes('サンプル芝')) {
                    isInclude = true;
                }
            })
        }
    })
    return isInclude;
}

function isIncludeDoublePrice(targetArray) {
    var isPrice = 0;
    if(!Array.isArray(targetArray)){
        Object.values(targetArray).map(function (val, key, array ) {
            if (val.includes('￥') || val.includes('¥')) {
                isPrice++;
            }
        })
    }else{
        targetArray.forEach((target, i, rowArray) => {
            if (target.includes('￥') || target.includes('¥')) {
                isPrice++;
            }
        });
    }
    return isPrice < 2 ? false : true;
}

function isDiscount(targetArray) {
    var isDiscountRow = false
    const discountWordList = ['お値引き', '値引き', '担当者値引き', '割引', '割引き', '%オフ', '％オフ', '%off', '%OFF', '％オフ', '％OFF', '端数処理', '端数']
    if(!Array.isArray(targetArray)){
        console.log('Not Array');
        Object.values(targetArray).map(function (val, key, array ) {
            if (discountWordList.includes(val)) {
                isDiscountRow = true;
            } else if (val.includes('%オフ') || val.includes('％オフ') || val.includes('値引')) {
                isDiscountRow = true;
            }
        })
    }else{
        targetArray.forEach((target, i, rowArray) => {
            if (discountWordList.includes(target)) {
                isDiscountRow = true;
            } else if (target.includes('%オフ') || target.includes('％オフ') || target.includes('値引')) {
                isDiscountRow = true;
            }
        });
    }
    return isDiscountRow;
}

// 単価や金額の塊かどうか
function isPriceArray(targetArray) {
    var returnResult = false;
    const unitWordList = ['m²', 'm2', '㎡', 'm２', '平米',];
    if(!Array.isArray(targetArray)){
        Object.values(targetArray).map(function (val, key, array ) {
            if (unitWordList.includes(val)) {
                console.log('価格の配列');
                returnResult = true;
            }
        })
    }else{
        targetArray.forEach((target, i, rowArray) => {
            if (unitWordList.includes(target)) {
                console.log('価格の配列');
                returnResult = true;
            }
        });
    }
    return returnResult;
    
}

function existPriceOnlyRow() {
    var exist = false;
    $('.js-row').each(function (index, element) {
        const name = $(element).find('.js-product-name').val();
        const count = $(element).find('.js-product-count').val();
        const unit = $(element).find('.js-unit').val();
        const unitPrice = $(element).find('.js-unit-price').val();
        if (!name && (count || unit || unitPrice)) {
            exist = true;
        }
    })
    return exist;
}

function existProductNameOnlyRow() {
    var exist = false;
    $('.js-row').each(function (index, element) {
        const name = $(element).find('.js-product-name').val();
        const count = $(element).find('.js-product-count').val();
        const unit = $(element).find('.js-unit').val();
        const unitPrice = $(element).find('.js-unit-price').val();
        if (name && !count && !unit && !unitPrice) {
            exist = true;
        }
    })
    return exist;
}

function calcRowPrice() {
    $('.js-row').each(function (index, val) {
        var count = $(this).closest('.js-row').find('.js-product-count').val();
        count = parseFloat(count);

        var unitPrice = $(this).closest('.js-row').find('.js-unit-price').val();
        unitPrice = Number(unitPrice.replace(/[^0-9]/g, ''));

        $(this).closest('.js-row').find('.js-price').val(Math.round(count * unitPrice));
    })
}
