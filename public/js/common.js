function appearLoader(message = 'ロード中です。', timeout = 10000) {
    $('body').append(`<aside class="loader"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i><p class="loader__message">${message}</p></aside>`)
    setTimeout(() => {
        $('.loader').remove();
    }, timeout);
}

// 便利な関数たち

function commonAlert(message = 'ありがとうございます！', color='alert-success', timeout = 2000) {
    $('body').append(`<div class="alert common-alert ${color}">${message}</div>`)
    setTimeout(() => {
        $('.common-alert').remove();
    }, timeout);
}

function upperCaseToLowerCase(str) {
    return str.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function (s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
}

function removeUnitAndComma(string) {
    if (isString(string)) {
        return string.replace(/￥/g, "").replace(/¥/g, "").replace(/,/g, "").replace(/、/g, "");
    } else {
        return string;
    }
}

function isString(obj) {
    return typeof (obj) == "string" || obj instanceof String;
};

function dripCity(str = '') {
  let city = str.split('市', 1);
  city = str.split('町', 1);
  city = str.split('村', 1);

  return city;
}

function replacePriceToInteger(value) {
    if (isString(value)) {
        var returnValue = value.replace(/￥/g, "").replace(/¥/g, "").replace(/-/g, "").replace(/ー/g, "").replace(/,/g, "").replace(/、/g, "");
        return parseInt(returnValue);
    } else {
        return value;
    }
}

// value = 丸めたい数字
// digit = 四捨五入したい桁数（少数○位） → digit = 2ならば少数第2位を四捨五入
function roundFloatNumber( value = 0, digit = 2){
  const base = digit === 1 ? 1 : Math.pow(10, digit);
  return Math.round(value * base) / base ;
}


// npm moduleのobject-digをコピーして作成した
// https://github.com/joe-re/object-dig
function dig(target, ...keys){
    let digged = target;
    for (const key of keys) {
        if (typeof digged === 'undefined' || digged === null) {
        return undefined;
        }
        if (typeof key === 'function') {
        digged = key(digged);
        } else {
        digged = digged[key];
        }
    };
    return digged;
};

// 日付から曜日を取得
// 入力日付 出力数値（0〜6曜日）
function dateTodayOfWeek(date='2021-08-08'){
    return dayjs(date).day();
}

// クエリパラメータを取得する
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// addressから都道府県名を取得する
function getPrefectureFromAddress(address = '') {
    let noPostalCode = address.replace(/〒\d{3}[-]\d{4}\s/, "");
    noPostalCode = noPostalCode.replace(/〒\d{7}\s/, "");
    noPostalCode = noPostalCode.trim();

    console.log(noPostalCode);
    const prefectures = [
      "北海道", "青森県", "岩手県", "宮城県", "秋田県",
      "山形県", "福島県", "茨城県", "栃木県", "群馬県",
      "埼玉県", "千葉県", "東京都", "神奈川県", "新潟県",
      "富山県", "石川県", "福井県", "山梨県", "長野県",
      "岐阜県", "静岡県", "愛知県", "三重県", "滋賀県",
      "京都府", "大阪府", "兵庫県", "奈良県", "和歌山県",
      "鳥取県", "島根県", "岡山県", "広島県", "山口県",
      "徳島県", "香川県", "愛媛県", "高知県", "福岡県",
      "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県",
      "鹿児島県", "沖縄県"
    ];
  
    for (let i = 0; i < prefectures.length; i++) {
      if (noPostalCode.startsWith(prefectures[i])) {
        return prefectures[i];
      }
    }
    
    return null; // マッチする都道府県名が見つからなかった場合
  }
