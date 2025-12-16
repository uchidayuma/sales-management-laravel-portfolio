window.onload = function () {
  //select2の初期化
  $('.select-fc').select2({
    placeholder: "FCを選択",
    templateResult: formatState,
  });
  $('.select-fc').val(window.fc).trigger('change');

  $('.select-type').select2({
    placeholder: "お問い合わせ種別を選択",
  });
  $('.select-type').val(window.type).trigger('change');

  $('.select-prefectures').select2({
    placeholder: "都道府県を選択",
  });
  $('.select-prefectures').val(window.prefectures).trigger('change');

  $(".datepicker").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja"
  });

  //flatpickrの初期化
  $(".select-created-at").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    defaultDate: window.createdAt,
    allowInput: true,
    locale: "ja",
    mode: "range"
  });

  $(".select-sent-at").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    defaultDate: window.sentAt,
    allowInput: true,
    locale: "ja",
    mode: "range"
  });

  //初期化が終わったらformを表示する
  $("#filtering-form").css("visibility", "visible");
}

function formatState(state) {
  var classLabel = $(state.element).attr('isremove') == 1 ? 'inactive' : 'active';
  var deadIcon = $(state.element).attr('isremove') == 1 ? '<i class="fas fa-skull-crossbones"></i>' : '';
  var $state = $(
    `<span class=${classLabel}>${deadIcon}` + state.text + '</span>'
  );
  return $state;
};

$("#clearCreatedAt").click(function () {
  $(".select-created-at").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    defaultDate: window.createdAt,
    allowInput: true,
    locale: "ja",
    mode: "range"
  }).clear();
});
$("#clearSentAt").click(function () {
  $(".select-sent-at").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    defaultDate: window.sentAt,
    allowInput: true,
    locale: "ja",
    mode: "range"
  }).clear();
});

//アラート
//失注ボタンをクリックしたときのイベント
$('#failure').click(function () {
  if (!confirm('本当に失注に設定してよろしいですか？')) {
    return false;
  }
});