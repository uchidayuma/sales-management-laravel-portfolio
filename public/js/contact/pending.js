$(document).on('click', '.open', function (e) {
  var contactId = $(this).closest('tr').find('.js-contact-id').attr('id');
  submitForm(contactId);
});

function submitForm(contactId) {
  $(document).on('click', '.pending-submit', function (e) {
    e.preventDefault();
    var form = $(this).parents('form');
    var type = $(this).attr('type');
    console.log(type);
    $(this).parent().children('input[name="type"]').val(type);
    $id = $(this).parent().children('input[name="id"]').val(contactId);
    form.submit();
  });
};

// 削除の確認
$('.js-remove-transaction').on('click', function(event){
  event.preventDefault();
  if (window.confirm('本当に削除してよろしいですか？')) {
    $(this).parent().submit();
  } else {
    window.alert('削除を取りやめました'); // 警告ダイアログを表示
  }
})