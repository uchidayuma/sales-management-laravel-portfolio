$(function () {
    //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
    $(document).on('change', '.js-file, .js-image-file', function (e) {
      var file = e.target.files[0],
        reader = new FileReader(),
        // _preview = $(this).prev().children('img');
        _preview = $(this).prev();
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
      $(this).parent().prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white"></i></p>')
    });
    $('.js-uploader').each(function (index, element) {
      if ($(element).find('img').attr('src').length > 2) {
        $(element).find('img').show();
        $(element).prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white"></i></p>')
      }
  
    });
  });
  
  $('body').on('click', '.js-image-remove', function (e) {
    $(this).parent().find('img').fadeOut(500).queue(function () {
      $(this).parent().find('input').before('<img src="">')
      $(this).remove();
    });
    $(this).parent().find('.js-image-state').val('deleted');
    $(this).remove();
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
    $(this).next('input').trigger('click');
  });
  