$(document).on('click', '.open-modal', function (e) {
  var areaId = $(this).attr('id');
  const selectedArea = window.fcApplyAreas.find(v => v.id == areaId );
  console.log(areaId, selectedArea);
  $('#area-name').val(selectedArea.name);
  $('#area-content').text(selectedArea.content);
  $('#update-form').attr('action', `/settings/fc-apply-areas-update/${areaId}`)
});

$(document).on('click', '.delete-btn', function (e) {
  if (!confirm('エリアを削除してもよろしいですか？')) {
    /* キャンセルの時の処理 */
    return false;
  }
});

$(function () {
  $('[data-toggle="popover"]').popover()
})

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