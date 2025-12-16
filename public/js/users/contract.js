$(document).ready(function () {
  // 過去のものは操作不可
  // 行ごとに日付をチェック
  $.each($('.js-fc'), function(i, v){
    const contractDate = dayjs().format('YYYY') + '-' + $(v).find('.js-contract-date').attr('contract-date');
    if(dayjs().isSame(window.queryParams.year, 'year')){
      if(dayjs().isSame(contractDate, 'day') || dayjs().isAfter(contractDate, 'day')){
        $(v).find('.js-check').prop('disabled', true).addClass('disabled');
        $(v).find('.js-check-label').addClass('disabled');
      }
    }
  });
  // もし昨年なら全部disabled
  const requestYear = dayjs(window.queryParams.year + '-' + window.queryParams.month + '-01');
    if(dayjs().isAfter(requestYear, 'year')){
      $('.js-check').prop('disabled', true).addClass('disabled');
      $('.js-check-label').addClass('disabled');
    }
})

$('#month').on('change', function(){
  $('#search-form').submit();
})

$('body').on('change', '.js-check', function (e) {
  $(this).prop('disabled', true);
  const id = $(this).attr('send-id');
  $.ajax({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: `/users/ajax/samefc/checkupdate/${id}`,
      type: 'POST',
      data: {
        id : id,
      }
  })
  .then( function (res) {
    $('.js-check').each(function(){
      if($(this).attr('send-id') == res){
        $(this).prop('disabled', false);
      }
    })

  }).catch(function(error){
    console.trace(error);
    // alert('メール送信ステータスを更新できませんでした。時間を置いて実行するか、開発者にご連絡ください。')
  });
});

$('body').on('click', '.js-samearea', function (e) {
  const userId = $(this).attr('user-id');
  const year = $(this).attr('year');
  console.log(userId, year);
  $('#same-fc-tbody').empty();
  $('.modal-body').prepend('<aside class="loader d-flex justify-content-center"><i class="center fa fa-spinner fa-spin fa-5x" aria-hidden="true"></i></aside>')
  $.ajax({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: `/users/ajax/samefc/status/${userId}/${year}`,
      type: 'GET',
      data: {
        id : userId,
        year: year
      }
  })
  .then( function (res) {
    console.log(res);
    res.forEach((r, index) => {
      console.log(`${index}: ${r.result1}`);
      console.log(`${index}: ${r.result2}`);
      $('#same-fc-tbody').append(`
        <tr>
          <th><a href="/users/${r.userid}" target="blank">${r.name}</a></th>
          <td class="text-center">${r.result1 ? '○' : '☓'}</td>
          <td class="text-center">${r.result2 ? '○' : '☓'}</td>
        </tr>
      `) 
    });
    $('.loader').remove();
    $('#result-table').removeClass('d-none');
    // res.each()
  }).catch(function(error){
    console.trace(error);
    alert('同エリアの達成状況を取得できませんでした。時間を置いて実行するか、開発者にご連絡ください。')
  });
});