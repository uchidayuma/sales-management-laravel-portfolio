window.onload = function () {
  // 1回だけAJAX_GET
  getNotificate();
  // getArticleUnread();
  setInterval("getNotificate()", 10800);
};

function getNotificate() {
  if ($('.info-circle-contents__item').length < 3) {
    $.get("/notifications/unread/ajaxGet",
      function (res) {
        console.log(res);
        if (res.length > 0) {
          $('.js-info-circle').append(`<span class="header-right__circle-notification js-info-circle-notification">${res.length}</span>`);
          res.forEach(element => {
            $('.js-info-circle-contents').prepend(`<a href="${element.url}" class="info-circle-contents__item" notificate_id="${element.id}">案件No:${element.contact_id} ${element.name}</a>`);
          });
          $('.js-no-notification').hide();
        }
      })
  }
}

$('.js-info-circle').on('click', function () {
  $('.js-info-circle-contents').toggle('slide', '', 600);
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $.post("/notifications/ajaxRead",
    function (res) {
      console.log(res);
      if (res.status) {
        $('.js-info-circle-notification').remove();
      } else {
        // alert('インターネット通信がありません。通信環境の良い場所でアクセスし直してください。');
      }
    })

})

function getArticleUnread() {
  $.get("/articles/unread/ajaxGet",
    function (res) {
      if (res.length > 0) {
        $('.js-info-circle').append(`<span class="header-right__circle-notification js-info-circle-notification">${res.length}</span>`);
      } else {
        $('.js-no-notification').hide();
      }
    })
}