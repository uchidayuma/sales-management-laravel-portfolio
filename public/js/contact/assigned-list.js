// flatpkr初期化
$(function () {
  $(".datepicker").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja"
  });
});

$('#js-appoint-modal').on('click', function () {
  const remodal = $('#remodal').remodal();
  const id = $(this).parent().parent().children('.js-contact-id').val();
  $('#contact-id').val(id);
  const name = $(this).parent().parent().children('.js-name').text();
  $('#name').text(name);
  remodal.open();
});

$('.modal-open-btn').on('click', function(e){
  var contactId = $(this).closest('tr').find('.js-contact-id').val();
  $('#contact-id').val(contactId);
  $(".js-skip-btn").on("click", function(e){
    event.preventDefault();

    let form = $(this).parents('form');
    if($(this).data("action")){
      form.attr("action", $(this).data("action"));
    }
    form.submit();
  });
});

