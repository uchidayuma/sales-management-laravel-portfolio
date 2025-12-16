window.onload = function() { 
  $(".datepicker").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja",
    allowInput: window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
    minDate: dayjs().format('YYYY-MM-DD')
  });
}

$(document).on('click', '.js-add-holiday', function(){
  console.log('add');
  $('.holidays').append(`
    <div class='col-md-3 m-2 d-flex align-items-center'>
      <input data-provide="datepicker" class="form-control datepicker js-date-${window.holidayCount} pointer mr-2" type="datetime" name="t[delivery_at]" value="" dusk='' autocomplete="off" placeholder="${dayjs().format('YYYY-MM-DD')}""/>
    <i class="fas fa-times fa-2x js-delete pointer" id="${window.holidayCount}"></i>
    </div>
  `);
  $(`.js-date-${window.holidayCount}`).flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja",
    allowInput: window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
    minDate: dayjs().format('YYYY-MM-DD')
  });
  window.holidayCount++;
})

$(document).on('change', '.datepicker', function(){
  // 日時決定でAJAX通信
  console.log($(this).val());
  $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      url: `/settings/office_holidays`,
      type: 'POST',
      data: { holiday : $(this).val() }
  })// ajax
  .then( (data) => {
    console.log('success');
    console.log(data);
    commonAlert(data.holiday + 'を追加しました！', 'alert-success', 3500)
  })
  .catch((e) =>{
    console.log(e);
    alert('会社休日カレンダーの追加に失敗しました。通信状況をご確認ください。')
  })
})

$(document).on('click', '.js-delete', function(){
  const holidayId = $(this).attr('id')
  $(this).parent().fadeOut(700).queue(function () {
    console.log(holidayId);
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        url: `/settings/office_holidays`,
        type: 'DELETE',
        data: { id : holidayId }
    })// ajax
    .then( (data) => {
      console.log(data);
      console.log('success');
    })
    .catch((e) =>{
      console.log(e);
      alert('会社休日カレンダーの削除に失敗しました。通信状況をご確認ください。')
    })

    $(this).remove();
  });
});