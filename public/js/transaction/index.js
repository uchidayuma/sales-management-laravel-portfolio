$(document).on('click', '.open', function (e) {
  var transactionId = $(this).closest('tr').find('.f12').attr('id');
  console.log(transactionId);

  submitForm(transactionId);
});

function submitForm(transactionId) {
  $(document).on('click', '#btn-delete', function (e) {
    e.preventDefault();
    var form = $(this).parents('form');
    form.submit();
  });
};

$(".custom-select").change(function(){
  $("#submit_form").submit();
});