$('.modal-open-btn').on('click', function (e) {
  $('#numbers').empty();
  $('#numbers2').empty();
  $('#numbers3').empty();
  $('.second').addClass('d-none');
  $('.third').addClass('d-none');
  var data = window.dispatches.find((v) => v.transaction_id == $(this).attr('transaction-id'));
  console.log(data);
  if(data.id){
    $('.modal-title').removeClass('d-none');
    $('.modal-title-only').addClass('d-none');
    $('#modal-no').text(data.contact_id);
  }else{
    $('.modal-title').addClass('d-none');
    $('.modal-title-only').removeClass('d-none');
    $('#t-id').text(data.transaction_id);
  }
  // 案件紐付きなしの場合は値の入れ替え
  if (data.contact_id === null) {
    data.shipping_id = data.transaction_only_shipping_id;
    data.shipping_number = data.transaction_only_shipping_number;
    data.shipping_date = data.transaction_only_shipping_date;
  }

  var shipping_date_format = formatDate(new Date(data.shipping_date), 'YYYY年MM月DD日');

  $('#modal-no').text($(this).attr('contact-no'));
  $('#shipping-date').text(shipping_date_format);
  $('#shipping-number').val(data.shipping_number);
  $('#transport-company').text(data.transport_company);

  var contactUrl
  if (data.shipping_id === 1) {
    // 西濃運輸 かつ 複数追跡番号
    if (data.shipping_number.indexOf(',') != -1) {
      var number = data.shipping_number;
      var array = number.split(',');
      var url = '';
      $.each(array, function (i, element) {
        url += `${ i === 0 ? '?' : '&'}GNPNO${i + 1}=${element}`;
        $('#numbers').append(`<input type="text" class="form-control mb10" value=${element} id=${'shipping-number-' + i} readonly>`);
      });
      contactUrl = data.trakking_url + url
      // 西濃運輸 かつ 単数追跡番号
    } else {
      $('#numbers').append(`<input type="text" class="form-control mb10" value=${data.shipping_number} readonly>`);
      contactUrl = data.trakking_url + '?GNPNO1=' + data.shipping_number
    }
  } else {
    // 西濃運輸以外 かつ 複数追跡番号
    if (data.shipping_number.indexOf(',') != -1) {
      var array = data.shipping_number.split(',');
      var url = '';
      $.each(array, function (i, element) {
        $('#numbers').append(`<input type="number" class="form-control mb10" value=${element} id='shipping-number' readonly>`);
      });
      contactUrl = data.trakking_url + data.shipping_number
      // 西濃運輸以外 かつ 単数追跡番号
    } else {
      $('#numbers').append(`<input type="number" class="form-control mb10" value=${data.shipping_number} id='shipping-number' readonly>`);
      contactUrl = data.trakking_url + data.shipping_number
    }
  }
  $('#js-trans-link').attr('href', contactUrl);
  // 1個目ここまで

  // ===== 分納対応2個目の荷物 ====
  if(data.shipping_date2){
    $('.second').removeClass('d-none');
    $('.modal-heading1').removeClass('d-none');
    var shipping_date_format = formatDate(new Date(data.shipping_date2), 'YYYY年MM月DD日');

    $('#shipping-date2').text(shipping_date_format);
    $('#shipping-number2').val(data.shipping_number2);
    $('#transport-company2').text(returnTransportCompany(data.shipping_id2));

    var contactUrl2
    if (data.shipping_id2 === 1) {
      // 西濃運輸 かつ 複数追跡番号
      if (data.shipping_number2.indexOf(',') != -1) {
        var array = data.shipping_number2.split(',');
        var url = '';
        $.each(array, function (i, element) {
          url += `${ i === 0 ? '?' : '&'}GNPNO${i + 1}=${element}`;
          $('#numbers2').append(`<input type="text" class="form-control mb10" value=${element} id=${'shipping-number2-' + i} readonly>`);
        });
        contactUrl2 = data.trakking_url2 + url
        // 西濃運輸 かつ 単数追跡番号
      } else {
        $('#numbers2').append(`<input type="text" class="form-control mb10" value=${data.shipping_number2} readonly>`);
        contactUrl2 = data.trakking_url2 + '?GNPNO1=' + data.shipping_number2
      }
    } else {
      // 西濃運輸以外 かつ 複数追跡番号
      if (data.shipping_number2.indexOf(',') != -1) {
        var array = data.shipping_number2.split(',');
        var url = '';
        $.each(array, function (i, element) {
          $('#numbers2').append(`<input type="text" class="form-control mb10" value=${element} id='shipping-numbers2' readonly>`);
        });
        contactUrl2 = data.trakking_url2 + data.shipping_number2
        // 西濃運輸以外 かつ 単数追跡番号
      } else {
        $('#numbers2').append(`<input type="text" class="form-control mb10" value=${data.shipping_number2} id='shipping-numbers2' readonly>`);
        contactUrl2 = data.trakking_url2 + data.shipping_number2
      }
    }
    $('#js-trans-link2').attr('href', contactUrl2);
  }

  // ========== 分納対応3個目の荷物 ==========
  if(data.shipping_date3){
    $('.third').removeClass('d-none');
    var shipping_date_format = formatDate(new Date(data.shipping_date3), 'YYYY年MM月DD日');

    $('#shipping-date3').text(shipping_date_format);
    $('#shipping-number3').val(data.shipping_number3);
    $('#transport-company3').text(returnTransportCompany(data.shipping_id3));

    var contactUrl3
    if (data.shipping_id3 === 1) {
      // 西濃運輸 かつ 複数追跡番号
      if (data.shipping_number3.indexOf(',') != -1) {
        var array = data.shipping_number3.split(',');
        var url = '';
        $.each(array, function (i, element) {
          url += `${ i === 0 ? '?' : '&'}GNPNO${i + 1}=${element}`;
          $('#numbers3').append(`<input type="text" class="form-control mb10" value=${element} id=${'shipping-number3-' + i} readonly>`);
        });
        contactUrl3 = data.trakking_url3 + url
        // 西濃運輸 かつ 単数追跡番号
      } else {
        $('#numbers3').append(`<input type="text" class="form-control mb10" value=${data.shipping_number3} readonly>`);
        contactUrl3 = data.trakking_url3 + '?GNPNO1=' + data.shipping_number3
      }
    } else {
      // 西濃運輸以外 かつ 複数追跡番号
      if (data.shipping_number3.indexOf(',') != -1) {
        var array = data.shipping_number3.split(',');
        var url = '';
        $.each(array, function (i, element) {
          $('#numbers3').append(`<input type="text" class="form-control mb10" value=${element} id='shipping-numbers3' readonly>`);
        });
        contactUrl3 = data.trakking_url3 + data.shipping_number3
        // 西濃運輸以外 かつ 単数追跡番号
      } else {
        $('#numbers2').append(`<input type="text" class="form-control mb10" value=${data.shipping_number3} id='shipping-numbers3' readonly>`);
        contactUrl3 = data.trakking_url3 + data.shipping_number3
      }
    }
    $('#js-trans-link3').attr('href', contactUrl3);
  }//分納3つ目ここまで
});

/**
 * 日付をフォーマットする
 * @param  {Date}   date     日付
 * @param  {String} [format] フォーマット
 * @return {String}          フォーマット済み日付
 */
var formatDate = function (date, format) {
  if (!format) format = 'YYYY-MM-DD hh:mm:ss.SSS';
  format = format.replace(/YYYY/g, date.getFullYear());
  format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
  format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
  format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
  format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
  format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
  if (format.match(/S/g)) {
    var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
    var length = format.match(/S/g).length;
    for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
  }
  return format;
};

function returnTransportCompany(id)
{
  var name = '';
  switch (id) {
    case 1:
      name = '西濃運輸' 
      break;
    case 2:
      name = 'ヤマト運輸' 
      break;
    case 3:
      name = '佐川急便' 
      break;
    case 4:
      name = '日本郵政' 
      break;
    case 5:
      name = 'チャーター便' 
      break;
    default:
      name = '西濃運輸' 
      break;
  }
  return name
}