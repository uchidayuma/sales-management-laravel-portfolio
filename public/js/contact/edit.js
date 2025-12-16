$(document).ready(function () {

  $(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    locale: "ja",
    allowInput: true
  });
  $(".datetimepicker").flatpickr({
    dateFormat: "Y-m-d H時:i分",
    enableTime: true,
    locale: "ja",
    allowInput: true
  });
});

$(function () {
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $(document).on('change', '.js-file', function (e) {
    var file = e.target.files[0],
      reader = new FileReader(),
      // _preview = $(this).prev().children('img');
      _preview = $(this).prev();
    t = this;

    // $(this).parent().prepend(`<p class="js-filename">${file.name}</p>`)
    $(this).parent().find('.js-upload-description').empty();
    $(this).parent().find('.js-upload-description').text(file.name);
    $(this).parent().find('.js-image-remove').remove();
    $(this).parent().prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>')
  });

  $('body').on('click', '.js-image-remove', function (e) {
    $(this).parent().find('.js-upload-description').empty();
    $(this).parent().find('.js-upload-description').text('クリックするかドラッグ&ドロップでファイルをアップロードできます。');
    $(this).parent().find('.js-file-state').val('deleted');
    // 施工前画像画像の処理
    $(this).parent().find('.js-upload-image').attr("src", "");
    $(this).remove();
  });

  $('body').on('click', '.js-uploader', function (e) {
    e.stopImmediatePropagation();
    $(this).next('input').trigger('click');
  });

  // 施工前画像の処理
  $('body').on('click', '.js-upload-image, .js-upload-description', function (e) {
    e.stopImmediatePropagation();
    $(this).parent().find('.js-image-file').trigger('click');
  });
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $(document).on('change', '.js-image-file', function (e) {
    var _this = $(this);
    imageChange(_this, e);
  });

  // ドラッグオーバー時の動作
  $(document).on('dragover', '.js-uploader, .js-image-uploader', function (e) {
    e.stopPropagation();
    e.preventDefault()
    $(this).css('background', 'rgba(0,0,0,0.2)');
  });
  $(document).on('dragleave', '.js-uploader, .js-image-uploader', function (e) {
    e.stopPropagation();
    e.preventDefault()
    $(this).css('background', '');
  });
  $('body').on('click', '#for-myself', function () {
    $('input[name="c[sample_send_at]"]').val('1970-01-01');
  });
});

function imageChange(_this, e) {
  // 画像ファイル以外の場合は何もしない
  var file = e.target.files[0],
    reader = new FileReader(),
    _preview = _this.prev().children('img');
    _preview = _this.prev();
    t = this;
  if (file.type.indexOf("image") < 0) {
    alert('画像以外は登録できません');
    return false;
  }

  _this.parent().find('.js-upload-description').empty();
  _this.parent().find('.js-image-remove').remove();
  _this.parent().find('.js-upload-image').attr({ src: window.URL.createObjectURL(file)});
  _this.parent().prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>')
}

//案件情報をいったん保存しないと、formが正しく表示されないため アラートを追加
$(function () {
  $('#contact_type').change(function() {
    if (confirm('案件の種別を変更した場合、一度保存する必要があります。\n今の状態で保存してもよろしいでしょうか？')) {
      //OKをクリックすると案件が保存される。
      //Queryでsubmit()するとHTML5のrequiredなどバリデーションが効かなくなるのでここで判定
      if (! $('form')[0].reportValidity()) {
        return false;
      }
      $("#contact-update").submit();
    } else { 
      //キャンセルをクリックした場合何も起きない。
      return false;
    }
  });
});