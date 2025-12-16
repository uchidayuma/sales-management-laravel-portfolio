Dropzone.autoDiscover = false;
window.onload = function () {
  $("#dropzone").dropzone({
    url: "/quotations/file/ajax/upload",
    addRemoveLinks: true,
    dictDefaultMessage: '見積書以外のファイルはここをクリックか、ドラッグ＆ドロップしてください',
    dictCancelUpload: 'アップロードを中止',
    dictRemoveFile: 'ファイルを削除',
    dictCancelUploadConfirmation: '削除が完了しました',
    init: function () {
      myDropzone = this;
      //Restore initial message when queue has been completed
      this.on("removedfile", function (e) {
        var contactId = $('body').find('.js-contact-id-dropzone').val();
        console.log(e.name);
        console.log(contactId);
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '/quotations/file/ajax/upload',
          type: 'POST',
          data: {
            'id': contactId,
            'filename': e.name,
            '_method': 'DELETE'
          } // DELETE リクエストだよ！と教えてあげる。
        }) // ajax
      }); //this
    }
  });
};

$(document).on('click', '.js-quotations', function () {
  var contactId = $(this).attr('contactId')
  $('.js-contact-id').text(contactId)
  $('.js-contact-id').val(contactId)
  $('.js-quotations-body').append('<p class="text-center d-flex justify-content-center w100"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i></p>')
  $.get(`/quotations/ajax/${contactId}`,
    function (res) {
      $('.js-quotations-body').empty();
      if (res.length > 0) {
        console.log(res);
        res.forEach(elem => {
          $('.js-quotations-body').append(`
            <div class="form-check col-md-6 mb40">
              <p class="quotation-total f11 mb10" for="radio{{ $key }}">見積書No.${elem.id}： ${new Intl.NumberFormat('ja-JP').format(elem.total)}円</p>
              <a class='btn btn-info' href="/quotations/${elem.id}" target='blank'>見積もり書を確認</a>
            </div>
          `);
        });
      } else {
        $('.js-quotations-body').append('<p class="f13 bold">この案件には見積書がありません</p>');
      }
    })
})

$('.js-submit-modal-open').on('click', function (e) {
  const remodal = $('#remodal').remodal();
  var contactId = $(this).attr('contactId')
  $('.js-contact-id').text(contactId)
  $('.js-contact-id').val(contactId)
  $('.js-contact-id-dropzone').val(contactId)
  $('.js-email').val($(this).attr('email'))
  if (!$(this).attr('email')) {
    alert('顧客のメールアドレスが設定されていません。案件編集から見積書を送信するメールアドレスを設定してください。')
    $('#dispatch').text('案件編集から顧客のメールアドレスを設定してください')
    $('#dispatch').addClass('disabled');
    $('#dispatch').prop('disabled', true);
  }
  var customerName = $(this).parent().parent().find('.js-name').text()

  $('.js-name').val(customerName)
  remodal.open()
  $('.js-dispatch-body').append('<p class="text-center d-flex justify-content-center w100 mb40"><i class="fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i></p>')
  $.get(`/quotations/ajax/${contactId}`,
    function (res) {
        console.log(res);
      $('.js-dispatch-body').empty();
      if (res.length > 0) {
        res.forEach(elem => {
          $('.js-dispatch-body').append(`
            <div class="form-check col-md-6 mb40">
              <input class="form-check-input" type="checkbox" name='q[]' value="${elem.id}" id="radio${elem.id}">
              <label class="form-check-label f11 mb10" for="radio${elem.id}">見積書No.${elem.id}： ${new Intl.NumberFormat('ja-JP', { style: 'currency', currency: 'JPY' }).format(elem.sub_total * 1.1)}円</label>
              <a class='btn btn-info' href="/quotations/${elem.id}" target='blank'>見積もり書を確認</a>
            </div>
          `);
          $('#dispatch').prop('disabled', false);
        });
      } else {
        $('.js-dispatch-body').append('<p class="f13 bold">この案件には見積書がありません</p>');
        $('#dispatch').append('<p class="f13 bold">この案件には見積書がありません</p>');
      }
    })
});
