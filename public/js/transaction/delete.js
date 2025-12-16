$('.delete-btn').on('click', function () {
  var transactionId = $(this).closest('tr').find('.js-transaction-id').attr('id');
  $('#delete-id').val(transactionId);
  if (window.confirm('本当に発注書No.' + transactionId + 'を削除してよろしいですか？')) {
    $('#delete-form').submit();
  } else {
    window.alert('削除を取りやめました'); // 警告ダイアログを表示
  }
})