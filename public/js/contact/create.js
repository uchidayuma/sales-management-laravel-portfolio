$(document).ready(function () {

  $(".datepicker").flatpickr({
    dateFormat: "Y-m-d",
    locale: "ja",
    allowInput: true
  });
  $(".datetimepicker").flatpickr({
    dateFormat: "Y-m-d H",
    locale: "ja",
    allowInput: true
  });

  $('form').hide();
  $(`.contact-form[contact-type-id=0]`).show();

  $("#contactType").change(function () {
    const selectedContactType = $('input:radio[name="contactType"]:checked').val();
    console.log(selectedContactType)

    if (selectedContactType == 2 || selectedContactType == 6) {
      $("#quoteDetails").prop('disabled', false);
    } else {
      $("#quoteDetails").prop('disabled', true);
    }
  });
});

$(window).on('load', function () {
  // コピーを作成される場合の処理部分
  if (window.copyData !== null) {
    // フォームのセレクト部分を自動入力
    const contactTypeId = window.copyData['contact_type_id'];
    $(`input:radio[name=contactType]:eq(${contactTypeId})`).prop('checked', true);

    // データを自動入力する前にフォームを更新(早く更新しすぎるとフォームが更新されないので少し待機)
    setTimeout(function () {
      $('.contact-form').hide();
      $(`.contact-form[contact-type-id=${contactTypeId}]`).show();
    }, 1000);

    // フォームに自動入力
    if (copyData.contact_type_id <= 4) {
      // 個人の顧客の場合の処理
      $('.js-copy-surname').val(copyData.surname);
      $('.js-copy-name').val(copyData.name);
      $('.js-copy-surname_ruby').val(copyData.surname_ruby);
      $('.js-copy-name_ruby').val(copyData.name_ruby);
      $('.js-copy-zipcode').val(copyData.zipcode);
      $('.js-copy-pref').val(copyData.pref);
      $('.js-copy-city').val(copyData.city);
      $('.js-copy-street').val(copyData.street);
      $('.js-copy-tel').val(copyData.tel);
      $('.js-copy-tel2').val(copyData.tel2);
      $('.js-copy-fax').val(copyData.fax);
      $('.js-copy-email').val(copyData.email);
      $('.js-copy-memo').val(copyData.memo);
    } else {
      // 法人の顧客の場合の処理
      // 法人の会社名がある場合は入力する
      $('.js-copy-company_name').val(copyData.company_name !== null ? copyData.company_name : "");
      // 法人のかいしゃめいがある場合は入力する
      $('.js-copy-company_ruby').val(copyData.company_ruby !== null ? copyData.company_ruby : "");
      // 法人の場合は担当者名部分に姓名をくっつけて入力する
      $('.js-copy-surname').val(copyData.surname + (copyData.name !== null ? copyData.name : ""));
      // 法人の場合は担当者名部分にセイメイをくっつけて入力する
      $('.js-copy-surname_ruby').val(copyData.surname_ruby + (copyData.name_ruby !== null ? copyData.name_ruby : ""));
      $('.js-copy-zipcode').val(copyData.zipcode);
      $('.js-copy-pref').val(copyData.pref);
      $('.js-copy-city').val(copyData.city);
      $('.js-copy-street').val(copyData.street);
      $('.js-copy-tel').val(copyData.tel);
      $('.js-copy-tel2').val(copyData.tel2);
      $('.js-copy-fax').val(copyData.fax);
      $('.js-copy-email').val(copyData.email);
      $('.js-copy-memo').val(copyData.memo);
    }
  }
});
// 個々の部分の処理でもcontactTypeにより
$('#contactType').on('change', function () {
  console.log('change');
  $('.contact-form').hide();
  var contactTypeId = $('input:radio[name="contactType"]:checked').val();
  $(`.contact-form[contact-type-id=${contactTypeId}]`).show();

  // 案件種別を変更した際にデータの入る場所を変更
  if (copyData != null) {
    if (contactTypeId <= 4) {
      // 個人の顧客の場合の処理
      $('.js-copy-surname').val(copyData.surname);
      $('.js-copy-name').val(copyData.name);
      $('.js-copy-surname_ruby').val(copyData.surname_ruby);
      $('.js-copy-name_ruby').val(copyData.name_ruby);
      $('.js-copy-zipcode').val(copyData.zipcode);
      $('.js-copy-pref').val(copyData.pref);
      $('.js-copy-city').val(copyData.city);
      $('.js-copy-street').val(copyData.street);
      $('.js-copy-tel').val(copyData.tel);
      $('.js-copy-tel2').val(copyData.tel2);
      $('.js-copy-fax').val(copyData.fax);
      $('.js-copy-email').val(copyData.email);
      $('.js-copy-memo').val(copyData.memo);

    } else {
      // 法人の顧客の場合の処理
      // 法人の会社名がある場合は入力する
      $('.js-copy-company_name').val(copyData.company_name !== null ? copyData.company_name : "");
      // 法人のかいしゃめいがある場合は入力する
      $('.js-copy-company_ruby').val(copyData.company_ruby !== null ? copyData.company_ruby : "");
      // 法人の場合は担当者名部分に姓名をくっつけて入れる
      $('.js-copy-surname').val(copyData.surname + (copyData.name !== null ? copyData.name : ""));
      // 法人の場合は担当者名部分にセイメイをくっつけて入れる
      $('.js-copy-surname_ruby').val(copyData.surname_ruby + (copyData.name_ruby !== null ? copyData.name_ruby : ""));
      $('.js-copy-zipcode').val(copyData.zipcode);
      $('.js-copy-pref').val(copyData.pref);
      $('.js-copy-city').val(copyData.city);
      $('.js-copy-street').val(copyData.street);
      $('.js-copy-tel').val(copyData.tel);
      $('.js-copy-tel2').val(copyData.tel2);
      $('.js-copy-fax').val(copyData.fax);
      $('.js-copy-email').val(copyData.email);
      $('.js-copy-memo').val(copyData.memo);
    }
  }
});

//同期処理

$('.js-copy-surname').on('change', function(){
  $('.js-copy-surname').val($(this).val());
})
$('.js-copy-name').on('change', function(){
  $('.js-copy-name').val($(this).val());
})
$('.js-copy-surname_ruby').on('change', function(){
  $('.js-copy-surname_ruby').val($(this).val());
})
$('.js-copy-name_ruby').on('change', function(){
  $('.js-copy-name_ruby').val($(this).val());
})
$('.js-copy-zipcode').on('keyup', function(){
  if($(this).val().length > 6) {
    $('.js-copy-zipcode').val($(this).val());
    setTimeout(() => {
      const pref = $(this).closest('tbody').find('.js-copy-pref').val();
      const city = $(this).closest('tbody').find('.js-copy-city').val();
      $('.js-copy-pref').val(pref);
      $('.js-copy-city').val(city);
    }, 1000);
  }
})
$('.js-copy-pref').on('change', function(){
  $('.js-copy-pref').val($(this).val());
})
$('.js-copy-city').on('change', function(){
  $('.js-copy-city').val($(this).val());
})
$('.js-copy-street').on('change', function(){
  $('.js-copy-street').val($(this).val());
})
$('.js-copy-tel').on('change', function(){
  $('.js-copy-tel').val($(this).val());
})
$('.js-copy-tel2').on('change', function(){
  $('.js-copy-tel2').val($(this).val());
})
$('.js-copy-fax').on('change', function(){
  $('.js-copy-fax').val($(this).val());
})
$('.js-copy-email').on('change', function(){
  $('.js-copy-email').val($(this).val());
})
$('.js-copy-memo').on('change', function(){
  $('.js-copy-memo').val($(this).val());
})
$('.js-copy-campany_name').on('change', function(){
  $('.js-copy-campany_name').val($(this).val());
})
$('.js-copy-campany_ruby').on('change', function(){
  $('.js-copy-campany_ruby').val($(this).val());
})
$('.js-copy-industory').on('change', function(){
  $('.js-copy-industory').val($(this).val());
})
$('.js-copy-etc').on('change', function(){
  $('.js-copy-etc').val($(this).val());
})


/*
  // ファイルアップロード関係
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $(document).on('change', '.js-file', function (e) {
    var file = e.target.files[0],
      reader = new FileReader(),
      // _preview = $(this).prev().children('img');
      _preview = $(this).prev();
    t = this;

    // $(this).parent().prepend(`<p class="js-filename">${file.name}</p>`)
    console.log($(this).parent().find('.js-upload-description'));
    console.log(file.name);
    $(this).parent().find('.js-upload-description').remove();
    $(this).parent().prepend(`<p class='js-upload-description uploader__description'>${file.name}</p>`);
    $(this).parent().find('.js-image-remove').remove();
    $(this).parent().prepend('<p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>')
  });

  $(document).on('click', '.js-image-remove', function (e) {
    alert('remogbe')
    $(this).parent().find('.js-upload-description').remove();
    $(this).parent().find('.js-upload-description').empty();
    $(this).parent().prepend(`<p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>`);
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
  */