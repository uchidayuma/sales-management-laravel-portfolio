$('input[name="fc[seal]"]').on('change', function () {
  $('input[name="sealchange"]').val('change');
});
//担当者
$('#add-staff2').on('click', function () {
  $('.staff2').show().removeAttr('hidden');
});
$('#add-staff3').on('click', function () {
  $('.staff3').show().removeAttr('hidden');
});
$('.js-staff-hide').on('click', function () {
  if (window.confirm('本当に担当者を削除してよろしいですか？')) {
    $(this).closest('tr').hide();
    $(this).closest('tr').prev().find('button').removeAttr('hidden');
    $(this).closest('tr').find('input').each(function (index, element) {
      $(element).val('');
    })
  } else {
    window.alert('削除を取りやめました'); // 警告ダイアログを表示
  }
})
//口座情報
$('#add-account-infomation2').on('click', function () {
  $('.infomation2').show().removeAttr('hidden');
});
$('#add-account-infomation3').on('click', function () {
  $('.infomation3').show().removeAttr('hidden');
});
$('.js-infomation-hide').on('click', function () {
  if (window.confirm('本当に口座情報を削除してよろしいですか？')) {
    $(this).closest('tr').hide();
    $(this).closest('tr').prev().find('button').removeAttr('hidden');
    $(this).closest('tr').find('textarea').each(function (index, element) {
      $(element).val('');
      $(element).text('');
    })
  } else {
    window.alert('削除を取りやめました'); // 警告ダイアログを表示
  }
})

$(function () {
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $(document).on('change', '.js-file', function (e) {
    var file = e.target.files[0],
      reader = new FileReader(),
      _preview = $('.js-preview');
    t = this;
    // 画像ファイル以外の場合は何もしない
    if (file.type.indexOf("image") < 0) {
      return false;
    }

    // ファイル読み込みが完了した際のイベント登録
    reader.onload = (function (file) {
      return function (e) {
        //既存のプレビューを削除{}
        _preview.empty();
        // .prevewの領域の中にロードした画像を表示するimageタグを追加
        _preview.attr({ src: e.target.result, title: file.name });
        _preview.show();
        // .prevewの領域の中にロードした画像を表示するimageタグを追加
      };
    })(file);

    reader.readAsDataURL(file);

    // $(this).parent().prepend(`<p class="js-filename">${file.name}</p>`)
    $(this).parent().find('.js-upload-description').empty();
    $(this).parent().find('.js-upload-description').text(file.name);
    $(this).parent().find('.js-image-remove').removeClass('d-none');
    // $(this).parent().prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>')
  });
  $(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    locale: "ja",
    defaultDate: window.fc ? window.fc.contract_date : null,
    allowInput: window.appEnv === 'local' || window.appEnv === 'localhost' || window.appEnv === 'circleci' ? true : false,
  });
});

$('body').on('click', '.js-image-remove', function (e) {
  console.log($(this));
  $(this).parent().find('.js-preview').attr('src', '');
  $(this).parent().find('.js-upload-description').empty();
  $(this).parent().find('.js-upload-description').text('クリックするかドラッグ&ドロップでファイルをアップロードできます。');
  $(this).parent().find('.js-seal-state').val('deleted');
  $(this).addClass('d-none');
});

// ドラッグオーバー時の動作
$(document).on('dragover', '.js-uploader', function (e) {
  e.stopPropagation();
  e.preventDefault()
  $(this).css('background', 'rgba(0,0,0,0.2)');
});
$(document).on('dragleave', '.js-uploader', function (e) {
  e.stopPropagation();
  e.preventDefault()
  $(this).css('background', '');
});

$('body').on('click', '.js-uploader', function (e) {
  e.stopImmediatePropagation();
  $(this).find('.js-file').trigger('click');
});

$(document).on('input', 'input[name="fc[qualified_business_number]"]', function() {
  var value = $(this).val();
  if(value.length === 13){
    var convertedValue = value.replace(/[０-９]/g, function(s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
    $(this).val(convertedValue);
  }
});
