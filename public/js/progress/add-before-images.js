$(document).ready(function () {
  //現場報告できる担当案件がなければ登録ボタンを無効にする
  var selected = $("#noOptions").val();
  if (selected == 0) {
    $('#register').attr("disabled", true);
  }

});

$('.form-check-input').on('change', function () {
  $('.form-check-input').parent().removeClass('active');
  if (this.checked) {
    $(this).parent().addClass('active');
  }
})