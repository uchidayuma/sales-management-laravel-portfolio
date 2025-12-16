$(function () {
  $(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    static: true,
    locale: "ja",
    allowInput: true
  });
  $('[data-toggle="popover"]').popover()
  // 
  if(window.transactions[0].shipping_date2 != null){
    window.inputState = 2;
  }
  if(window.transactions[0].shipping_date3 != null){
    window.inputState = 3;
  }
  if(window.transactions[0].delivery_at2 != null || window.transactions[0].shipping_date2 != null){
    window.inputState = 2;
    $('#input-cost2').removeClass('d-none');
  }
  if(window.transactions[0].delivery_at3 != null || window.transactions[0].shipping_date3 != null){
    window.inputState = 3;
    $('#input-cost3').removeClass('d-none');
  }
  console.log(window.inputState);
  // 追加ボタン 第3納品希望日の入力があるか、3つ目の発送情報連絡ボタンがあれば出さない
  if(window.inputState < 3 || window.transactions[0].delivery_at3 == null){
    $('#add-input').removeClass('d-none');
  }
});

// 第2納品希望日以降があれば実行↓
$('.open-modal').on('click', function () {
  // if(window.transactions[0].delivery_at2 == null && window.inputState < 2) { return false; }
  // 一度リセット
  $('.shipping-cost-group').addClass('d-none');
  $('input[name="shipping_date"]').val('');
  $('input[name="shipping_number"]').val('');
  // $(`select[name="shipping_id"] option`).prop('selected', false);
  $('textarea[name="dispatch_message"]').val('');
  // $(`#select[name="shipping_id"] option`).attr('selected', false);
  const deliveryNumber = $(this).attr('delivery-number');
  $('input[name="number"]').val(deliveryNumber);
  if(window.transactions[0].delivery_at2 != null){
    $('#shipping-number').text(`（第${deliveryNumber}納品希望日）`);
  }
  // もし編集ならデフォルト値を設定する
  switch (deliveryNumber) {
    case '1':
      $('.shipping-cost-group').removeClass('d-none');
      if(window.transactions[0].transaction_only_shipping_date){
        $('input[name="shipping_date"]').val(dayjs(window.transactions[0].transaction_only_shipping_date).format('YYYY-MM-DD'));
        $('input[name="shipping_number"]').val(window.transactions[0].transaction_only_shipping_number);
        $(`select[name="shipping_id"] option[value="${window.transactions[0].transaction_only_shipping_id}]"`).prop('selected', true);
        $('textarea[name="dispatch_message"]').val(window.transactions[0].dispatch_message);
      } 
      break;
    case '2':
      $('.shipping-cost-group').addClass('d-none');
      if(window.transactions[0].shipping_date2){
        $('input[name="shipping_date"]').val(dayjs(window.transactions[0].shipping_date2).format('YYYY-MM-DD'));
        $('input[name="shipping_number"]').val(window.transactions[0].shipping_number2);
        $(`select[name="shipping_id"] option[value="${window.transactions[0].shipping_id2}"]`).prop('selected', true);
        $('textarea[name="dispatch_message"]').val(window.transactions[0].dispatch_message2);
      } 
      break;
    case '3':
      $('.shipping-cost-group').addClass('d-none');
      if(window.transactions[0].shipping_date3){
        $('input[name="shipping_date"]').val(dayjs(window.transactions[0].shipping_date3).format('YYYY-MM-DD'));
        $('input[name="shipping_number"]').val(window.transactions[0].shipping_number3);
        $(`select[name="shipping_id"] option[value="${window.transactions[0].shipping_id3}"]`).prop('selected', true);
        $('textarea[name="dispatch_message"]').val(window.transactions[0].dispatch_message3);
      } 
      break;
    default:
      $('.shipping-cost-group').removeClass('d-none');
      if(window.transactions[0].transaction_only_shipping_date){
        $('input[name="shipping_date"]').val(dayjs(window.transactions[0].transaction_only_shipping_date).format('YYYY-MM-DD'));
        $('input[name="shipping_number"]').val(window.transactions[0].transaction_only_shipping_number);
        $(`select[name="shipping_id"] option[value=${window.transactions[0].transaction_only_shipping_id}]`).prop('selected', true);
        $('textarea[name="dispatch_message"]').val(window.transactions[0].dispatch_message);
      } 
      break;
  }
})

// 追加ボタンの動き
$('#add-input').on('click', function(){
  if (!confirm('発送情報を追加してよろしいですか？\n発送情報を追加すると自動的に納品希望日が追加されます。\n納品希望日が追加されると全ての発送情報入力が終わるまで請書にはならないため、ご注意ください。')) {
    /* キャンセルの時の処理 */
    return false;
  } else {
    window.inputState++;
    if(window.inputState === 2){
      $('#input-cost2').removeClass('d-none');
      $('#input-cost1').text('発送1の送料と追跡番号を入力');
      $('#edit-cost1').text('発送1の送料と追跡番号を修正');
      const nextDay = dayjs(window.transactions[0].delivery_at).add(1, 'day').format('YYYY-MM-DD');
      ajaxAddDelivery(window.transactions[0].transaction_id, 'delivery_at2', nextDay);
    }else if(window.inputState === 3){
      $('#input-cost3').removeClass('d-none');
      $(this).addClass('d-none');
      var nextDay = '';
      if(window.transactions[0].delivery_at2){
        nextDay = dayjs(window.transactions[0].delivery_at2).add(1, 'day').format('YYYY-MM-DD');
      }else{
        // 連続で追加ボタンを押したときはこちら
        nextDay = dayjs(window.transactions[0].delivery_at).add(2, 'day').format('YYYY-MM-DD');
      }
      ajaxAddDelivery(window.transactions[0].transaction_id, 'delivery_at3', nextDay);
    }
  }
})

function ajaxAddDelivery(transactionId = null, column = null, deliveryAt = null){
  $.ajax({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: `/transactions/ajax/update`,
      type: 'POST',
      data: {
        id : transactionId,
        column: column,
        value : deliveryAt
      }
  })
  .then( function (res) {
    console.log(res);
  }).catch(function(error){
    console.trace(error);
  });
}