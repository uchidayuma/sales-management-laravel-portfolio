$('.stock-input').on('change', function () {
  $(this).next().removeClass('disabled');
})
$('.stock-update').on('click', function () {
  const stock = $(this).prev().val();
  const id = $(this).attr('id');
  console.log({ "id": id, "stock": stock });

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "/products/ajax/update",
    type: 'POST',
    dataType: "json",
    data: { "id": id, "stock": stock },
  })
    // Ajaxリクエストが成功した時発動
    .done((response) => {
      $('.alert-msg').text('在庫を' + response.stock + '個に更新しました。');
      $('#alert-info').fadeIn(400).delay(2000).fadeOut(2000);
    })
    // Ajaxリクエストが失敗した時発動
    .fail((response) => {
      alert('通信に失敗しました。時間を空けてお試し下さい。')
    })
  $(this).next().addClass('disabled');
})