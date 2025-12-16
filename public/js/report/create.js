$(document).ready(function () {
  $(".datepicker").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja",
    // dusk用にtrue
    allowInput: true
  });

  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $(document).on("change", ".js-file", function (e) {
    var file = e.target.files[0],
      reader = new FileReader(),
      _preview = $(this).prev();
    t = this;

    // 画像ファイル以外の場合は何もしない
    if (file.type.indexOf("image") < 0) {
      alert('画像以外は登録できません');
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
        // .previewの領域の中にロードした画像を表示するimageタグを追加
      };
    })(file);

    reader.readAsDataURL(file);
    //画像が登録された際にp要素とlabel要素を非表示にする部分
    $(this).parent().find("p,label").addClass("d-none");
    $(this).parent().prepend('<p class="js-image-remove image-remove position-absolute"><i class="fas fa-times fa-3x color-black"></i></p>');
  });

  // デフォルトで写真が表示される場合はコメントアウトオフ
  // $(".js-uploader").each(function (index, element) {
  //   if ($(element).find("img").attr("src").length > 2) {
  //     $(element).find("img").show();
  //     $(this).parent().find("p,label").addClass("d-none");
  //     $(element).prepend('<p class="js-image-remove image-remove position-absolute"><i class="fas fa-times color-black"></i></p>');
  //   }
  // });
});

$(document).on('click', '.js-image-remove', function (e) {
  $(this).parent().find('img').fadeOut(500).queue(function () {
    $(this).parent().find('input').before('<img src="">')
    $(this).parent().find('.js-file').val('');
    $(this).parent().css('backround', 'none');
    // $(this).remove();
    $(this).parent().find("p,label").removeClass("d-none");
    $(this).parent().parent().find('.js-uploader').css("background", "");
  });
  $(this).remove();
});

// ドラッグオーバー時の動作
$(document).on("dragover", ".js-uploader", function (e) {
  e.stopPropagation();
  e.preventDefault();
  $(this).css("background", "rgba(0,0,0,0.2)")
});
$(document).on("dragleave", ".js-uploader", function (e) {
  e.stopPropagation();
  e.preventDefault();
  $(this).css("background", "");
});
$(document).on("click", ".js-uploader", function (e) {
  e.stopImmediatePropagation();
  $(this)
    .next("input")
    .trigger("click");
});

$(document).on("drop", ".js-uploader", function (e) {
  $(this).css("background", "");
});
