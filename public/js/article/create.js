$(document).ready(function () {
  $('#trumbowyg').trumbowyg({
    lang: 'ja',
    imageWidthModalEdit: true,
    btnsDef: {
      // Create a new dropdown
      image: {
        dropdown: ['insertImage', 'upload'],
        ico: 'insertImage'
      }
    },
    // Redefine the button pane
    btns: [
      ['viewHTML'],
      ['formatting'],
      ['strong', 'em', 'del'],
      ['superscript', 'subscript'],
      ['link'],
      ['image'], // Our fresh created dropdown
      ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
      ['unorderedList', 'orderedList'],
      ['horizontalRule'],
      ['removeformat'],
      ['fullscreen']
    ],
    plugins: {
      // Add imagur parameters to upload plugin for demo purposes
      upload: {
        serverPath: '/articles/image/ajaxupload',
        fileFieldName: 'image',
        headers: {
          'Authorization': 'Client-ID xxxxxxxxxxxx',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // urlPropertyNameはコントローラーから返す画像のURLのキー
        // https://alex-d.github.io/Trumbowyg/documentation/plugins/#plugin-upload
        urlPropertyName: 'url',
      }
    }
  });

  $(".datepicker").flatpickr({
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ja"
  });
});

// 日時どちらかに変化があった時に発火
$('.datepicker,#publish_time').on('change', function (event) {
  // alert('change');
  var now = new Date();
  var inputDate = new Date($('.datepicker').val());
  inputDate.setHours($('#publish_time').val());

  if (now.getTime() > inputDate.getTime()) {
    $('#js-submit').val('今すぐ投稿');
    $('#js-submit').addClass('btn-danger');
    $('#js-submit').removeClass('btn-primary');
  } else {
    $('#js-submit').val('予約投稿');
    $('#js-submit').removeClass('btn-danger');
    $('#js-submit').addClass('btn-primary');
  }
});

// フォーム送信時
$(document).on('click', '#js-submit', function (event) {
  event.preventDefault();
  $('#status').val('1');
  $('#js-article-form').submit();
});
$(document).on('click', '#draft-submit', function (event) {
  event.preventDefault();
  $('#status').val('0');
  $('#js-article-form').submit();
});

// $('#submit').on('click', function () {
//   // 日本語入力時のバグ対策
//   $('.trumbowyg-viewHTML-button').trigger('mousedown');
// });
