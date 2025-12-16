$('.modal-open-btn').on('click', function (e) {
  var contactId = $(this).closest('tr').find('.js-contact-id').text();
  console.log(contactId);

  $('#contact-id').val(contactId);
  $('#modal-customer').text($(this).closest('tr').find('.js-name').text());
})
$(function () {
  $(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    static: true,
    locale: "ja",
    allowInput: true
  });
});